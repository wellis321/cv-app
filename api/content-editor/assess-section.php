<?php
/**
 * API endpoint for section-specific AI assessment
 */

// Set error handler to catch errors before they output HTML
set_error_handler(function($severity, $message, $file, $line) {
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
});

// Start output buffering to catch any unexpected output
ob_start();

header('Content-Type: application/json');

/**
 * Build section-specific recommendations prompt
 * @param array $options Optional: work_experience_id, single_entry (true when assessing one work experience only)
 */
function buildSectionRecommendationsPrompt($sectionId, $sectionData, $fullCvData, $options = []) {
    $sectionNames = [
        'professional-summary' => 'Professional Summary',
        'work-experience' => 'Work Experience',
        'skills' => 'Skills',
        'projects' => 'Projects',
        'qualification-equivalence' => 'Professional Qualification Equivalence',
        'interests' => 'Interests & Activities'
    ];
    
    $sectionName = $sectionNames[$sectionId] ?? ucfirst(str_replace('-', ' ', $sectionId));
    $singleWorkEntry = ($sectionId === 'work-experience' && !empty($options['single_entry']));
    $singleProjectEntry = ($sectionId === 'projects' && !empty($options['single_entry']));
    $singleQualEntry = ($sectionId === 'qualification-equivalence' && !empty($options['single_entry']));
    $singleInterestEntry = ($sectionId === 'interests' && !empty($options['single_entry']));
    
    $prompt = "You are a CV section advisor. Your response MUST be valid JSON only. Do not include any markdown formatting, explanatory text, or code blocks. Return ONLY a valid JSON object.\n\n";
    
    $prompt .= "CRITICAL: Use British English spelling throughout (e.g., 'organise' not 'organize', 'emphasise' not 'emphasize', 'colour' not 'color', 'centre' not 'center', 'realise' not 'realize', 'recognise' not 'recognize', 'analyse' not 'analyze', 'favour' not 'favor', 'honour' not 'honor', 'labour' not 'labor', 'neighbour' not 'neighbor').\n\n";
    
    if ($singleWorkEntry) {
        $prompt .= "Focus ONLY on the ONE work experience entry provided below. Do NOT assess any other work experience entries. Your assessment and suggested_replacement must refer solely to this single entry.\n\n";
    } elseif ($singleProjectEntry) {
        $prompt .= "Focus ONLY on the ONE project entry provided below. Do NOT assess any other projects. Your assessment and suggested_replacement must refer solely to this single entry.\n\n";
    } elseif ($singleQualEntry) {
        $prompt .= "Focus ONLY on the ONE qualification equivalence entry provided below. Do NOT assess any other entries. Your assessment and suggested_replacement must refer solely to this single entry.\n\n";
    } elseif ($singleInterestEntry) {
        $prompt .= "Focus ONLY on the ONE interest/activity entry provided below. Do NOT assess any other entries. Your assessment and suggested_replacement must refer solely to this single entry.\n\n";
    } else {
        $prompt .= "Focus ONLY on the {$sectionName} section of this CV. Do NOT assess other sections.\n\n";
    }
    
    // Format section data for prompt (strip _single_entry_id if present so formatter sees normal shape)
    $dataForFormat = $sectionData;
    unset($dataForFormat['_single_entry_id']);
    $sectionText = formatSectionForPrompt($sectionId, $dataForFormat, $fullCvData);
    $prompt .= "{$sectionName} Section Data:\n" . $sectionText . "\n\n";
    
    $prompt .= "Analyse ONLY this section and provide:\n";
    $prompt .= "1. Strengths - What is good about this section (array of strings, max 5)\n";
    $prompt .= "2. Weaknesses - What needs improvement in this section (array of strings, max 5)\n";
    $prompt .= "3. Recommendations - Specific, actionable recommendations to improve this section (array of strings, max 5)\n";
    $prompt .= "4. Suggested Replacement - A complete, improved version of this section content based on your assessment. This should be ready to use and incorporate all your recommendations. Use British English spelling.\n\n";
    
    // Section-specific guidance
    switch ($sectionId) {
        case 'professional-summary':
            $prompt .= "For Professional Summary, focus on:\n";
            $prompt .= "- Clarity and conciseness (2-4 sentences ideal)\n";
            $prompt .= "- Use of quantifiable achievements (numbers, percentages, metrics)\n";
            $prompt .= "- Relevance to target roles\n";
            $prompt .= "- Use of keywords relevant to the industry\n";
            $prompt .= "- Professional tone and impact\n";
            $prompt .= "- Connection to work experience (if available)\n\n";
            break;
        case 'work-experience':
            if ($singleWorkEntry) {
                $prompt .= "For this single Work Experience entry, focus on BOTH the main description AND the key responsibilities:\n";
                $prompt .= "- When the entry has Key Responsibilities, your strengths/weaknesses/recommendations MUST include at least one item that explicitly addresses them (e.g. quality of responsibility bullets, use of action verbs in responsibilities, clarity of categories).\n";
            } else {
                $prompt .= "For the Work Experience section as a whole, focus on:\n";
            }
            $prompt .= "- Completeness (dates, company, title, description)\n";
            $prompt .= "- Use of action verbs and quantifiable achievements\n";
            $prompt .= "- Key responsibilities: relevance, impact, use of action verbs, and clarity of each category and item\n";
            $prompt .= "- Consistency in formatting\n";
            $prompt .= "- Clarity of descriptions and responsibility text\n\n";
            break;
        case 'skills':
            $prompt .= "For Skills, focus on:\n";
            $prompt .= "- Relevance to target roles\n";
            $prompt .= "- Organization and categorization\n";
            $prompt .= "- Specificity (specific tools, technologies, methodologies)\n";
            $prompt .= "- Balance between technical and soft skills\n";
            $prompt .= "- Alignment with work experience\n\n";
            break;
        case 'projects':
            if ($singleProjectEntry) {
                $prompt .= "For this single Project entry, focus on:\n";
            } else {
                $prompt .= "For the Projects section as a whole, focus on:\n";
            }
            $prompt .= "- Clarity of project title and description\n";
            $prompt .= "- Use of action verbs and quantifiable outcomes (scope, impact, technologies)\n";
            $prompt .= "- Relevance to target roles and demonstration of skills\n";
            $prompt .= "- Conciseness and readability\n";
            $prompt .= "- British English spelling\n\n";
            break;
        case 'qualification-equivalence':
            if ($singleQualEntry) {
                $prompt .= "For this single Qualification Equivalence entry, focus on:\n";
            } else {
                $prompt .= "For the Qualification Equivalence section as a whole, focus on:\n";
            }
            $prompt .= "- Clarity of the stated level (e.g. Masters, Bachelor's equivalent)\n";
            $prompt .= "- How well the description supports equivalence (evidence, experience, outcomes)\n";
            $prompt .= "- Use of concrete experience and professional development\n";
            $prompt .= "- Professional tone and relevance to recruiters/employers\n";
            $prompt .= "- British English spelling\n\n";
            break;
        case 'interests':
            if ($singleInterestEntry) {
                $prompt .= "For this single Interest/Activity entry, focus on:\n";
            } else {
                $prompt .= "For the Interests & Activities section as a whole, focus on:\n";
            }
            $prompt .= "- Clarity and appeal of the interest name\n";
            $prompt .= "- How well the description brings the interest to life (specificity, relevance)\n";
            $prompt .= "- Balance of personal vs professionally relevant interests\n";
            $prompt .= "- Conciseness and readability\n";
            $prompt .= "- British English spelling\n\n";
            break;
    }
    
    $prompt .= "CRITICAL: Your recommendations must:\n";
    $prompt .= "- Be specific and actionable\n";
    $prompt .= "- Focus ONLY on this section\n";
    $prompt .= "- NOT mention other sections (education, certifications, etc.)\n";
    $prompt .= "- Provide concrete examples when possible\n";
    $prompt .= "- Use British English spelling throughout\n\n";
    
    // Section-specific replacement guidance
    switch ($sectionId) {
        case 'professional-summary':
            $prompt .= "For Suggested Replacement:\n";
            $prompt .= "- Write a complete professional summary (2-4 sentences)\n";
            $prompt .= "- Include quantifiable achievements from work experience if available\n";
            $prompt .= "- Use strong action verbs and professional language\n";
            $prompt .= "- Make it impactful and relevant\n";
            $prompt .= "- Use British English spelling\n\n";
            break;
        case 'work-experience':
            if ($singleWorkEntry) {
                $prompt .= "For Suggested Replacement (this one entry only):\n";
                $prompt .= "- Provide a single object with 'title' (or position), 'company', 'description', and 'responsibility_categories'.\n";
                $prompt .= "- 'description': improved main description with action verbs and quantifiable achievements.\n";
                $prompt .= "- 'responsibility_categories': REQUIRED when the section data includes Key Responsibilities. Array of { \"name\": \"Category name\", \"items\": [ {\"content\": \"...\"}, ... ] } with improved, concise bullet-style text for each responsibility. Match or improve the structure of the original entry. Do not return suggested_replacement with only description/title/company when the entry has responsibilities—you must include improved responsibility_categories.\n";
                $prompt .= "- If the entry has no key responsibilities, you may set responsibility_categories to an empty array.\n";
                $prompt .= "- Use British English spelling throughout.\n\n";
            } else {
                $prompt .= "Do NOT provide suggested_replacement for the whole section. Set \"suggested_replacement\" to null.\n\n";
            }
            break;
        case 'skills':
            $prompt .= "For Suggested Replacement:\n";
            $prompt .= "- DO NOT simply copy the user's existing skills. The suggested replacement must ADD VALUE.\n";
            $prompt .= "- Suggest ONLY: (a) new skills to consider adding that they don't already have, or (b) rephrased/keyword alternatives for better ATS match, or (c) better categorisation of existing skills.\n";
            $prompt .= "- If their current skills are already strong and well-organised, suggest a SHORT list of 2–5 additional skills they could add for their target roles, or set suggested_replacement to null.\n";
            $prompt .= "- When you do suggest skills, format as an array of objects with 'name', 'level', and 'category'. Each skill MUST have a category.\n";
            $prompt .= "- Use British English spelling.\n\n";
            break;
        case 'qualification-equivalence':
            if ($singleQualEntry) {
                $prompt .= "For Suggested Replacement (this one entry only):\n";
                $prompt .= "- Provide a single object with 'level' and 'description'\n";
                $prompt .= "- Strengthen the description with evidence and outcomes where possible\n";
                $prompt .= "- Use British English spelling\n\n";
            } else {
                $prompt .= "Do NOT provide suggested_replacement for the whole section. Set \"suggested_replacement\" to null.\n\n";
            }
            break;
        case 'interests':
            if ($singleInterestEntry) {
                $prompt .= "For Suggested Replacement (this one entry only):\n";
                $prompt .= "- Provide a single object with 'name' and 'description'\n";
                $prompt .= "- Make the interest name clear and the description engaging\n";
                $prompt .= "- Use British English spelling\n\n";
            } else {
                $prompt .= "Do NOT provide suggested_replacement for the whole section. Set \"suggested_replacement\" to null.\n\n";
            }
            break;
        case 'projects':
            if ($singleProjectEntry) {
                $prompt .= "For Suggested Replacement (this one entry only):\n";
                $prompt .= "- Provide a single object with 'title', 'description', and optionally 'url', 'start_date', 'end_date'\n";
                $prompt .= "- Improve the title and description with action verbs and quantifiable outcomes\n";
                $prompt .= "- Use British English spelling\n\n";
            } else {
                $prompt .= "Do NOT provide suggested_replacement for the whole section. Set \"suggested_replacement\" to null.\n\n";
            }
            break;
    }
    
    $prompt .= "Required JSON format:\n";
    $prompt .= "{\n";
    $prompt .= "  \"strengths\": [\"...\", \"...\"],\n";
    $prompt .= "  \"weaknesses\": [\"...\", \"...\"],\n";
    $prompt .= "  \"recommendations\": [\"...\", \"...\"],\n";
    
    // Section-specific format for suggested_replacement
    switch ($sectionId) {
        case 'professional-summary':
            $prompt .= "  \"suggested_replacement\": \"A complete professional summary as a single string (2-4 sentences)\"\n";
            break;
        case 'work-experience':
            if ($singleWorkEntry) {
                $prompt .= "  \"suggested_replacement\": {\"title\": \"...\", \"company\": \"...\", \"description\": \"...\", \"responsibility_categories\": [{\"name\": \"...\", \"items\": [{\"content\": \"...\"}]}]}\n";
                $prompt .= "  When the entry has Key Responsibilities, responsibility_categories MUST be present and non-empty in suggested_replacement.\n";
            } else {
                $prompt .= "  \"suggested_replacement\": null\n";
            }
            break;
        case 'skills':
            $prompt .= "  \"suggested_replacement\": [{\"name\": \"...\", \"level\": \"...\", \"category\": \"...\"}, ...] OR null if no meaningful additions\n";
            $prompt .= "  CRITICAL: suggested_replacement must NOT duplicate the user's existing skills. Only suggest NEW skills to add, or null.\n";
            break;
        case 'qualification-equivalence':
            if ($singleQualEntry) {
                $prompt .= "  \"suggested_replacement\": {\"level\": \"...\", \"description\": \"...\"}\n";
            } else {
                $prompt .= "  \"suggested_replacement\": null\n";
            }
            break;
        case 'interests':
            if ($singleInterestEntry) {
                $prompt .= "  \"suggested_replacement\": {\"name\": \"...\", \"description\": \"...\"}\n";
            } else {
                $prompt .= "  \"suggested_replacement\": null\n";
            }
            break;
        case 'projects':
            if ($singleProjectEntry) {
                $prompt .= "  \"suggested_replacement\": {\"title\": \"...\", \"description\": \"...\", \"url\": \"...\" (optional), \"start_date\": \"...\", \"end_date\": \"...\" (optional)}\n";
            } else {
                $prompt .= "  \"suggested_replacement\": null\n";
            }
            break;
        default:
            $prompt .= "  \"suggested_replacement\": \"...\"\n";
    }
    
    $prompt .= "}\n\n";
    
    $prompt .= "REMEMBER: Return ONLY the JSON object, starting with { and ending with }. No markdown, no explanations, no other text. Focus ONLY on the {$sectionName} section. Use British English spelling throughout.\n";
    $prompt .= "CRITICAL: For professional-summary, suggested_replacement MUST be a plain string, NOT a nested object.\n";
    if ($sectionId === 'work-experience' && $singleWorkEntry) {
        $prompt .= "CRITICAL: For work-experience single entry, when the section data includes Key Responsibilities, suggested_replacement MUST include responsibility_categories with improved text for each category and item. Do not omit them.\n";
    }
    
    return $prompt;
}

