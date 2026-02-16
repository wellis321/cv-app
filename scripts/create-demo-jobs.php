<?php
/**
 * Create Demo Jobs for Showcase Account
 *
 * Adds demo job applications to the noreply@simple-job-tracker.com account
 * (username: simple-cv-example) for showcasing cover letter generation,
 * AI CV tailoring, keywords, and job tracking features.
 *
 * Run from command line: php scripts/create-demo-jobs.php
 *
 * Prerequisite: Run php scripts/create-example-cv.php first to create the demo account.
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/database.php';
require_once __DIR__ . '/../php/utils.php';

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

echo "Demo Jobs Creator for Showcase Account\n";
echo "=====================================\n\n";

$email = 'noreply@simple-job-tracker.com';
$profile = db()->fetchOne("SELECT id FROM profiles WHERE email = ?", [$email]);

if (!$profile) {
    die("Demo account not found. Please run: php scripts/create-example-cv.php first.\n");
}

$userId = $profile['id'];
echo "Found demo account (noreply@simple-job-tracker.com)\n\n";

// Check for existing demo jobs
$existing = db()->fetchAll("SELECT id FROM job_applications WHERE user_id = ?", [$userId]);
if (count($existing) > 0) {
    echo "This account already has " . count($existing) . " job application(s).\n";
    echo "Do you want to delete them and add fresh demo jobs? (y/n): ";
    $confirm = trim(fgets(STDIN));
    if (strtolower($confirm) !== 'y') {
        die("Cancelled.\n");
    }
    db()->delete('job_applications', 'user_id = ?', [$userId]);
    echo "Deleted existing job applications.\n\n";
}

$now = date('Y-m-d H:i:s');

// Job 1: Senior Marketing Manager - Applied, with keywords
$job1Id = generateUuid();
db()->insert('job_applications', [
    'id' => $job1Id,
    'user_id' => $userId,
    'company_name' => 'TechForward Ltd',
    'job_title' => 'Senior Marketing Manager',
    'job_description' => "We're looking for a Senior Marketing Manager to lead our B2B marketing strategy and team.

**Key responsibilities:**
- Develop and execute integrated marketing campaigns across digital and traditional channels
- Lead brand positioning and messaging for UK and European markets
- Manage marketing budget and report on campaign performance and ROI
- Collaborate with sales, product, and leadership teams to align marketing with business goals
- Build and mentor a small marketing team
- Drive demand generation through SEO, PPC, content marketing, and events

**Requirements:**
- 8+ years of marketing experience, ideally in B2B SaaS or technology
- Strong track record in digital marketing, campaign management, and team leadership
- Experience with marketing automation (HubSpot, Marketo) and analytics (GA4)
- Excellent communication and project management skills
- Degree or equivalent professional qualification in marketing",
    'status' => 'applied',
    'application_date' => date('Y-m-d', strtotime('-5 days')),
    'salary_range' => '£55,000 - £70,000',
    'job_location' => 'London (Hybrid)',
    'remote_type' => 'hybrid',
    'application_url' => 'https://example.com/jobs/senior-marketing-manager',
    'notes' => "Applied via company website. Recruiter mentioned they're looking to hire within 4-6 weeks. Follow up in 2 weeks if no response.",
    'extracted_keywords' => json_encode(['marketing strategy', 'digital marketing', 'B2B', 'campaign management', 'brand positioning', 'team leadership', 'HubSpot', 'SEO', 'PPC', 'content marketing', 'demand generation', 'marketing automation']),
    'selected_keywords' => json_encode(['marketing strategy', 'digital marketing', 'B2B', 'campaign management', 'team leadership', 'content marketing', 'demand generation']),
    'priority' => 'high',
    'created_at' => $now,
    'updated_at' => $now
]);
echo "✓ Created: Senior Marketing Manager at TechForward Ltd\n";

// Job 2: Digital Marketing Lead - Interviewing, with keywords and cover letter
$job2Id = generateUuid();
db()->insert('job_applications', [
    'id' => $job2Id,
    'user_id' => $userId,
    'company_name' => 'GreenTech Solutions',
    'job_title' => 'Digital Marketing Lead',
    'job_description' => "GreenTech Solutions is seeking a Digital Marketing Lead to drive our growth marketing efforts.

**About the role:**
You will own our digital marketing strategy, from acquisition to conversion. We're a fast-growing cleantech startup with a mission to accelerate the transition to sustainable energy.

**What you'll do:**
- Design and execute multi-channel digital marketing campaigns
- Own our content strategy, including blog, email, and social
- Optimise conversion funnels and landing pages
- Work with data to inform decisions and report on marketing metrics
- Support product launches and partnership announcements

**What we're looking for:**
- 6+ years in digital marketing with strong B2B experience
- Expertise in content marketing, SEO, paid social, and email
- Data-driven mindset; comfortable with Google Analytics and similar tools
- Passion for sustainability and clean technology
- Self-starter who thrives in a fast-paced startup environment",
    'status' => 'interviewing',
    'application_date' => date('Y-m-d', strtotime('-3 weeks')),
    'salary_range' => '£50,000 - £65,000',
    'job_location' => 'Edinburgh (Remote)',
    'remote_type' => 'remote',
    'application_url' => 'https://example.com/jobs/digital-marketing-lead',
    'notes' => "First interview completed. Second interview scheduled for next week. Prepare case study on content strategy.",
    'next_follow_up' => date('Y-m-d', strtotime('+5 days')) . ' 14:00:00',
    'had_interview' => 1,
    'extracted_keywords' => json_encode(['digital marketing', 'content strategy', 'SEO', 'B2B', 'conversion', 'analytics', 'email marketing', 'paid social', 'growth marketing', 'startup']),
    'selected_keywords' => json_encode(['digital marketing', 'content strategy', 'SEO', 'B2B', 'analytics', 'email marketing', 'growth marketing']),
    'priority' => 'high',
    'created_at' => $now,
    'updated_at' => $now
]);
echo "✓ Created: Digital Marketing Lead at GreenTech Solutions\n";

// Pre-generated cover letter for Job 2
$coverLetterId = generateUuid();
db()->insert('cover_letters', [
    'id' => $coverLetterId,
    'user_id' => $userId,
    'job_application_id' => $job2Id,
    'cover_letter_text' => "Dear Hiring Manager,

**About Me**

I am a marketing and communications professional with over 12 years of experience in digital marketing, content strategy, and brand development. I am excited to apply for the Digital Marketing Lead role at GreenTech Solutions, where I can combine my expertise in growth marketing with my interest in sustainability and clean technology.

**Why GreenTech Solutions?**

Your mission to accelerate the transition to sustainable energy resonates strongly with me. I have followed GreenTech's progress in the cleantech space and admire your commitment to innovation and impact. I am drawn to the opportunity to contribute to meaningful work while applying my skills in digital marketing to drive growth and engagement.

**Why Me**

In my current role as Senior Marketing Manager at TechForward Ltd, I have led digital marketing strategy across SEO, content, email, and paid channels—resulting in a 40% increase in qualified leads. I bring hands-on experience with analytics, conversion optimisation, and content strategy, and I thrive in fast-paced, mission-driven environments. I would welcome the chance to discuss how I can support GreenTech Solutions' growth and marketing goals.

Yours sincerely,
Example Account",
    'generated_at' => $now,
    'last_edited_at' => $now,
    'created_at' => $now,
    'updated_at' => $now
]);
echo "✓ Added pre-generated cover letter for Digital Marketing Lead\n";

// Job 3: Content Strategist - Interested (not yet applied)
$job3Id = generateUuid();
db()->insert('job_applications', [
    'id' => $job3Id,
    'user_id' => $userId,
    'company_name' => 'Creative Agency Scotland',
    'job_title' => 'Content Strategist',
    'job_description' => "Creative Agency Scotland is hiring a Content Strategist to shape content for our clients across tech, culture, and education.

**Role overview:**
- Develop content strategies aligned with client objectives and audience insights
- Create editorial calendars, content frameworks, and tone-of-voice guidelines
- Write and edit long-form content, including blogs, case studies, and thought leadership
- Work with designers and developers to deliver integrated campaigns
- Measure content performance and iterate based on data

**Ideal candidate:**
- 5+ years in content strategy, editorial, or marketing
- Strong writing and editing skills
- Experience with content management systems and analytics
- Collaborative mindset; comfortable presenting to clients
- Interest in technology, culture, and education sectors",
    'status' => 'interested',
    'application_date' => null,
    'salary_range' => '£45,000 - £55,000',
    'job_location' => 'Glasgow (Hybrid)',
    'remote_type' => 'hybrid',
    'application_url' => 'https://example.com/jobs/content-strategist',
    'notes' => "Closing date: 2 weeks. Need to tailor CV and prepare cover letter. Strong match for content and strategy experience.",
    'next_follow_up' => date('Y-m-d', strtotime('+10 days')),
    'priority' => 'medium',
    'created_at' => $now,
    'updated_at' => $now
]);
echo "✓ Created: Content Strategist at Creative Agency Scotland\n";

// Add an application question to Job 1 (showcase application questions feature)
$qId = generateUuid();
db()->insert('job_application_questions', [
    'id' => $qId,
    'job_application_id' => $job1Id,
    'user_id' => $userId,
    'question_text' => 'What experience do you have leading marketing teams and what is your approach to mentoring?',
    'answer_text' => null,
    'answer_instructions' => 'Max 150 words. Use 1-2 concrete examples.',
    'sort_order' => 0,
    'created_at' => $now,
    'updated_at' => $now
]);
echo "✓ Added sample application question to Senior Marketing Manager\n";

echo "\n✓ Demo jobs created successfully!\n";
echo "\nSummary:\n";
echo "  - 3 job applications (varied statuses: applied, interviewing, interested)\n";
echo "  - Extracted & selected keywords on 2 jobs (ready for AI CV tailoring)\n";
echo "  - 1 pre-generated cover letter (Digital Marketing Lead)\n";
echo "  - 1 application question (can use AI to generate answer)\n";
echo "\nLogin: noreply@simple-job-tracker.com\n";
echo "Password: ExampleAccount123!\n";
echo "Content Editor (Jobs): " . (defined('APP_URL') ? APP_URL : 'https://yoursite.com') . "/content-editor.php#jobs\n";
echo "\nYou can now generate cover letters and AI CVs for these jobs in the app.\n";
