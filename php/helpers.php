<?php
/**
 * Helper functions and includes
 */

// Load all core files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/cv-data.php';
require_once __DIR__ . '/subscriptions.php';
require_once __DIR__ . '/stripe.php';
require_once __DIR__ . '/authorisation.php';
require_once __DIR__ . '/invitations.php';
require_once __DIR__ . '/job-applications.php';
require_once __DIR__ . '/ai-service.php';
require_once __DIR__ . '/cv-variants.php';

/**
 * Enforce canonical domain (prevents www/non-www duplicates)
 */
if (!function_exists('enforceCanonicalDomain')) {
function enforceCanonicalDomain() {
    if (PHP_SAPI === 'cli') {
        return;
    }

    $currentHost = $_SERVER['HTTP_HOST'] ?? null;
    if (!$currentHost) {
        return;
    }

    $primaryHost = parse_url(APP_URL, PHP_URL_HOST);
    if (!$primaryHost) {
        return;
    }

    $normalizedCurrent = strtolower($currentHost);
    $normalizedPrimary = strtolower($primaryHost);

    if ($normalizedCurrent === $normalizedPrimary) {
        return;
    }

    $scheme = parse_url(APP_URL, PHP_URL_SCHEME) ?: 'https';
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $redirectUrl = $scheme . '://' . $primaryHost . $requestUri;

    header('Location: ' . $redirectUrl, true, 301);
    exit;
}
} // End function_exists check

if (APP_ENV === 'production' && !defined('SKIP_CANONICAL_REDIRECT')) {
    enforceCanonicalDomain();
}

// Set security headers early (before any output)
if (!headers_sent()) {
    setSecurityHeaders();
}

/**
 * Convert American spelling to British spelling in text (for UK documents).
 */
