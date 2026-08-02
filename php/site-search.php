<?php
/**
 * Simple keyword search across public blog and help content.
 * Backs the SearchAction structured data (see helpers.php buildStructuredData)
 * so the /?s={search_term_string} URL Google may show as a sitelinks searchbox
 * actually returns results.
 */

function getSiteSearchIndex() {
    return [
        ['title' => 'How to Update Your CV: Complete Guide UK', 'description' => "Step-by-step advice for refreshing your CV, from personal statements to ATS optimisation, so you're always ready for the next opportunity.", 'url' => '/blog/cv-tips/how-to-update-your-cv.php', 'category' => 'CV Tips'],
        ['title' => 'CV Keywords & ATS: Pass Applicant Tracking Systems', 'description' => 'How ATS works and which keywords to add to your CV. UK guide to passing ATS screening and reaching recruiters.', 'url' => '/blog/cv-tips/keywords-and-ats-guide.php', 'category' => 'CV Tips'],
        ['title' => 'CV Update Checklist | Free Printable', 'description' => 'Free CV update checklist for UK job seekers. Systematic guide to refreshing your CV. Printable, nothing missed.', 'url' => '/blog/cv-tips/cv-update-checklist.php', 'category' => 'CV Tips'],
        ['title' => 'Legit Ways to Earn Money Online (UK)', 'description' => 'Make money online from home, 20+ legitimate ways: surveys, side hustles, work from home UK, passive income. No scams.', 'url' => '/blog/career/legitimate-ways-to-earn-money-online.php', 'category' => 'Career'],
        ['title' => 'AI for Job Applications', 'description' => 'Use AI for CVs, cover letters, and interview prep. ChatGPT and Claude tips for UK job seekers.', 'url' => '/blog/job-search/using-ai-in-job-applications.php', 'category' => 'Job Search'],
        ['title' => 'AI Prompts for CVs & Cover Letters', 'description' => 'Copy-paste AI prompts for CV writing, cover letters, and interview prep.', 'url' => '/blog/job-search/ai-prompt-cheat-sheet.php', 'category' => 'Job Search'],
        ['title' => 'How to Refresh Your CV in 30 Minutes', 'description' => 'Update your CV fast. Free 30-minute CV refresh checklist covering your summary, skills, and experience.', 'url' => '/blog/job-search/how-to-refresh-your-cv-in-30-minutes.php', 'category' => 'Job Search'],
        ['title' => 'Work From Home Jobs (UK)', 'description' => '11 work from home jobs for UK beginners, no experience needed. Salaries, skills, where to apply.', 'url' => '/blog/job-search/remote-jobs-begginers.php', 'category' => 'Job Search'],
        ['title' => 'Entry-Level Healthcare Careers (UK)', 'description' => 'Healthcare careers you can start without a degree. Entry-level roles, training, salaries.', 'url' => '/blog/job-search/entry-level-healthcare-careers.php', 'category' => 'Job Search'],
        ['title' => 'AI Model Guide', 'description' => 'Choose the best AI model for CV rewriting, cover letters, and application questions. Works with Ollama and Browser AI.', 'url' => '/help/ai-models.php', 'category' => 'Help'],
        ['title' => 'Frequently Asked Questions', 'description' => 'How to create your CV, pricing, job tracking, AI cover letters, PDF export, and more.', 'url' => '/help/faq.php', 'category' => 'Help'],
        ['title' => 'Extension Setup Guide', 'description' => 'Complete guide for installing and configuring the Simple CV Builder browser extension to save jobs from any website.', 'url' => '/help/setup/extension.php', 'category' => 'Help'],
        ['title' => 'CV Prompt Best Practices', 'description' => 'Learn how to write effective prompts for AI CV rewriting to get better results.', 'url' => '/help/guides/ai-prompts.php', 'category' => 'Help'],
    ];
}

/**
 * @return array List of matching entries (title, description, url, category), best match first
 */
function searchSiteContent($query) {
    $query = trim((string) $query);
    if ($query === '') {
        return [];
    }

    $terms = array_filter(preg_split('/\s+/', mb_strtolower($query)));
    if (empty($terms)) {
        return [];
    }

    $results = [];
    foreach (getSiteSearchIndex() as $item) {
        $titleLower = mb_strtolower($item['title']);
        $haystack = $titleLower . ' ' . mb_strtolower($item['description']) . ' ' . mb_strtolower($item['category']);
        $score = 0;
        foreach ($terms as $term) {
            if (strpos($haystack, $term) !== false) {
                $score += (strpos($titleLower, $term) !== false) ? 2 : 1;
            }
        }
        if ($score > 0) {
            $item['score'] = $score;
            $results[] = $item;
        }
    }

    usort($results, function ($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    return $results;
}
