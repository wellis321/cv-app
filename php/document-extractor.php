<?php
/**
 * Document Text Extraction Service
 * Extracts text from various document formats (PDF, Word, Excel, text files)
 */

require_once __DIR__ . '/config.php';

/**
 * Convert PDF to base64-encoded PNG images for vision AI.
 * Uses pdftoppm (poppler-utils). Returns [] if unavailable.
 * @param string $filePath Path to PDF
 * @param int $maxPages Max pages to convert (default 12)
 * @return array Array of base64-encoded PNG strings
 */
function convertPdfToImages($filePath, $maxPages = 12) {
    if (!file_exists($filePath) || !function_exists('shell_exec')) {
        return [];
    }
    $which = trim((string) @shell_exec('which pdftoppm 2>/dev/null'));
    if (empty($which)) {
        return [];
    }
    $tmpDir = sys_get_temp_dir() . '/pdf_vision_' . uniqid();
    if (!@mkdir($tmpDir, 0700, true)) {
        return [];
    }
    $prefix = $tmpDir . '/page';
    $cmd = 'pdftoppm -png -r 150 -f 1 -l ' . (int) $maxPages . ' ' . escapeshellarg($filePath) . ' ' . escapeshellarg($prefix) . ' 2>/dev/null';
    @shell_exec($cmd);
    $images = [];
    foreach (glob($tmpDir . '/page-*.png') ?: [] as $f) {
        $img = file_get_contents($f);
        if ($img !== false) {
            $images[] = base64_encode($img);
        }
    }
    array_map('unlink', glob($tmpDir . '/*.png') ?: []);
    @rmdir($tmpDir);
    return $images;
}

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
 * Extract text from PDF file.
 * Uses plain text extraction (reliable). For tables/structure, use "Format with AI" which uses vision models.
 */
function extractPdfText($filePath) {
    // Plain text with tab-separated columns
    if (class_exists('Smalot\PdfParser\Parser')) {
        try {
            $config = new \Smalot\PdfParser\Config();
            $config->setHorizontalOffset("\t");
            $parser = new \Smalot\PdfParser\Parser([], $config);
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
            $text = $text !== null ? trim((string) $text) : '';
            if ($text !== '') {
                return ['success' => true, 'text' => $text];
            }
        } catch (\Throwable $e) {
            // Fall through
        }
    }

    // pdftotext with -layout preserves column alignment
    if (function_exists('shell_exec') && !empty(trim((string) @shell_exec('which pdftotext 2>/dev/null')))) {
        $output = shell_exec('pdftotext -layout ' . escapeshellarg($filePath) . ' - 2>&1');
        if ($output !== null && trim($output) !== '') {
            return ['success' => true, 'text' => trim($output)];
        }
    }

    $hasParser = class_exists('Smalot\PdfParser\Parser');
    $hasPdftotext = function_exists('shell_exec') && !empty(trim((string) @shell_exec('which pdftotext 2>/dev/null')));
    if ($hasParser || $hasPdftotext) {
        return [
            'success' => false,
            'error' => 'Could not extract text from this PDF. It may be image-based (scanned document) or use unsupported features. Try copying text manually or use a PDF with selectable text.'
        ];
    }
    return [
        'success' => false,
        'error' => 'PDF extraction requires smalot/pdfparser (composer require smalot/pdfparser) or pdftotext (poppler-utils).'
    ];
}

/**
 * Extract PDF text with table structure using position data (getDataTm).
 * Groups text by x,y coordinates to reconstruct rows and columns.
 */