if (!function_exists('convertToBritishSpelling')) {
function convertToBritishSpelling($text) {
    if (!is_string($text) || $text === '') {
        return $text;
    }
    $replacements = [
        '/\borganization\b/i' => 'organisation',
        '/\borganizations\b/i' => 'organisations',
        '/\borganized\b/i' => 'organised',
        '/\borganizing\b/i' => 'organising',
        '/\borganize\b/i' => 'organise',
        '/\bemphasize\b/i' => 'emphasise',
        '/\bemphasized\b/i' => 'emphasised',
        '/\bemphasizing\b/i' => 'emphasising',
        '/\bcolor\b/i' => 'colour',
        '/\bcolors\b/i' => 'colours',
        '/\bcenter\b/i' => 'centre',
        '/\bcenters\b/i' => 'centres',
        '/\brealize\b/i' => 'realise',
        '/\brealized\b/i' => 'realised',
        '/\brealizes\b/i' => 'realises',
        '/\brecognize\b/i' => 'recognise',
        '/\brecognized\b/i' => 'recognised',
        '/\brecognizes\b/i' => 'recognises',
        '/\banalyze\b/i' => 'analyse',
        '/\banalyzed\b/i' => 'analysed',
        '/\banalyzes\b/i' => 'analyses',
        '/\bfavor\b/i' => 'favour',
        '/\bfavors\b/i' => 'favours',
        '/\bfavored\b/i' => 'favoured',
        '/\bhonor\b/i' => 'honour',
        '/\bhonors\b/i' => 'honours',
        '/\bhonored\b/i' => 'honoured',
        '/\blabor\b/i' => 'labour',
        '/\blabors\b/i' => 'labours',
        '/\bneighbor\b/i' => 'neighbour',
        '/\bneighbors\b/i' => 'neighbours',
        '/\bbehavior\b/i' => 'behaviour',
        '/\bbehaviors\b/i' => 'behaviours',
        '/\bbehavioral\b/i' => 'behavioural',
        '/\bcustomize\b/i' => 'customise',
        '/\bcustomized\b/i' => 'customised',
        '/\bcustomizing\b/i' => 'customising',
        '/\bcustomization\b/i' => 'customisation',
        '/\bcustomizations\b/i' => 'customisations',
        '/\bprioritize\b/i' => 'prioritise',
        '/\bprioritized\b/i' => 'prioritised',
        '/\bprioritizing\b/i' => 'prioritising',
        '/\bprioritization\b/i' => 'prioritisation',
        '/\bspecialize\b/i' => 'specialise',
        '/\bspecialized\b/i' => 'specialised',
        '/\bspecializing\b/i' => 'specialising',
        '/\bspecialization\b/i' => 'specialisation',
        '/\bspecializations\b/i' => 'specialisations',
        '/\boptimize\b/i' => 'optimise',
        '/\boptimized\b/i' => 'optimised',
        '/\boptimizing\b/i' => 'optimising',
        '/\boptimization\b/i' => 'optimisation',
        '/\boptimizations\b/i' => 'optimisations',
        '/\bauthorize\b/i' => 'authorise',
        '/\bauthorized\b/i' => 'authorised',
        '/\bauthorization\b/i' => 'authorisation',
        '/\bdefense\b/i' => 'defence',
        '/\bcatalog\b/i' => 'catalogue',
        '/\bcatalogs\b/i' => 'catalogues',
        '/\banalog\b/i' => 'analogue',
        '/\banalogs\b/i' => 'analogues',
        '/\bdialog\b/i' => 'dialogue',
        '/\bdialogs\b/i' => 'dialogues',
        '/\blabeled\b/i' => 'labelled',
        '/\blabeling\b/i' => 'labelling',
        '/\btraveled\b/i' => 'travelled',
        '/\btraveling\b/i' => 'travelling',
        '/\bcanceled\b/i' => 'cancelled',
        '/\bcanceling\b/i' => 'cancelling',
        '/\bmodeled\b/i' => 'modelled',
        '/\bmodeling\b/i' => 'modelling',
        '/\bfulfill\b/i' => 'fulfil',
        '/\bfulfilled\b/i' => 'fulfilled',
        '/\bfulfillment\b/i' => 'fulfilment',
        '/\bskillful\b/i' => 'skilful',
        '/\bmaneuver\b/i' => 'manoeuvre',
        '/\bmaneuvers\b/i' => 'manoeuvres',
        '/\bmaximize\b/i' => 'maximise',
        '/\bmaximized\b/i' => 'maximised',
        '/\bmaximizing\b/i' => 'maximising',
        '/\bminimize\b/i' => 'minimise',
        '/\bminimized\b/i' => 'minimised',
        '/\bminimizing\b/i' => 'minimising',
        '/\bfinalize\b/i' => 'finalise',
        '/\bfinalized\b/i' => 'finalised',
        '/\bfinalizing\b/i' => 'finalising',
        '/\bstandardize\b/i' => 'standardise',
        '/\bstandardized\b/i' => 'standardised',
        '/\bcharacterize\b/i' => 'characterise',
        '/\bcharacterized\b/i' => 'characterised',
        '/\bsummarize\b/i' => 'summarise',
        '/\bsummarized\b/i' => 'summarised',
        '/\bprofessionalize\b/i' => 'professionalise',
        '/\bprofessionalized\b/i' => 'professionalised',
        '/\bfervor\b/i' => 'fervour',
        '/\bfervors\b/i' => 'fervours',
        '/\bvigor\b/i' => 'vigour',
        '/\bvigors\b/i' => 'vigours',
        '/\bharbor\b/i' => 'harbour',
        '/\bharbors\b/i' => 'harbours',
        '/\bharbored\b/i' => 'harboured',
        '/\brumor\b/i' => 'rumour',
        '/\brumors\b/i' => 'rumours',
        '/\bflavor\b/i' => 'flavour',
        '/\bflavors\b/i' => 'flavours',
        '/\bflavored\b/i' => 'flavoured',
        '/\bendeavor\b/i' => 'endeavour',
        '/\bendeavors\b/i' => 'endeavours',
        '/\bendeavored\b/i' => 'endeavoured',
        '/\bfavorable\b/i' => 'favourable',
        '/\bunfavorable\b/i' => 'unfavourable',
        '/\bhonorable\b/i' => 'honourable',
        '/\barmor\b/i' => 'armour',
        '/\barmors\b/i' => 'armours',
        '/\bhumor\b/i' => 'humour',
        '/\bhumors\b/i' => 'humours',
        '/\bhumored\b/i' => 'humoured',
        '/\brevolutionizing\b/i' => 'revolutionising',
        '/\brevolutionized\b/i' => 'revolutionised',
        '/\brevolutionize\b/i' => 'revolutionise',
        '/\brevolutionizes\b/i' => 'revolutionises',
        '/\bmodernizing\b/i' => 'modernising',
        '/\bmodernized\b/i' => 'modernised',
        '/\bmodernize\b/i' => 'modernise',
        '/\bfamiliarizing\b/i' => 'familiarising',
        '/\bfamiliarized\b/i' => 'familiarised',
        '/\bfamiliarize\b/i' => 'familiarise',
        '/\bparalyzing\b/i' => 'paralysing',
        '/\bparalyzed\b/i' => 'paralysed',
        '/\bparalyze\b/i' => 'paralyse',
    ];
    foreach ($replacements as $pattern => $replacement) {
        $text = preg_replace($pattern, $replacement, $text);
    }
    return $text;
}
} // End function_exists check

/**
 * Sanitize cover letter text: strip control artifacts (e.g. [control_187]) and remove duplicate letters.
 * Use before saving or displaying cover letter content from browser AI or pasted sources.
 */
if (!function_exists('sanitizeCoverLetterText')) {
function sanitizeCoverLetterText($text) {
    if (!is_string($text) || $text === '') {
        return $text;
    }
    $text = trim($text);
    $text = preg_replace('/\[control_\d+\]/i', '', $text);
    $pattern = '/\bDear\s+(?:Hiring\s+Manager|Recruitment\s+Team|[A-Z][a-z]+)\s*[,:]?\s*\n/i';
    if (preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE) >= 2) {
        $last = end($matches[0]);
        $text = substr($text, $last[1]);
    }
    return trim($text);
}
} // End function_exists check