/**
 * Convert American spelling to British spelling
 */
function convertToBritishSpelling($data) {
    if (is_string($data)) {
        $data = preg_replace('/\borganize\b/i', 'organise', $data);
        $data = preg_replace('/\borganized\b/i', 'organised', $data);
        $data = preg_replace('/\borganizing\b/i', 'organising', $data);
        $data = preg_replace('/\borganization\b/i', 'organisation', $data);
        $data = preg_replace('/\bemphasize\b/i', 'emphasise', $data);
        $data = preg_replace('/\bemphasized\b/i', 'emphasised', $data);
        $data = preg_replace('/\bemphasizing\b/i', 'emphasising', $data);
        $data = preg_replace('/\bcolor\b/i', 'colour', $data);
        $data = preg_replace('/\bcolors\b/i', 'colours', $data);
        $data = preg_replace('/\bcenter\b/i', 'centre', $data);
        $data = preg_replace('/\bcenters\b/i', 'centres', $data);
        $data = preg_replace('/\brealize\b/i', 'realise', $data);
        $data = preg_replace('/\brealized\b/i', 'realised', $data);
        $data = preg_replace('/\brecognize\b/i', 'recognise', $data);
        $data = preg_replace('/\brecognized\b/i', 'recognised', $data);
        $data = preg_replace('/\banalyze\b/i', 'analyse', $data);
        $data = preg_replace('/\banalyzed\b/i', 'analysed', $data);
        $data = preg_replace('/\bfavor\b/i', 'favour', $data);
        $data = preg_replace('/\bfavors\b/i', 'favours', $data);
        $data = preg_replace('/\bhonor\b/i', 'honour', $data);
        $data = preg_replace('/\bhonors\b/i', 'honours', $data);
        $data = preg_replace('/\blabor\b/i', 'labour', $data);
        $data = preg_replace('/\bneighbor\b/i', 'neighbour', $data);
        $data = preg_replace('/\bneighbors\b/i', 'neighbours', $data);
        return $data;
    } elseif (is_array($data)) {
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = convertToBritishSpelling($value);
        }
        return $result;
    }
    return $data;
}