function extractPdfTextWithTables($filePath) {
    $parser = new \Smalot\PdfParser\Parser();
    $pdf = $parser->parseFile($filePath);
    $pages = $pdf->getPages();
    if (empty($pages)) {
        return ['success' => false, 'text' => '', 'error' => 'No pages'];
    }

    $Y_TOLERANCE = 4;   // Points – same row if Y within this
    $X_COL_GAP = 28;    // Points – gap larger than this = new column (higher = fewer spurious splits)
    $allRows = [];

    foreach ($pages as $page) {
        $data = $page->getDataTm();
        if (empty($data)) continue;

        $items = [];
        foreach ($data as $item) {
            if (!isset($item[0], $item[1]) || !is_array($item[0])) continue;
            $tm = $item[0];
            $x = isset($tm[4]) ? (float) $tm[4] : 0;
            $y = isset($tm[5]) ? (float) $tm[5] : 0;
            $text = trim((string) $item[1]);
            if ($text === '') continue;
            $items[] = ['x' => $x, 'y' => $y, 'text' => $text];
        }
        if (empty($items)) continue;

        // Group by row (similar Y)
        usort($items, function ($a, $b) {
            if (abs($a['y'] - $b['y']) <= 4) return $a['x'] <=> $b['x'];
            return $b['y'] <=> $a['y']; // PDF Y: larger = higher on page
        });

        $currentY = null;
        $currentRow = [];
        foreach ($items as $it) {
            if ($currentY === null || abs($it['y'] - $currentY) <= $Y_TOLERANCE) {
                $currentRow[] = $it;
                if ($currentY === null) $currentY = $it['y'];
            } else {
                if (!empty($currentRow)) {
                    $allRows[] = mergeRowIntoColumns($currentRow, $X_COL_GAP);
                }
                $currentRow = [$it];
                $currentY = $it['y'];
            }
        }
        if (!empty($currentRow)) {
            $allRows[] = mergeRowIntoColumns($currentRow, $X_COL_GAP);
        }
    }

    if (empty($allRows)) {
        return ['success' => false, 'text' => ''];
    }

    $output = partitionRowsIntoTablesAndParagraphs($allRows);
    if ($output === '') {
        return ['success' => false, 'text' => ''];
    }
    return ['success' => true, 'text' => $output];
}