/**
 * Render a view/template
 */
if (!function_exists('render')) {
function render($template, $data = []) {
    extract($data);
    $templatePath = __DIR__ . '/../views/' . $template . '.php';

    if (!file_exists($templatePath)) {
        die("Template not found: {$template}");
    }

    ob_start();
    include $templatePath;
    return ob_get_clean();
}
} // End function_exists check

/**
 * Include partial/template
 */
if (!function_exists('partial')) {
function partial($template, $data = []) {
    extract($data);
    $templatePath = __DIR__ . '/../views/partials/' . $template . '.php';

    if (!file_exists($templatePath)) {
        die("Partial not found: {$template}");
    }

    include $templatePath;
}
} // End function_exists check

/**
 * Sanitise job description for safe HTML output (allows tables from Word extraction).
 * Strips all tags except table, tr, td, th, tbody, thead, p, br; removes attributes to prevent XSS.
 */
if (!function_exists('jobDescriptionHtml')) {
function jobDescriptionHtml($html) {
    if ($html === null || $html === '') return '';
    $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $allowed = '<table><tbody><thead><tr><td><th><p><br>';
    $html = strip_tags($html, $allowed);
    $html = preg_replace('/<\s*(\w+)\s+[^>]*>/', '<$1>', $html);
    return $html;
}
}

/**
 * Render job description for display. If content contains HTML (e.g. imported tables),
 * output safe HTML; otherwise render as markdown so **bold** etc. work.
 */
if (!function_exists('renderJobDescription')) {
function renderJobDescription($text) {
    if ($text === null || $text === '') return '';
    $trimmed = trim($text);
    // Content that looks like HTML (tables from Word/PDF, or <br> line breaks) – render as safe HTML
    if (preg_match('/<\s*table[\s>]|<\s*tr\s|<\s*td\s|<\s*th\s|<\s*br\s*\/?\s*>/i', $trimmed)) {
        return jobDescriptionHtml($trimmed);
    }
    return renderMarkdown($trimmed);
}
}

/**
 * Render markdown to HTML (basic server-side conversion, enhanced client-side with marked.js)
 * This provides a fallback; marked.js will enhance it client-side for better rendering.
 */
