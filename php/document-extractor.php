<?php
/**
 * Document Text Extraction Service
 * Extracts text from various document formats (PDF, Word, Excel, text files)
 */

require_once __DIR__ . '/config.php';

/**
 * Extract text from a document file
 * @param string $filePath Full path to the file
 * @param string $mimeType MIME type of the file
 * @return array ['success' => bool, 'text' => string, 'error' => string]
 */
function extractDocumentText($filePath, $mimeType) {
    if (!file_exists($filePath)) {
        return ['success' => false, 'error' => 'File not found'];
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
                return ['success' => false, 'error' => 'Unsupported file type: ' . $mimeType];
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
 * Extract text from Word documents
 */
function extractWordText($filePath) {
    // Try using phpoffice/phpword if available
    if (class_exists('PhpOffice\PhpWord\IOFactory')) {
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $text = '';
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
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