function partitionRowsIntoTablesAndParagraphs(array $allRows) {
    $MIN_COLS = 2;
    $MAX_COLS = 6;
    $MIN_BLOCK_ROWS = 3;
    $MIN_BLOCK_DENSITY = 0.35;
    $MAX_COL_SPREAD = 2;

    $segments = [];
    $i = 0;
    $n = count($allRows);

    while ($i < $n) {
        $row = $allRows[$i];
        $numCols = count($row);
        $filled = count(array_filter($row, fn($c) => trim($c) !== ''));

        $isTableLike = $numCols >= $MIN_COLS && $numCols <= $MAX_COLS && $filled >= 1;

        if ($isTableLike) {
            $block = [$row];
            $j = $i + 1;
            while ($j < $n) {
                $next = $allRows[$j];
                $nc = count($next);
                if ($nc >= $MIN_COLS && $nc <= $MAX_COLS) {
                    $block[] = $next;
                    $j++;
                } else {
                    break;
                }
            }
            $totalCells = 0;
            $filledCells = 0;
            foreach ($block as $r) {
                foreach ($r as $c) {
                    $totalCells++;
                    if (trim($c) !== '') $filledCells++;
                }
            }
            $density = $totalCells > 0 ? $filledCells / $totalCells : 0;
            $colCounts = array_map('count', $block);
            $colSpread = max($colCounts) - min($colCounts);
            if (count($block) >= $MIN_BLOCK_ROWS && $density >= $MIN_BLOCK_DENSITY && $colSpread <= $MAX_COL_SPREAD) {
                $segments[] = ['type' => 'table', 'rows' => $block];
                $i = $j;
                continue;
            }
        }

        $paraRows = [];
        while ($i < $n) {
            $r = $allRows[$i];
            $nc = count($r);
            $filled = count(array_filter($r, fn($c) => trim($c) !== ''));
            $isTableLike = $nc >= $MIN_COLS && $nc <= $MAX_COLS && $filled >= 1;

            if ($isTableLike) {
                $block = [$r];
                $j = $i + 1;
                while ($j < $n) {
                    $next = $allRows[$j];
                    $nn = count($next);
                    if ($nn >= $MIN_COLS && $nn <= $MAX_COLS) {
                        $block[] = $next;
                        $j++;
                    } else {
                        break;
                    }
                }
                $totalCells = 0;
                $filledCells = 0;
                foreach ($block as $bRow) {
                    foreach ($bRow as $cell) {
                        $totalCells++;
                        if (trim($cell) !== '') $filledCells++;
                    }
                }
                $density = $totalCells > 0 ? $filledCells / $totalCells : 0;
                $colCounts = array_map('count', $block);
                $colSpread = max($colCounts) - min($colCounts);
                if (count($block) >= $MIN_BLOCK_ROWS && $density >= $MIN_BLOCK_DENSITY && $colSpread <= $MAX_COL_SPREAD) {
                    break;
                }
            }
            $cellTexts = array_map('trim', array_filter($r, fn($c) => trim($c) !== ''));
            $lineText = preg_replace('/\s+/', ' ', implode(' ', $cellTexts));
            if ($lineText !== '') $paraRows[] = $lineText;
            $i++;
        }
        if (!empty($paraRows)) {
            $text = implode("\n", $paraRows);
            if ($text !== '') {
                $segments[] = ['type' => 'para', 'text' => $text];
            }
        }
    }

    $html = '';
    foreach ($segments as $seg) {
        if ($seg['type'] === 'table') {
            $rows = $seg['rows'];
            $maxCols = max(array_map('count', $rows));
            $html .= '<table class="border border-gray-300 border-collapse w-full my-3 text-sm"><tbody>';
            foreach ($rows as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) {
                    $escaped = htmlspecialchars(trim($cell), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $html .= '<td class="border border-gray-300 px-2 py-1.5 align-top">' . $escaped . '</td>';
                }
                $missing = $maxCols - count($row);
                for ($k = 0; $k < $missing; $k++) {
                    $html .= '<td class="border border-gray-300 px-2 py-1.5"></td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
        } else {
            $lines = explode("\n", $seg['text']);
            $lines = array_map(fn($l) => preg_replace('/[ \t]+/', ' ', trim($l)), $lines);
            $text = implode("\n", array_filter($lines));
            if ($text !== '') {
                $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $html .= '<p class="my-2">' . nl2br($escaped) . '</p>';
            }
        }
    }

    return $html;
}

function mergeRowIntoColumns(array $row, $xGap) {
    if (empty($row)) return [];
    usort($row, fn($a, $b) => $a['x'] <=> $b['x']);
    $cells = [];
    $lastX = -9999;
    $current = '';
    foreach ($row as $it) {
        if ($it['x'] - $lastX > $xGap && $current !== '') {
            $cells[] = trim($current);
            $current = '';
        }
        $current .= ($current !== '' ? ' ' : '') . $it['text'];
        $lastX = $it['x'];
    }
    if ($current !== '') $cells[] = trim($current);
    return $cells;
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
    // TextRun and ListItemRun are containers; getText() can return object. Recurse instead.
    if ($element instanceof \PhpOffice\PhpWord\Element\TextRun || $element instanceof \PhpOffice\PhpWord\Element\ListItemRun) {
        $text = '';
        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                $text .= extractWordElementText($child);
            }
        }
        return $text;
    }
    // Elements with getText() – use it (Text, etc.)
    if (method_exists($element, 'getText')) {
        $t = $element->getText();
        return is_string($t) ? $t : '';
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
 * Extract a single Text element with inline formatting (bold, italic, underline).
 */
function extractWordTextAsHtml($element) {
    $t = $element->getText();
    $text = is_string($t) ? $t : '';
    if ($text === '') {
        return '';
    }
    $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $font = $element->getFontStyle();
    if ($font instanceof \PhpOffice\PhpWord\Style\Font) {
        $wrap = [];
        if ($font->isBold()) {
            $wrap[] = ['<strong>', '</strong>'];
        }
        if ($font->isItalic()) {
            $wrap[] = ['<em>', '</em>'];
        }
        if ($font->getUnderline() && $font->getUnderline() !== \PhpOffice\PhpWord\Style\Font::UNDERLINE_NONE) {
            $wrap[] = ['<u>', '</u>'];
        }
        if ($font->isStrikethrough()) {
            $wrap[] = ['<s>', '</s>'];
        }
        foreach (array_reverse($wrap) as $pair) {
            $text = $pair[0] . $text . $pair[1];
        }
    }
    return $text;
}

/**
 * Extract inline content (TextRun/ListItemRun children) as HTML with formatting.
 */
function extractWordInlineAsHtml($element) {
    if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
        return extractWordTextAsHtml($element);
    }
    if ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
        return '<br>';
    }
    if ($element instanceof \PhpOffice\PhpWord\Element\TextRun || $element instanceof \PhpOffice\PhpWord\Element\ListItemRun) {
        $html = '';
        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                $html .= extractWordInlineAsHtml($child);
            }
        }
        return $html;
    }
    if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
        return extractWordTableAsHtml($element);
    }
    if ($element instanceof \PhpOffice\PhpWord\Element\Link) {
        $href = htmlspecialchars($element->getSource(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = htmlspecialchars($element->getText(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return '<a href="' . $href . '" class="text-blue-600 hover:underline" target="_blank" rel="noopener">' . $text . '</a>';
    }
    if (method_exists($element, 'getText')) {
        $t = $element->getText();
        if (is_string($t)) {
            return htmlspecialchars($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        if (is_array($t)) {
            return htmlspecialchars(implode('', $t), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }
    $html = '';
    if (method_exists($element, 'getElements')) {
        foreach ($element->getElements() as $child) {
            $html .= extractWordInlineAsHtml($child);
        }
    }
    return $html;
}

/**
 * Extract element as HTML: tables as HTML tables, paragraphs/headings/lists as formatted HTML.
 */
function extractWordElementAsHtml($element) {
    if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
        return extractWordTableAsHtml($element);
    }
    if ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
        return '<br>';
    }
    if ($element instanceof \PhpOffice\PhpWord\Element\Title) {
        $depth = $element->getDepth();
        $tag = $depth >= 1 && $depth <= 6 ? 'h' . min($depth, 6) : 'h2';
        $content = $element->getText();
        if ($content instanceof \PhpOffice\PhpWord\Element\TextRun) {
            $inner = extractWordInlineAsHtml($content);
        } else {
            $inner = htmlspecialchars(is_string($content) ? $content : '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        return $inner !== '' ? "<{$tag} class=\"font-semibold text-gray-900 mt-3 mb-1\">{$inner}</{$tag}>" : '';
    }
    if ($element instanceof \PhpOffice\PhpWord\Element\ListItem) {
        $text = $element->getText();
        $inner = htmlspecialchars(is_string($text) ? $text : '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return '<li class="ml-4 my-0.5">' . $inner . '</li>';
    }
    if ($element instanceof \PhpOffice\PhpWord\Element\ListItemRun) {
        $inner = extractWordInlineAsHtml($element);
        return '<li class="ml-4 my-0.5">' . $inner . '</li>';
    }
    if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
        $inner = extractWordInlineAsHtml($element);
        return trim($inner) !== '' ? '<p class="my-1">' . $inner . '</p>' : '';
    }
    if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
        $inner = extractWordTextAsHtml($element);
        return $inner !== '' ? '<p class="my-1">' . $inner . '</p>' : '';
    }
    if ($element instanceof \PhpOffice\PhpWord\Element\Link) {
        $href = htmlspecialchars($element->getSource(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = htmlspecialchars($element->getText(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return '<p class="my-1"><a href="' . $href . '" class="text-blue-600 hover:underline" target="_blank" rel="noopener">' . $text . '</a></p>';
    }
    if ($element instanceof \PhpOffice\PhpWord\Element\PreserveText) {
        $t = $element->getText();
        $str = is_array($t) ? implode('', $t) : (is_string($t) ? $t : '');
        $inner = htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $inner !== '' ? '<p class="my-1">' . $inner . '</p>' : '';
    }
    $html = '';
    if (method_exists($element, 'getElements')) {
        foreach ($element->getElements() as $child) {
            $html .= extractWordElementAsHtml($child);
        }
    }
    return $html;
}

/**
 * Extract element as text or HTML: tables as HTML, everything else as formatted HTML.
 */
function extractWordElementTextOrHtml($element) {
    if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
        return extractWordTableAsHtml($element);
    }
    return extractWordElementAsHtml($element);
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
                $sectionHtml = '';
                $listBuffer = '';
                foreach ($section->getElements() as $element) {
                    $chunk = extractWordElementTextOrHtml($element);
                    if (preg_match('/^<li\s/i', $chunk)) {
                        $listBuffer .= $chunk;
                    } else {
                        if ($listBuffer !== '') {
                            $sectionHtml .= '<ul class="list-disc list-inside my-1 space-y-0.5">' . $listBuffer . '</ul>';
                            $listBuffer = '';
                        }
                        if ($chunk !== '') {
                            $sectionHtml .= $chunk . "\n";
                        }
                    }
                }
                if ($listBuffer !== '') {
                    $sectionHtml .= '<ul class="list-disc list-inside my-1 space-y-0.5">' . $listBuffer . '</ul>';
                }
                $output .= $sectionHtml;
            }
            // Collapse excessive line breaks and empty blocks (Word often uses empty paragraphs for spacing)
            $output = preg_replace('/(<br\s*\/?>)\s*\1+/', '<br>', $output);
            $output = preg_replace('/<p class="[^"]*">\s*<\/p>\s*/', '', $output);
            $output = preg_replace("/\n{3,}/", "\n\n", $output);
            // Decode HTML entities in plain text (e.g. &lt; from Word); tables are already HTML
            $output = html_entity_decode($output, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $result = ['success' => true, 'text' => trim($output)];
            return $result;
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => 'Word extraction failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Fallback: PhpWord not available
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