if (!function_exists('renderMarkdown')) {
function renderMarkdown($markdown) {
    if ($markdown === null || $markdown === '') return '';
    $markdown = html_entity_decode($markdown, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // Normalise excessive newlines (3+) to paragraph break; preserve \n\n for paragraph spacing.
    // Single \n = line break, double \n\n = paragraph spacing on the CV.
    $markdown = preg_replace('/\R{3,}/u', "\n\n", $markdown);

    // Escape HTML to prevent XSS, then convert markdown syntax
    $text = htmlspecialchars($markdown, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Convert bold **text** to <strong>text</strong>
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    
    // Convert italic *text* to <em>text</em> (but not if it's part of **text**)
    $text = preg_replace('/(?<!\*)\*([^*]+?)\*(?!\*)/', '<em>$1</em>', $text);
    
    // Convert headers
    $text = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $text);
    
    // Convert links [text](url)
    $text = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $text);
    
    // Convert underline tags (if they were in original)
    $text = preg_replace('/&lt;u&gt;(.*?)&lt;\/u&gt;/', '<u>$1</u>', $text);
    
    // Convert markdown lists and tables to HTML
    $lines = explode("\n", $text);
    $output = [];
    $i = 0;
    while ($i < count($lines)) {
        $line = $lines[$i];
        // Unordered list: - , * , or • at line start
        if (preg_match('/^\s*[-*•]\s+/', $line)) {
            $listItems = [];
            while ($i < count($lines) && preg_match('/^\s*[-*•]\s+(.*)$/s', $lines[$i], $m)) {
                $listItems[] = $m[1];
                $i++;
            }
            if (!empty($listItems)) {
                $lis = implode('', array_map(function ($item) { return '<li>' . $item . '</li>'; }, $listItems));
                $output[] = '<ul style="list-style-type: disc; padding-left: 1.25em;">' . $lis . '</ul>';
            }
            continue;
        }
        // Ordered list: 1. , 2. , etc at line start
        if (preg_match('/^\s*\d+\.\s+/', $line)) {
            $listItems = [];
            while ($i < count($lines) && preg_match('/^\s*\d+\.\s+(.*)$/s', $lines[$i], $m)) {
                $listItems[] = $m[1];
                $i++;
            }
            if (!empty($listItems)) {
                $lis = implode('', array_map(function ($item) { return '<li>' . $item . '</li>'; }, $listItems));
                $output[] = '<ol style="list-style-type: decimal; padding-left: 1.25em;">' . $lis . '</ol>';
            }
            continue;
        }
        if (preg_match('/^\|.+\|$/', trim($line))) {
            $tableRows = [];
            while ($i < count($lines) && preg_match('/^\|.+\|$/', trim($lines[$i]))) {
                $row = $lines[$i];
                if (preg_match('/^\|[\s\-:]+\|/', trim($row))) {
                    $i++;
                    continue;
                }
                $cells = array_map('trim', explode('|', trim($row, '|')));
                $cells = array_values(array_filter($cells));
                if (!empty($cells)) {
                    $tableRows[] = $cells;
                }
                $i++;
            }
            if (count($tableRows) >= 1) {
                $html = '<table class="border border-gray-300 border-collapse w-full my-3 text-sm"><tbody>';
                foreach ($tableRows as $ri => $row) {
                    $tag = $ri === 0 ? 'th' : 'td';
                    $html .= '<tr>';
                    foreach ($row as $cell) {
                        $cellEsc = htmlspecialchars($cell, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $html .= "<$tag class=\"border border-gray-300 px-2 py-1.5\">$cellEsc</$tag>";
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody></table>';
                $output[] = $html;
            }
            continue;
        }
        $output[] = $line;
        $i++;
    }
    $text = implode("\n", $output);
    
    // Convert line breaks (preserve formatting)
    $text = nl2br($text);
    
    // Allow safe HTML tags only
    $allowed = '<h1><h2><h3><strong><em><u><a><ul><ol><li><br><p><table><tbody><thead><tr><td><th>';
    $text = strip_tags($text, $allowed);
    
    return $text;
}
}

/**
 * Strip markdown syntax for plain text (useful for AI processing)
 */
if (!function_exists('stripMarkdown')) {
function stripMarkdown($markdown) {
    if ($markdown === null || $markdown === '') return '';
    
    $text = $markdown;
    
    // Remove headers
    $text = preg_replace('/^#{1,6}\s+(.*?)$/m', '$1', $text);
    
    // Remove bold/italic
    $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text);
    $text = preg_replace('/\*(.*?)\*/', '$1', $text);
    
    // Remove underline tags
    $text = preg_replace('/<u>(.*?)<\/u>/', '$1', $text);
    
    // Remove links (keep text)
    $text = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $text);
    
    // Remove list markers
    $text = preg_replace('/^[-*+]\s+/m', '', $text);
    $text = preg_replace('/^\d+\.\s+/m', '', $text);
    
    // Decode HTML entities
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    return trim($text);
}
}

/**
 * Parse max character limit from answer instructions (e.g. "max 1000 characters", "1000 chars").
 * Returns null if no character limit found.
 */
if (!function_exists('parseAnswerCharacterLimit')) {
function parseAnswerCharacterLimit($instructions) {
    if ($instructions === null || trim($instructions) === '') return null;
    if (preg_match('/\b(?:max\.?\s*)?(\d+)\s*(?:character|char)s?\b/i', $instructions, $m)) {
        $n = (int) $m[1];
        return $n > 0 ? $n : null;
    }
    return null;
}
}

/**
 * Parse max word limit from instructions (e.g. "max 300 words", "approximately 250 words").
 * Returns null if no word limit found.
 */
if (!function_exists('parseAnswerWordLimit')) {
function parseAnswerWordLimit($instructions) {
    if ($instructions === null || trim($instructions) === '') return null;
    if (preg_match('/\b(?:max\.?\s*|approximately\s*|approx\.?\s*|~?\s*)(\d+)\s*words?\b/i', $instructions, $m)) {
        $n = (int) $m[1];
        return $n > 0 ? $n : null;
    }
    return null;
}
}

/**
 * Truncate text to max words, cutting at word boundary.
 */
if (!function_exists('truncateToWordLimit')) {
function truncateToWordLimit($text, $maxWords) {
    if ($maxWords <= 0 || $text === null || $text === '') return $text;
    $text = (string) $text;
    $words = preg_split('/\s+/', trim($text), $maxWords + 1);
    if (count($words) <= $maxWords) return $text;
    return implode(' ', array_slice($words, 0, $maxWords));
}
}

/**
 * Truncate text to max characters, cutting at word boundary when possible.
 */
if (!function_exists('truncateToCharacterLimit')) {
function truncateToCharacterLimit($text, $maxChars) {
    if ($maxChars <= 0 || $text === null || $text === '') return $text;
    $text = (string) $text;
    if (mb_strlen($text) <= $maxChars) return $text;
    $cut = mb_substr($text, 0, $maxChars);
    $lastSpace = mb_strrpos($cut, ' ');
    if ($lastSpace !== false && $lastSpace > $maxChars * 0.6) {
        return mb_substr($cut, 0, $lastSpace);
    }
    return rtrim($cut);
}
}

/**
 * Get flash message
 */
if (!function_exists('getFlash')) {
function getFlash($key) {
    $message = $_SESSION['flash_' . $key] ?? null;
    unset($_SESSION['flash_' . $key]);
    return $message;
}
} // End function_exists check

/**
 * Set flash message
 */
if (!function_exists('setFlash')) {
function setFlash($key, $message) {
    $_SESSION['flash_' . $key] = $message;
}
} // End function_exists check

/**
 * Output view
 */
if (!function_exists('view')) {
function view($template, $data = []) {
    echo render($template, $data);
}
} // End function_exists check

/**
 * Include layout with content
 */
if (!function_exists('layout')) {
function layout($layout, $content, $data = []) {
    $data['content'] = $content;
    echo render('layouts/' . $layout, $data);
}
} // End function_exists check

/**
 * Get CV sections in order
 */
if (!function_exists('getCvSections')) {
function getCvSections() {
    return [
        ['id' => 'professional-summary', 'name' => 'Professional Summary', 'path' => '/content-editor.php#professional-summary'],
        ['id' => 'work-experience', 'name' => 'Work Experience', 'path' => '/content-editor.php#work-experience'],
        ['id' => 'education', 'name' => 'Education', 'path' => '/content-editor.php#education'],
        ['id' => 'projects', 'name' => 'Projects', 'path' => '/content-editor.php#projects'],
        ['id' => 'skills', 'name' => 'Skills', 'path' => '/content-editor.php#skills'],
        ['id' => 'certifications', 'name' => 'Certifications', 'path' => '/content-editor.php#certifications'],
        ['id' => 'qualification-equivalence', 'name' => 'Professional Qualification Equivalence', 'path' => '/content-editor.php#qualification-equivalence'],
        ['id' => 'memberships', 'name' => 'Professional Memberships', 'path' => '/content-editor.php#memberships'],
        ['id' => 'interests', 'name' => 'Interests & Activities', 'path' => '/content-editor.php#interests'],
    ];
}

/**
 * Get sections_online for online CV display.
 * Maps API keys to template section ids. Returns array keyed by template section id.
 *
 * @param array $profile Profile row (may have sections_online JSON)
 * @param array|null $cvVariant Variant row (may have pdf_preferences.sections_online)
 * @return array ['profile' => true, 'professional-summary' => true, ...]
 */
function getSectionsOnlineForCv($profile, $cvVariant = null) {
    $templateSectionIds = [
        'profile', 'professional-summary', 'work-experience', 'education', 'skills',
        'projects', 'certifications', 'memberships', 'interests', 'qualification-equivalence'
    ];
    $apiToTemplate = [
        'profile' => 'profile', 'summary' => 'professional-summary', 'work' => 'work-experience',
        'education' => 'education', 'skills' => 'skills', 'projects' => 'projects',
        'certifications' => 'certifications', 'memberships' => 'memberships',
        'interests' => 'interests', 'qualificationEquivalence' => 'qualification-equivalence'
    ];
    $default = array_fill_keys($templateSectionIds, true);

    $raw = null;
    if ($cvVariant && !empty($cvVariant['pdf_preferences'])) {
        $decoded = is_string($cvVariant['pdf_preferences'])
            ? json_decode($cvVariant['pdf_preferences'], true)
            : $cvVariant['pdf_preferences'];
        $raw = $decoded['sections_online'] ?? null;
    }
    if ($raw === null && !empty($profile['sections_online'])) {
        $raw = is_string($profile['sections_online'])
            ? json_decode($profile['sections_online'], true)
            : $profile['sections_online'];
    }
    if (!is_array($raw)) {
        return $default;
    }
    $out = $default;
    foreach ($apiToTemplate as $apiKey => $templateId) {
        if (isset($raw[$apiKey])) {
            $out[$templateId] = (bool) $raw[$apiKey];
        }
    }
    return $out;
}
} // End function_exists check

/**
 * Get section navigation (previous and next sections)
 */
if (!function_exists('getSectionNavigation')) {
function getSectionNavigation($currentSectionId) {
    $sections = getCvSections();
    $currentIndex = -1;

    foreach ($sections as $index => $section) {
        if ($section['id'] === $currentSectionId) {
            $currentIndex = $index;
            break;
        }
    }

    if ($currentIndex === -1) {
        return ['previous' => null, 'current' => null, 'next' => null];
    }

    $previous = ($currentIndex > 0) ? $sections[$currentIndex - 1] : null;
    $current = $sections[$currentIndex];
    $next = ($currentIndex < count($sections) - 1) ? $sections[$currentIndex + 1] : null;

    return ['previous' => $previous, 'current' => $current, 'next' => $next];
}
} // End function_exists check

/**
 * Generate JSON-LD structured data for SEO and AI discoverability
 */
if (!function_exists('generateStructuredData')) {
function generateStructuredData($type = 'default', $data = []) {
    $schemas = [];

    // Organization schema (always included) - helps AI understand who publishes the content
    $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Simple CV Builder',
        'url' => APP_URL,
        'logo' => APP_URL . '/static/images/favicon_io/favicon-32x32.png',
        'description' => 'Free CV builder UK with job application tracking and AI cover letters. Build, share, and export professional CVs online.',
        'foundingDate' => '2024',
        'areaServed' => ['@type' => 'Country', 'name' => 'United Kingdom'],
        'knowsAbout' => ['CV building', 'job application tracking', 'career advice', 'AI cover letters', 'CV templates']
    ];

    // WebSite schema (always included) - helps search and AI understand site structure
    $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Simple CV Builder',
        'url' => APP_URL,
        'description' => 'Free CV builder UK with job application tracking and AI cover letters. Build your CV online, share a link, and export PDFs. For job seekers and recruitment agencies.',
        'inLanguage' => 'en-GB',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => APP_URL . '/?s={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ]
    ];

    // SoftwareApplication schema (for homepage)
    if ($type === 'homepage' || $type === 'default') {
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => 'Simple CV Builder',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web Browser',
            'description' => 'Free CV builder UK with job application tracking and AI cover letters. Build, share, and export professional CVs.',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'GBP',
                'description' => 'Free CV builder with premium templates available'
            ]
        ];
    }

    // Article schema (for resource/blog pages) - helps AI cite and understand articles
    if ($type === 'article' && !empty($data)) {
        $articleUrl = $data['url'] ?? (APP_URL . ($_SERVER['REQUEST_URI'] ?? ''));
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'url' => $articleUrl,
            'image' => $data['image'] ?? APP_URL . '/static/images/default-profile.svg',
            'datePublished' => $data['datePublished'] ?? date('Y-m-d'),
            'dateModified' => $data['dateModified'] ?? date('Y-m-d'),
            'author' => [
                '@type' => 'Organization',
                'name' => 'Simple CV Builder'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Simple CV Builder',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => APP_URL . '/static/images/favicon_io/favicon-32x32.png'
                ]
            ]
        ];
    }

    // ItemList schema (for features page) - helps AI understand product capabilities
    if ($type === 'features' && !empty($data['features'])) {
        $itemListElement = [];
        $position = 1;
        foreach ($data['features'] as $feature) {
            $name = is_array($feature) ? ($feature['name'] ?? '') : $feature;
            $url = is_array($feature) && !empty($feature['link']) ? (str_starts_with($feature['link'], 'http') ? $feature['link'] : APP_URL . $feature['link']) : null;
            $item = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $name
            ];
            if ($url) {
                $item['url'] = $url;
            }
            $itemListElement[] = $item;
        }
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'Simple CV Builder Features',
            'description' => 'Complete list of CV building, job tracking, and AI features offered by Simple CV Builder.',
            'numberOfItems' => count($itemListElement),
            'itemListElement' => $itemListElement
        ];
    }

    // CollectionPage schema (for resources hub) - helps AI understand content organisation
    if ($type === 'collection' && !empty($data['sections'])) {
        $itemListElement = [];
        $position = 1;
        foreach ($data['sections'] as $section) {
            $name = is_array($section) ? ($section['title'] ?? '') : $section;
            $url = is_array($section) && !empty($section['href']) ? (str_starts_with($section['href'], 'http') ? $section['href'] : APP_URL . $section['href']) : null;
            $item = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $name
            ];
            if ($url) {
                $item['url'] = $url;
            }
            if (!empty($section['description'])) {
                $item['description'] = $section['description'];
            }
            $itemListElement[] = $item;
        }
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $data['name'] ?? 'Free CV & Job Guides',
            'description' => $data['description'] ?? 'Free job search guides, CV tips, career advice, and extra income ideas for UK job seekers.',
            'numberOfItems' => count($itemListElement),
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => $itemListElement
            ]
        ];
    }

    // BreadcrumbList schema (if breadcrumbs provided)
    if ($type === 'breadcrumb' && !empty($data['breadcrumbs'])) {
        $itemListElement = [];
        $position = 1;
        foreach ($data['breadcrumbs'] as $crumb) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $crumb['name'],
                'item' => $crumb['url']
            ];
        }

        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement
        ];
    }

    // FAQPage schema (if FAQs provided) - helps AI answer user questions with citations
    if ($type === 'faq' && !empty($data['faqs'])) {
        $mainEntity = [];
        foreach ($data['faqs'] as $faq) {
            $answerText = is_string($faq['answer']) ? strip_tags($faq['answer']) : '';
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answerText
                ]
            ];
        }

        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity
        ];
    }

    return $schemas;
}
} // End function_exists check

