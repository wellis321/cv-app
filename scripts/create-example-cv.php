<?php
/**
 * Create Example CV Data
 *
 * Creates the example CV content for noreply@simple-job-tracker.com (username: simple-cv-example).
 * Run from command line: php scripts/create-example-cv.php [--with-demo-jobs]
 *
 * Use --with-demo-jobs to also add demo job applications for showcasing cover letters,
 * AI CV generation, and job tracking features.
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/database.php';
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/utils.php';

// Only allow running from command line
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

echo "Example CV Data Creator\n";
echo "======================\n\n";

$username = 'simple-cv-example';

// Check if example profile already exists
$existing = db()->fetchOne("SELECT id FROM profiles WHERE username = ?", [$username]);

if ($existing) {
    echo "Example CV profile already exists. Do you want to delete and recreate it? (y/n): ";
    $confirm = trim(fgets(STDIN));
    
    if (strtolower($confirm) !== 'y') {
        die("Cancelled.\n");
    }
    
    // Delete existing data (cascade will handle related records)
    $userId = $existing['id'];
    db()->delete('profiles', 'id = ?', [$userId]);
    echo "Deleted existing example CV data.\n\n";
}

// Create profile
$userId = generateUuid();
// Fixed password for example account: ExampleAccount123!
$passwordHash = hashPassword('ExampleAccount123!');

try {
    db()->insert('profiles', [
        'id' => $userId,
        'email' => 'noreply@simple-job-tracker.com',
        'password_hash' => $passwordHash,
        'full_name' => 'Example Account',
        'username' => $username,
        'phone' => '0745 081 4167',
        'location' => 'Glasgow',
        'linkedin_url' => 'https://www.linkedin.com/company/simple-data-cleaner/',
        'bio' => 'Strategic Marketing Leader | Digital Innovation | Brand Development',
        'photo_url' => '/static/images/example-account-profile-image/profile-image.png',
        'show_photo' => 1,
        'show_photo_pdf' => 1,
        'cv_visibility' => 'public',
        'email_verified' => 1,
        'account_type' => 'individual',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "✓ Created profile\n";
    
    // Create professional summary
    $summaryId = generateUuid();
    db()->insert('professional_summary', [
        'id' => $summaryId,
        'profile_id' => $userId,
        'description' => 'For over 12 years, I have worked as a marketing and communications professional across technology, education, and creative sectors in the UK and Europe. My expertise spans digital marketing strategy, brand development, content creation, and data-driven campaign management. I specialise in building cohesive marketing strategies that combine creative storytelling with analytical insights—driving engagement, growing audiences, and delivering measurable business results. My approach balances strategic vision with hands-on execution, working with leadership teams to shape brand direction while collaborating directly with creative and technical teams to bring campaigns to life. More recently, my focus has expanded to AI-driven marketing automation, personalisation at scale, and sustainable marketing practices—demonstrating how innovation can support growth while maintaining authentic connections with audiences.',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "✓ Created professional summary\n";
    
    // Create work experience
    $workExpId = generateUuid();
    db()->insert('work_experience', [
        'id' => $workExpId,
        'profile_id' => $userId,
        'company_name' => 'TechForward Ltd',
        'position' => 'Senior Marketing Manager',
        'start_date' => '2020-03-01',
        'end_date' => null, // Present
        'description' => 'Led comprehensive marketing strategy for a growing B2B SaaS company, managing brand positioning, digital campaigns, and customer acquisition across UK and European markets.',
        'sort_order' => 0,
        'hide_date' => 0,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "✓ Created work experience\n";
    
    // Create responsibility categories and items for work experience
    // Note: The browser snapshot shows a "View Responsibilities" button, so we'll add some sample responsibilities
    $respCatId = generateUuid();
    db()->insert('responsibility_categories', [
        'id' => $respCatId,
        'work_experience_id' => $workExpId,
        'name' => 'Marketing Strategy',
        'sort_order' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    db()->insert('responsibility_items', [
        'id' => generateUuid(),
        'category_id' => $respCatId,
        'content' => 'Developed and executed comprehensive marketing strategies aligned with business objectives',
        'sort_order' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    db()->insert('responsibility_items', [
        'id' => generateUuid(),
        'category_id' => $respCatId,
        'content' => 'Managed brand positioning and messaging across all marketing channels',
        'sort_order' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    db()->insert('responsibility_items', [
        'id' => generateUuid(),
        'category_id' => $respCatId,
        'content' => 'Oversaw digital campaign execution including SEO, PPC, social media, and email marketing',
        'sort_order' => 2,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Add another category for Campaign Management
    $respCat2Id = generateUuid();
    db()->insert('responsibility_categories', [
        'id' => $respCat2Id,
        'work_experience_id' => $workExpId,
        'name' => 'Campaign Management',
        'sort_order' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    db()->insert('responsibility_items', [
        'id' => generateUuid(),
        'category_id' => $respCat2Id,
        'content' => 'Planned and executed multi-channel marketing campaigns resulting in 40% increase in qualified leads',
        'sort_order' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    db()->insert('responsibility_items', [
        'id' => generateUuid(),
        'category_id' => $respCat2Id,
        'content' => 'Collaborated with sales and product teams to align marketing initiatives with business goals',
        'sort_order' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "✓ Created work experience responsibilities\n";
    
    // Create project
    $projectId = generateUuid();
    db()->insert('projects', [
        'id' => $projectId,
        'profile_id' => $userId,
        'title' => 'AI Marketing Toolkit',
        'description' => 'Developed an open-source collection of prompts and frameworks for marketing professionals to leverage AI tools effectively. The toolkit includes templates for content creation, campaign planning, and audience research, helping marketers integrate AI into their workflows while maintaining strategic thinking and creativity.',
        'start_date' => '2024-11-01',
        'end_date' => null,
        'url' => 'https://simple-cv-builder.com/cv/@simple-cv-example',
        'image_url' => null,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "✓ Created project\n";
    
    // Create certification
    db()->insert('certifications', [
        'id' => generateUuid(),
        'profile_id' => $userId,
        'name' => 'Google Analytics 4 Certification',
        'issuer' => 'Google',
        'date_obtained' => '2024-01-01',
        'expiry_date' => null,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "✓ Created certification\n";
    
    // Create professional qualification equivalence
    $qualEquivId = generateUuid();
    db()->insert('professional_qualification_equivalence', [
        'id' => $qualEquivId,
        'profile_id' => $userId,
        'level' => 'Bachelor\'s Degree Equivalent',
        'description' => 'Over 12 years of professional marketing experience combined with advanced certifications in digital marketing, analytics, and content strategy. My expertise encompasses strategic planning, team leadership, and measurable business impact across multiple sectors, demonstrating depth of knowledge equivalent to formal degree qualifications in marketing and communications.',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "✓ Created professional qualification equivalence\n";
    
    // Create professional membership
    db()->insert('professional_memberships', [
        'id' => generateUuid(),
        'profile_id' => $userId,
        'organisation' => 'Chartered Institute of Marketing (CIM)',
        'role' => 'Member',
        'start_date' => '2019-09-01',
        'end_date' => null, // Present
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "✓ Created professional membership\n";
    
    // Create interests
    $interest1Id = generateUuid();
    db()->insert('interests', [
        'id' => $interest1Id,
        'profile_id' => $userId,
        'name' => 'Photography',
        'description' => 'I\'m passionate about street photography and documenting everyday moments in Edinburgh. I regularly share my work on Instagram and have had several photos featured in local exhibitions.',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    $interest2Id = generateUuid();
    db()->insert('interests', [
        'id' => $interest2Id,
        'profile_id' => $userId,
        'name' => 'Trail Running',
        'description' => 'I enjoy exploring Scotland\'s trails and hills, regularly participating in 10K-25K trail races. Running helps me clear my mind and approach challenges with fresh perspective.',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "✓ Created interests\n";
    
    echo "\n✓ Example CV data created successfully!\n";
    echo "Username: " . $username . "\n";
    echo "Profile ID: " . $userId . "\n";
    echo "View at: " . (defined('APP_URL') ? APP_URL : '') . "/cv/@simple-cv-example\n";
    echo "Login: noreply@simple-job-tracker.com / ExampleAccount123!\n\n";

    // Optionally add demo jobs
    if (in_array('--with-demo-jobs', $argv ?? [])) {
        echo "Adding demo jobs...\n";
        passthru('php ' . escapeshellarg(__DIR__ . '/create-demo-jobs.php'));
    }
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}