/**
 * Format section data for prompt
 */
function formatSectionForPrompt($sectionId, $sectionData, $fullCvData) {
    switch ($sectionId) {
        case 'professional-summary':
            $summary = $sectionData['professional_summary'] ?? null;
            if (!$summary) {
                return "No professional summary provided.";
            }
            $text = "Description: " . ($summary['description'] ?? 'Not provided') . "\n";
            if (!empty($summary['strengths'])) {
                $text .= "Strengths:\n";
                foreach ($summary['strengths'] as $strength) {
                    $text .= "- " . ($strength['strength'] ?? '') . "\n";
                }
            }
            return $text;
            
        case 'work-experience':
            $workExp = $sectionData['work_experience'] ?? [];
            if (empty($workExp)) {
                return "No work experience entries.";
            }
            $isSingle = count($workExp) === 1;
            $text = $isSingle ? "Work Experience Entry to assess:\n" : "Work Experience Entries:\n";
            foreach ($workExp as $entry) {
                $position = $entry['position'] ?? $entry['title'] ?? 'No title';
                $company = $entry['company_name'] ?? $entry['company'] ?? 'Unknown company';
                $text .= "- " . $position . " at " . $company;
                if (!empty($entry['start_date'])) {
                    $text .= " (" . ($entry['start_date'] ?? '') . " - " . ($entry['end_date'] ?? 'Present') . ")";
                }
                $text .= "\n";
                if (!empty($entry['description'])) {
                    $text .= "  Description: " . $entry['description'] . "\n";
                }
                $cats = $entry['responsibility_categories'] ?? $entry['responsibilities'] ?? [];
                if (!empty($cats)) {
                    $text .= "  Key Responsibilities:\n";
                    foreach ($cats as $cat) {
                        $catName = $cat['name'] ?? $cat['category'] ?? '';
                        $items = $cat['items'] ?? $cat['responsibilities'] ?? [];
                        $parts = [];
                        foreach ($items as $item) {
                            $parts[] = is_array($item) ? ($item['content'] ?? '') : (string) $item;
                        }
                        $text .= "    - " . $catName . ": " . implode(', ', array_filter($parts)) . "\n";
                    }
                }
                $text .= "\n";
            }
            return $text;
            
        case 'skills':
            $skills = $sectionData['skills'] ?? [];
            if (empty($skills)) {
                return "No skills listed.";
            }
            // Group skills by category for display
            $grouped = [];
            foreach ($skills as $skill) {
                $cat = ($skill['category'] ?? 'Other') ?: 'Other';
                if (!isset($grouped[$cat])) {
                    $grouped[$cat] = [];
                }
                $grouped[$cat][] = $skill;
            }
            $text = "Current Skills (grouped by category):\n";
            foreach ($grouped as $category => $categorySkills) {
                $text .= "\nCategory: {$category}\n";
                foreach ($categorySkills as $skill) {
                    $name = $skill['name'] ?? $skill['skill'] ?? 'Unknown';
                    $level = $skill['level'] ?? $skill['proficiency'] ?? 'Not specified';
                    $text .= "- {$name} ({$level})\n";
                }
            }
            return $text;
            
        case 'qualification-equivalence':
            $quals = $sectionData['qualification_equivalence'] ?? [];
            if (empty($quals)) {
                return "No qualification equivalence entries.";
            }
            $isSingle = count($quals) === 1;
            $text = $isSingle ? "Qualification Equivalence entry to assess:\n" : "Qualification Equivalence entries:\n";
            foreach ($quals as $q) {
                $level = $q['level'] ?? 'Not specified';
                $desc = $q['description'] ?? '';
                $text .= "\nLevel: {$level}\n";
                if ($desc) {
                    $text .= "Description: {$desc}\n";
                }
                $evidence = $q['evidence'] ?? [];
                if (!empty($evidence)) {
                    $text .= "Supporting evidence:\n";
                    foreach ($evidence as $e) {
                        $c = is_array($e) ? ($e['content'] ?? '') : (string) $e;
                        if ($c) {
                            $text .= "- {$c}\n";
                        }
                    }
                }
            }
            return $text;
            
        case 'interests':
            $interests = $sectionData['interests'] ?? [];
            if (empty($interests)) {
                return "No interests or activities listed.";
            }
            $isSingle = count($interests) === 1;
            $text = $isSingle ? "Interest/Activity entry to assess:\n" : "Interests & Activities entries:\n";
            foreach ($interests as $i) {
                $name = $i['name'] ?? 'Not specified';
                $desc = $i['description'] ?? '';
                $text .= "\nName: {$name}\n";
                if ($desc) {
                    $text .= "Description: {$desc}\n";
                }
            }
            return $text;
            
        case 'projects':
            $projects = $sectionData['projects'] ?? [];
            if (empty($projects)) {
                return "No projects listed.";
            }
            $isSingle = count($projects) === 1;
            $text = $isSingle ? "Project entry to assess:\n" : "Projects entries:\n";
            foreach ($projects as $p) {
                $title = $p['title'] ?? 'No title';
                $desc = $p['description'] ?? '';
                $url = $p['url'] ?? '';
                $start = $p['start_date'] ?? '';
                $end = $p['end_date'] ?? '';
                $text .= "\nTitle: {$title}\n";
                if ($start || $end) {
                    $text .= "Dates: " . ($start ?: '?') . " - " . ($end ?: 'Present') . "\n";
                }
                if ($desc) {
                    $text .= "Description: {$desc}\n";
                }
                if ($url) {
                    $text .= "URL: {$url}\n";
                }
            }
            return $text;
            
        default:
            return json_encode($sectionData, JSON_PRETTY_PRINT);
    }
}