/**
 * Output JSON-LD structured data script tags
 */
if (!function_exists('outputStructuredData')) {
function outputStructuredData($schemas) {
    foreach ($schemas as $schema) {
        echo '<script type="application/ld+json">' . "\n";
        echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "\n" . '</script>' . "\n";
    }
}
} // End function_exists check

/**
 * Normalize storage URL to use current request origin (fixes port mismatch in local dev)
 */
if (!function_exists('normalizeStorageUrlForDisplay')) {
function normalizeStorageUrlForDisplay($url) {
    if (empty($url)) return $url;
    $appHost = parse_url(APP_URL, PHP_URL_HOST);
    $urlHost = parse_url($url, PHP_URL_HOST);
    if ($urlHost && $appHost && $urlHost !== $appHost) return $url;
    if (preg_match('#/storage/(.+)#', $url, $m)) return '/storage/' . $m[1];
    return $url;
}
}

/**
 * Generate responsive image srcset and sizes attributes
 * @param string|array $imageData - Either a JSON string or array of responsive image data
 * @param string $fallbackUrl - Fallback URL if responsive data is not available
 * @param string $context - Context for sizing: 'list' (projects list), 'cv' (CV page), 'full' (full width)
 * @return array - ['srcset' => string, 'sizes' => string, 'src' => string]
 */
