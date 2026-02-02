<?php
/**
 * Document Text Extraction Service
 * Extracts text from various document formats (PDF, Word, Excel, text files)
 */

require_once __DIR__ . '/config.php';

// Load Composer autoloader so smalot/pdfparser, phpoffice/phpword, phpoffice/phpspreadsheet are available
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

/**
 * Extract text from a document file
 * @param string $filePath Full path to the file
 * @param string $mimeType MIME type of the file
 * @param string $originalName Original filename (used to guess type when MIME is generic)
 * @return array ['success' => bool, 'text' => string, 'error' => string]
 */
function extractDocumentText($filePath, $mimeType, $originalName = '') {
    if (!file_exists($filePath)) {
        return ['success' => false, 'error' => 'File not found'];
    }
    
    // When MIME is generic (e.g. application/octet-stream), guess from extension
    $ext = $originalName ? strtolower(pathinfo($originalName, PATHINFO_EXTENSION)) : '';
    if (empty($mimeType) || $mimeType === 'application/octet-stream') {
        $mimeMap = [
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc' => 'application/msword',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        ];
        if (isset($mimeMap[$ext])) {
            $mimeType = $mimeMap[$ext];
        }
    }
    
    try {
        switch ($mimeType) {
            case 'application/pdf':
                return extractPdfText($filePath);
            
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': // .docx
            case 'application/msword': // .doc
                return extractWordText($filePath);
            
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': // .xlsx
            case 'application/vnd.ms-excel': // .xls
                return extractExcelText($filePath);
            
            case 'text/plain':
            case 'text/csv':
                return extractTextFile($filePath);
            
            case 'image/jpeg':
            case 'image/png':
                // Optional: OCR support if Tesseract is available
                return extractImageText($filePath);
            
            default:
                return ['success' => false, 'error' => 'Unsupported file type: ' . ($mimeType ?: 'unknown') . '. Supported: PDF, Word (.doc/.docx), Excel (.xls/.xlsx), text, CSV, or images (requires Tesseract for OCR).'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Extraction error: ' . $e->getMessage()];
    }
}

/**
 * Extract text from PDF file
 */
function extractPdfText($filePath) {
    // Try using smalot/pdfparser if available
    if (class_exists('Smalot\PdfParser\Parser')) {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
            return ['success' => true, 'text' => trim($text)];
        } catch (Exception $e) {
            // Fall through to basic extraction
        }
    }
    
    // Basic PDF text extraction using pdftotext command (if available)
    if (function_exists('shell_exec') && !empty(shell_exec('which pdftotext'))) {
        $output = shell_exec('pdftotext "' . escapeshellarg($filePath) . '" - 2>&1');
        if ($output !== null) {
            return ['success' => true, 'text' => trim($output)];
        }
    }
    
    // Fallback: Return error suggesting library installation
    return [
        'success' => false,
        'error' => 'PDF extraction requires smalot/pdfparser library or pdftotext command. Install via: composer require smalot/pdfparser'
    ];
}

/**
 * Extract a Word table as HTML so it can be displayed as a table in the job description.
 */
function extractWordTableAsHtml($table) {
    $html = '<table class="border border-gray-300 border-collapse w-full my-3 text-sm">';
    foreach ($table->getRows() as $row) {
        $html .= '<tr>';
        foreach ($row->getCells() as $cell) {
            $cellText = '';
            if (method_exists($cell, 'getElements')) {
                $elements = is_array($cell->getElements()) ? $cell->getElements() : iterator_to_array($cell->getElements(), false);
                $count = count($elements);
                foreach ($elements as $idx => $child) {
                    $cellText .= extractWordElementText($child);
                    $isTextOrList = $child instanceof \PhpOffice\PhpWord\Element\TextRun || $child instanceof \PhpOffice\PhpWord\Element\ListItemRun;
                    $isPara = $child instanceof \PhpOffice\PhpWord\Element\Paragraph;
                    $hasNext = $idx + 1 < $count;
                    $nextIsTextOrList = $hasNext && (
                        $elements[$idx + 1] instanceof \PhpOffice\PhpWord\Element\TextRun
                        || $elements[$idx + 1] instanceof \PhpOffice\PhpWord\Element\ListItemRun
                    );
                    if ($hasNext && ($isPara || ($isTextOrList && $nextIsTextOrList))) {
                        $cellText .= "\n";
                    }
                }
            }
            $cellText = trim($cellText);
            $cellText = preg_replace("/\n{3,}/", "\n\n", $cellText);
            // Decode entities Word/PhpWord may have left (e.g. &lt; &gt;) so we encode once for HTML
            $cellText = html_entity_decode($cellText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $cellText = htmlspecialchars($cellText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $cellText = nl2br($cellText);
            $html .= '<td class="border border-gray-300 px-2 py-1.5 align-top">' . $cellText . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';
    return $html;
}

/**
 * Extract text from a single PhpWord element (recursive; handles tables, text runs, etc.)
 * Tables are output as HTML when using extractWordElementTextOrHtml().
 */
function extractWordElementText($element) {
    // Table: iterate rows -> cells -> elements (Table has no getText())
    if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
        $text = '';
        foreach ($element->getRows() as $row) {
            $rowText = [];
            foreach ($row->getCells() as $cell) {
                $cellText = '';
                if (method_exists($cell, 'getElements')) {
                    foreach ($cell->getElements() as $child) {
                        $cellText .= extractWordElementText($child);
                    }
                }
                $rowText[] = trim($cellText);
            }
            $text .= implode("\t", array_filter($rowText)) . "\n";
        }
        return $text;
    }
    // Paragraph/line break – output newline
    if ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
        return "\n";
    }
    // Elements with getText() – use it and don't recurse (avoids duplicating TextRun content)
    if (method_exists($element, 'getText')) {
        return $element->getText();
    }
    // Other containers (Section, Cell, etc.) – recurse into getElements()
    $text = '';
    if (method_exists($element, 'getElements')) {
        foreach ($element->getElements() as $child) {
            $text .= extractWordElementText($child);
        }
    }
    return $text;
}

/**
 * Extract element as text or HTML: tables as HTML, everything else as plain text.
 */
function extractWordElementTextOrHtml($element) {
    if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
        return extractWordTableAsHtml($element);
    }
    return extractWordElementText($element);
}

/**
 * Extract text from Word documents (including content inside tables).
 * Tables are output as HTML so they can be displayed as tables in the job description.
 */
function extractWordText($filePath) {
    // Try using phpoffice/phpword if available
    if (class_exists('PhpOffice\PhpWord\IOFactory')) {
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $output = '';
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $output .= extractWordElementTextOrHtml($element);
                    $output .= "\n";
                }
            }
            // Normalise whitespace in plain-text parts only (avoid breaking HTML)
            $output = preg_replace("/\n{3,}/", "\n\n", $output);
            // Decode HTML entities in plain text (e.g. &lt; from Word); tables are already HTML
            $output = html_entity_decode($output, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return ['success' => true, 'text' => trim($output)];
        } catch (Exception $e) {
            // Fall through to basic extraction
        }
    }
    
    // Fallback: Return error suggesting library installation
    return [
        'success' => false,
        'error' => 'Word document extraction requires phpoffice/phpword library. Install via: composer require phpoffice/phpword'
    ];
}

/**
 * Extract text from Excel files
 */
function extractExcelText($filePath) {
    // Try using phpoffice/phpspreadsheet if available
    if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $text = '';
            foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                foreach ($worksheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $rowText = [];
                    foreach ($cellIterator as $cell) {
                        $rowText[] = $cell->getCalculatedValue();
                    }
                    $text .= implode(' ', $rowText) . "\n";
                }
            }
            return ['success' => true, 'text' => trim($text)];
        } catch (Exception $e) {
            // Fall through to basic extraction
        }
    }
    
    // Fallback: Return error suggesting library installation
    return [
        'success' => false,
        'error' => 'Excel extraction requires phpoffice/phpspreadsheet library. Install via: composer require phpoffice/phpspreadsheet'
    ];
}

/**
 * Extract text from plain text files
 */
function extractTextFile($filePath) {
    $text = file_get_contents($filePath);
    if ($text === false) {
        return ['success' => false, 'error' => 'Failed to read file'];
    }
    
    // Detect encoding and convert to UTF-8
    $encoding = mb_detect_encoding($text, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
    if ($encoding && $encoding !== 'UTF-8') {
        $text = mb_convert_encoding($text, 'UTF-8', $encoding);
    }
    
    return ['success' => true, 'text' => trim($text)];
}

/**
 * Extract text from images using OCR (optional)
 */
function extractImageText($filePath) {
    // Try using Tesseract OCR if available
    if (function_exists('shell_exec') && !empty(shell_exec('which tesseract'))) {
        $output = shell_exec('tesseract "' . escapeshellarg($filePath) . '" stdout 2>&1');
        if ($output !== null && !empty(trim($output))) {
            return ['success' => true, 'text' => trim($output)];
        }
    }
    
    // Fallback: Return error
    return [
        'success' => false,
        'error' => 'Image OCR requires Tesseract OCR. Install tesseract-ocr package on your system.'
    ];
}