try {
    require_once __DIR__ . '/../../php/helpers.php';
    require_once __DIR__ . '/../../php/ai-service.php';
    require_once __DIR__ . '/../../php/cv-data.php';

    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    if (!isPost()) {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
        exit;
    }

    $userId = getUserId();
    $sectionId = post('section_id');
    $workExperienceId = post('work_experience_id') ?: null;
    $projectId = post('project_id') ?: null;
    $qualificationEquivalenceId = post('qualification_equivalence_id') ?: null;
    $interestId = post('interest_id') ?: null;

    if (empty($sectionId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Section ID required']);
        exit;
    }

    // Load CV data
    $cvData = loadCvData($userId);
    
    // Filter data to only include the requested section
    $sectionData = [];
    switch ($sectionId) {
        case 'professional-summary':
            $sectionData = [
                'professional_summary' => $cvData['professional_summary'] ?? null
            ];
            break;
        case 'work-experience':
            $allWork = $cvData['work_experience'] ?? [];
            if ($workExperienceId) {
                $single = null;
                foreach ($allWork as $w) {
                    if (($w['id'] ?? '') === $workExperienceId) {
                        $single = $w;
                        break;
                    }
                }
                $sectionData = [
                    'work_experience' => $single ? [$single] : [],
                    '_single_entry_id' => $workExperienceId
                ];
            } else {
                $sectionData = [
                    'work_experience' => $allWork
                ];
            }
            break;
        case 'skills':
            $sectionData = [
                'skills' => $cvData['skills'] ?? []
            ];
            break;
        case 'qualification-equivalence':
            $allQuals = $cvData['qualification_equivalence'] ?? [];
            if ($qualificationEquivalenceId) {
                $single = null;
                foreach ($allQuals as $q) {
                    if (($q['id'] ?? '') === $qualificationEquivalenceId) {
                        $single = $q;
                        break;
                    }
                }
                $sectionData = [
                    'qualification_equivalence' => $single ? [$single] : [],
                    '_single_entry_id' => $qualificationEquivalenceId
                ];
            } else {
                $sectionData = [
                    'qualification_equivalence' => $allQuals
                ];
            }
            break;
        case 'interests':
            $allInterests = $cvData['interests'] ?? [];
            if ($interestId) {
                $single = null;
                foreach ($allInterests as $i) {
                    if (($i['id'] ?? '') === $interestId) {
                        $single = $i;
                        break;
                    }
                }
                $sectionData = [
                    'interests' => $single ? [$single] : [],
                    '_single_entry_id' => $interestId
                ];
            } else {
                $sectionData = [
                    'interests' => $allInterests
                ];
            }
            break;
        case 'projects':
            $allProjects = $cvData['projects'] ?? [];
            if ($projectId) {
                $single = null;
                foreach ($allProjects as $p) {
                    if (($p['id'] ?? '') === $projectId) {
                        $single = $p;
                        break;
                    }
                }
                $sectionData = [
                    'projects' => $single ? [$single] : [],
                    '_single_entry_id' => $projectId
                ];
            } else {
                $sectionData = [
                    'projects' => $allProjects
                ];
            }
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid section ID']);
            exit;
    }
    
    // Create a minimal CV data structure for assessment
    $assessmentCvData = array_merge($cvData, $sectionData);
    
    // Get AI service
    $aiService = getAIService($userId);
    
    // Build section-specific recommendations prompt
    $options = [];
    if ($sectionId === 'work-experience') {
        $options['work_experience_id'] = $workExperienceId;
        $options['single_entry'] = !empty($sectionData['_single_entry_id']);
    }
    if ($sectionId === 'qualification-equivalence') {
        $options['qualification_equivalence_id'] = $qualificationEquivalenceId;
        $options['single_entry'] = !empty($sectionData['_single_entry_id']);
    }
    if ($sectionId === 'interests') {
        $options['interest_id'] = $interestId;
        $options['single_entry'] = !empty($sectionData['_single_entry_id']);
    }
    if ($sectionId === 'projects') {
        $options['project_id'] = $projectId;
        $options['single_entry'] = !empty($sectionData['_single_entry_id']);
    }
    $prompt = buildSectionRecommendationsPrompt($sectionId, $sectionData, $cvData, $options);
    
    // Call AI with section-specific prompt
    $result = $aiService->assessSectionWithPrompt($prompt);
    
    if (!$result['success']) {
        throw new Exception($result['error'] ?? 'AI assessment failed');
    }
    
    // Check if browser execution is required
    if (isset($result['browser_execution']) && $result['browser_execution']) {
        // Browser AI execution required - return prompt and data for frontend execution
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'browser_execution' => true,
            'prompt' => $prompt,
            'model' => $result['model'] ?? 'llama3.2',
            'model_type' => $result['model_type'] ?? 'webllm',
            'cv_data' => $assessmentCvData,
            'section_id' => $sectionId,
            'message' => 'Browser AI execution required. Frontend will handle this request.'
        ]);
        exit;
    }
    
    // Extract assessment
    $assessment = $result['assessment'] ?? null;
    
    if (!$assessment || !is_array($assessment)) {
        throw new Exception('Failed to parse AI response as valid assessment structure.');
    }
    
    // Return section-specific recommendations
    // Clear any unexpected output
    ob_clean();
    
    // Convert American to British spelling in assessment
    $assessment = convertToBritishSpelling($assessment);
    
    $response = [
        'success' => true,
        'assessment' => [
            'strengths' => array_slice($assessment['strengths'] ?? [], 0, 5),
            'weaknesses' => array_slice($assessment['weaknesses'] ?? [], 0, 5),
            'recommendations' => array_slice($assessment['recommendations'] ?? [], 0, 5),
            'suggested_replacement' => $assessment['suggested_replacement'] ?? null
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Clear any output before sending error
    ob_clean();
    
    error_log("Section assessment error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to assess section: ' . $e->getMessage()]);
} catch (Error $e) {
    // Clear any output before sending error
    ob_clean();
    
    error_log("Section assessment fatal error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to assess section: ' . $e->getMessage()]);
}