if (!function_exists('getResponsiveImageAttributes')) {
function getResponsiveImageAttributes($imageData, $fallbackUrl = '', $context = 'full') {
    $responsive = [];

    $normalizeStorageUrl = function($url) {
        if (empty($url)) return $url;
        $appHost = parse_url(APP_URL, PHP_URL_HOST);
        $urlHost = parse_url($url, PHP_URL_HOST);
        if ($urlHost && $appHost && $urlHost !== $appHost) return $url;
        if (preg_match('#/storage/(.+)#', $url, $m)) return '/storage/' . $m[1];
        return $url;
    };

    // Parse JSON if string
    if (is_string($imageData) && !empty($imageData)) {
        $decoded = json_decode($imageData, true);
        // Check if JSON decode failed or returned null/empty/invalid
        if (json_last_error() !== JSON_ERROR_NONE || empty($decoded) || !is_array($decoded)) {
            $responsive = []; // Treat as empty, will trigger on-the-fly generation
        } else {
            $responsive = $decoded;
        }
    } elseif (is_array($imageData)) {
        $responsive = $imageData;
    }
    
    // If no responsive data, check if responsive images exist in file system
    if (empty($responsive) && !empty($fallbackUrl)) {
        // Check if fallback URL points to storage (local or production)
        // Extract path after /storage/ (e.g., /storage/projects/userid/file.jpg -> projects/userid/file.jpg)
        if (preg_match('#/storage/(.+)#', $fallbackUrl, $matches)) {
            $relativePath = $matches[1]; // e.g., "projects/userid/file.jpg"
            $originalFileName = basename($relativePath);
            $fullPath = STORAGE_PATH . '/' . $relativePath; // STORAGE_PATH already ends with /storage, so this is correct
            
            // Extract base name and extension
            $pathInfo = pathinfo($originalFileName);
            $baseName = $pathInfo['filename'];
            $ext = $pathInfo['extension'] ?? 'jpg';
            
            // Extract bucket and userId from relative path (e.g., "projects/userid/file.jpg")
            $pathParts = explode('/', $relativePath);
            $bucket = $pathParts[0] ?? 'projects';
            $userId = $pathParts[1] ?? '';
            
            // Check if URL is production (different domain) or local
            $isProductionUrl = (strpos($fallbackUrl, 'https://') === 0 || strpos($fallbackUrl, 'http://') === 0) && strpos($fallbackUrl, APP_URL) === false;
            
            // If original file exists locally, check for responsive versions
            if (file_exists($fullPath)) {
                require_once __DIR__ . '/storage.php';
                
                $storageDir = STORAGE_PATH . '/' . $bucket . '/' . $userId;
                
                // Define responsive sizes
                $sizes = [
                    'thumb' => ['width' => 150, 'height' => 150],
                    'small' => ['width' => 400, 'height' => 400],
                    'medium' => ['width' => 800, 'height' => 800],
                    'large' => ['width' => 1200, 'height' => 1200]
                ];
                
                $foundResponsive = [];
                foreach ($sizes as $sizeName => $dimensions) {
                    $resizedFileName = $baseName . '_' . $sizeName . '.' . $ext;
                    $resizedPath = $storageDir . '/' . $resizedFileName;
                    
                    // Check if responsive image already exists
                    if (file_exists($resizedPath)) {
                        $foundResponsive[$sizeName] = [
                            'path' => $bucket . '/' . $userId . '/' . $resizedFileName,
                            'width' => $dimensions['width'],
                            'height' => $dimensions['height']
                        ];
                    } elseif (extension_loaded('gd')) {
                        // Generate if doesn't exist and GD is available
                        if (resizeImage($fullPath, $resizedPath, $dimensions['width'], $dimensions['height'])) {
                            $foundResponsive[$sizeName] = [
                                'path' => $bucket . '/' . $userId . '/' . $resizedFileName,
                                'width' => $dimensions['width'],
                                'height' => $dimensions['height']
                            ];
                        }
                    }
                }
                
                if (!empty($foundResponsive)) {
                    $responsive = $foundResponsive;
                }
            } elseif ($isProductionUrl) {
                // For production URLs, generate responsive URLs based on naming convention
                // Even if files don't exist locally, we can still generate the URLs
                // The browser will handle 404s gracefully
                $baseUrl = preg_replace('#(/storage/.+)/([^/]+)$#', '$1', $fallbackUrl);
                
                // Define responsive sizes
                $sizes = [
                    'thumb' => ['width' => 150, 'height' => 150],
                    'small' => ['width' => 400, 'height' => 400],
                    'medium' => ['width' => 800, 'height' => 800],
                    'large' => ['width' => 1200, 'height' => 1200]
                ];
                
                $foundResponsive = [];
                foreach ($sizes as $sizeName => $dimensions) {
                    $resizedFileName = $baseName . '_' . $sizeName . '.' . $ext;
                    $responsiveUrl = $baseUrl . '/' . $resizedFileName;
                    
                    // Store as URL format (not path) for production URLs
                    $foundResponsive[$sizeName] = [
                        'url' => $responsiveUrl,
                        'width' => $dimensions['width'],
                        'height' => $dimensions['height']
                    ];
                }
                
                if (!empty($foundResponsive)) {
                    $responsive = $foundResponsive;
                }
            }
        }
        
        // If still no responsive data, return fallback
        if (empty($responsive)) {
            return [
                'srcset' => '',
                'sizes' => '',
                'src' => $normalizeStorageUrl($fallbackUrl)
            ];
        }
    } elseif (empty($responsive)) {
        return [
            'srcset' => '',
            'sizes' => '',
            'src' => $normalizeStorageUrl($fallbackUrl)
        ];
    }
    
    // Build srcset (small to large)
    $srcsetParts = [];
    $sizes = [];
    
    $normalizeUrl = function($url, $path = null) {
        if ($path) return '/storage/' . $path;
        if (empty($url)) return null;
        $appHost = parse_url(APP_URL, PHP_URL_HOST);
        $urlHost = parse_url($url, PHP_URL_HOST);
        if ($urlHost && $appHost && $urlHost !== $appHost) return $url;
        if (preg_match('#/storage/(.+)#', $url, $m)) return '/storage/' . $m[1];
        return $url;
    };
    
    // Order: thumb, small, medium, large
    $order = ['thumb', 'small', 'medium', 'large'];
    foreach ($order as $size) {
        if (isset($responsive[$size])) {
            // Support both old format (with 'url') and new format (with 'path' only)
            $imageUrl = $normalizeUrl(
                $responsive[$size]['url'] ?? null,
                $responsive[$size]['path'] ?? null
            );
            
            if ($imageUrl) {
                $width = $responsive[$size]['width'] ?? 0;
                if ($width > 0) {
                    $srcsetParts[] = $imageUrl . ' ' . $width . 'w';
                }
            }
        }
    }
    
    // Use the largest available image as fallback
    $fallback = $fallbackUrl;
    if (!empty($responsive['large'])) {
        $fallback = $normalizeUrl(
            $responsive['large']['url'] ?? null,
            $responsive['large']['path'] ?? null
        ) ?: $fallback;
    } elseif (!empty($responsive['medium'])) {
        $fallback = $normalizeUrl(
            $responsive['medium']['url'] ?? null,
            $responsive['medium']['path'] ?? null
        ) ?: $fallback;
    } elseif (!empty($responsive['small'])) {
        $fallback = $normalizeUrl(
            $responsive['small']['url'] ?? null,
            $responsive['small']['path'] ?? null
        ) ?: $fallback;
    } elseif (!empty($responsive['thumb'])) {
        $fallback = $normalizeUrl(
            $responsive['thumb']['url'] ?? null,
            $responsive['thumb']['path'] ?? null
        ) ?: $fallback;
    }
    
    $srcset = implode(', ', $srcsetParts);
    
    // Context-aware sizes attribute
    // For 'list': Image is md:w-48 (192px) on medium+ screens, full width on mobile
    // For 'cv': Image is w-full (100vw) on all screens
    // For 'full': Default to full width with reasonable max
    switch ($context) {
        case 'list':
            // Projects list: 192px on md+ screens, 100vw on mobile
            // Account for 2x displays: 192px * 2 = 384px, so small (400w) is appropriate
            $sizesAttr = '(max-width: 768px) 100vw, 192px';
            break;
        case 'cv':
            // CV page: Container is max-w-6xl (1152px) with px-4 sm:px-6 lg:px-8 padding
            // Project images are in the right column (lg:col-span-2, takes 2/3 width on large screens)
            // Actual rendered widths at different breakpoints:
            // - Mobile (< 640px): Container is ~calc(100vw - 2rem), image is w-full → small (400w) or medium (800w)
            // - Tablet (640-1024px): Container is ~calc(100vw - 3rem), image is w-full → medium (800w) or large (1200w)
            // - Desktop (> 1024px): Container is max-w-6xl, right column is 2/3 width (~700px) → large (1200w)
            // More specific sizes help browser choose appropriate image when viewport changes
            $sizesAttr = '(max-width: 640px) 100vw, (max-width: 1024px) 100vw, (max-width: 1280px) 700px, 700px';
            break;
        default:
            // Default: Full width with reasonable max
            $sizesAttr = '(max-width: 640px) 100vw, (max-width: 1024px) 100vw, 800px';
    }
    
    return [
        'srcset' => $srcset,
        'sizes' => $sizesAttr,
        'src' => $fallback
    ];
}
} // End function_exists check
