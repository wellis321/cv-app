<?php
/**
 * Create Test Accounts with Full CVs and Job Applications
 * 
 * This script creates multiple test accounts with complete CVs, work histories,
 * job applications, and full subscriptions for testing purposes.
 * 
 * Run from command line: php scripts/create-test-accounts.php
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/database.php';
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/utils.php';
require_once __DIR__ . '/../php/security.php';
require_once __DIR__ . '/../php/authorisation.php';
require_once __DIR__ . '/../php/job-applications.php';

// Only allow running from command line
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

echo "Test Accounts Creator\n";
echo "=====================\n\n";

// Test account data - multiple accounts with different profiles
$testAccounts = [
    [
        'username' => 'test-marketing',
        'email' => 'test.marketing@example.com',
        'password' => 'TestAccount123!',
        'full_name' => 'Sarah Johnson',
        'phone' => '0745 123 4567',
        'location' => 'London, UK',
        'linkedin_url' => 'https://www.linkedin.com/in/sarah-johnson',
        'bio' => 'Experienced Marketing Manager | Digital Strategy | Brand Development',
        'professional_summary' => 'Strategic marketing leader with over 8 years of experience driving growth through digital campaigns, brand development, and data-driven insights. Proven track record of managing cross-functional teams and delivering measurable results in fast-paced environments.',
        'work_experience' => [
            [
                'company_name' => 'Digital Solutions Ltd',
                'position' => 'Senior Marketing Manager',
                'start_date' => '2020-03-01',
                'end_date' => null,
                'description' => 'Leading marketing strategy for a growing SaaS company, managing brand positioning, digital campaigns, and customer acquisition across UK and European markets.',
                'responsibilities' => [
                    'Digital Strategy' => [
                        'Developed and executed comprehensive digital marketing strategies that increased lead generation by 150%',
                        'Managed multi-channel campaigns including SEO, PPC, social media, and email marketing',
                        'Oversaw brand positioning and messaging across all marketing channels'
                    ],
                    'Team Leadership' => [
                        'Led a team of 5 marketing professionals, fostering collaboration and professional development',
                        'Collaborated with sales and product teams to align marketing initiatives with business goals',
                        'Established KPIs and reporting frameworks to measure campaign effectiveness'
                    ]
                ]
            ],
            [
                'company_name' => 'Creative Agency Group',
                'position' => 'Marketing Manager',
                'start_date' => '2017-06-01',
                'end_date' => '2020-02-28',
                'description' => 'Managed marketing campaigns for multiple B2B and B2C clients, focusing on content strategy and social media engagement.',
                'responsibilities' => [
                    'Campaign Management' => [
                        'Planned and executed marketing campaigns for 15+ clients across various industries',
                        'Developed content strategies that increased client engagement by 80%',
                        'Managed social media presence across multiple platforms'
                    ]
                ]
            ]
        ],
        'education' => [
            [
                'institution' => 'University of Manchester',
                'degree' => 'Bachelor of Arts in Marketing',
                'field_of_study' => 'Marketing',
                'start_date' => '2012-09-01',
                'end_date' => '2016-06-30'
            ]
        ],
        'skills' => [
            ['name' => 'Digital Marketing', 'category' => 'Marketing'],
            ['name' => 'Content Strategy', 'category' => 'Marketing'],
            ['name' => 'SEO/SEM', 'category' => 'Marketing'],
            ['name' => 'Social Media Marketing', 'category' => 'Marketing'],
            ['name' => 'Google Analytics', 'category' => 'Analytics'],
            ['name' => 'Adobe Creative Suite', 'category' => 'Design'],
            ['name' => 'Team Leadership', 'category' => 'Management']
        ],
        'projects' => [
            [
                'title' => 'Digital Transformation Campaign',
                'description' => 'Led a comprehensive digital transformation campaign that modernized brand presence and increased online engagement by 200%.',
                'start_date' => '2021-01-01',
                'end_date' => '2021-12-31',
                'url' => 'https://example.com/projects/digital-transformation'
            ]
        ],
        'certifications' => [
            ['name' => 'Google Analytics Certified', 'issuer' => 'Google', 'date_obtained' => '2020-05-15'],
            ['name' => 'HubSpot Content Marketing', 'issuer' => 'HubSpot', 'date_obtained' => '2019-03-10']
        ],
        'job_applications' => [
            [
                'company_name' => 'TechCorp International',
                'job_title' => 'Head of Marketing',
                'job_description' => 'We are seeking an experienced Head of Marketing to lead our global marketing team. The ideal candidate will have 8+ years of marketing experience, strong leadership skills, and a proven track record in B2B SaaS marketing.',
                'status' => 'interviewing',
                'application_date' => date('Y-m-d', strtotime('-10 days')),
                'salary_range' => '£70,000 - £90,000',
                'job_location' => 'London',
                'remote_type' => 'hybrid',
                'notes' => 'Second interview scheduled for next week'
            ],
            [
                'company_name' => 'Innovative Brands Ltd',
                'job_title' => 'Senior Marketing Manager',
                'job_description' => 'Looking for a Senior Marketing Manager to drive growth through innovative marketing strategies. Must have experience with digital campaigns, team management, and brand development.',
                'status' => 'applied',
                'application_date' => date('Y-m-d', strtotime('-5 days')),
                'salary_range' => '£55,000 - £75,000',
                'job_location' => 'Manchester',
                'remote_type' => 'remote'
            ],
            [
                'company_name' => 'Global Media Group',
                'job_title' => 'Marketing Director',
                'job_description' => 'Marketing Director position available for a strategic leader to oversee our marketing operations across multiple markets. Requires extensive experience in brand management and international marketing.',
                'status' => 'applied',
                'application_date' => date('Y-m-d', strtotime('-2 days')),
                'salary_range' => '£80,000 - £100,000',
                'job_location' => 'London',
                'remote_type' => 'hybrid'
            ]
        ]
    ],
    [
        'username' => 'test-software',
        'email' => 'test.software@example.com',
        'password' => 'TestAccount123!',
        'full_name' => 'Michael Chen',
        'phone' => '0745 234 5678',
        'location' => 'Edinburgh, UK',
        'linkedin_url' => 'https://www.linkedin.com/in/michael-chen',
        'bio' => 'Senior Software Engineer | Full-Stack Development | Cloud Architecture',
        'professional_summary' => 'Full-stack software engineer with 10 years of experience building scalable web applications. Expert in Python, JavaScript, React, and cloud technologies. Passionate about clean code, agile methodologies, and mentoring junior developers.',
        'work_experience' => [
            [
                'company_name' => 'CloudTech Solutions',
                'position' => 'Senior Software Engineer',
                'start_date' => '2019-01-15',
                'end_date' => null,
                'description' => 'Leading development of cloud-based SaaS platforms, working with microservices architecture and modern JavaScript frameworks.',
                'responsibilities' => [
                    'Software Development' => [
                        'Architected and developed microservices-based applications serving 100,000+ users',
                        'Led migration of legacy systems to cloud-native architecture',
                        'Implemented CI/CD pipelines reducing deployment time by 70%'
                    ],
                    'Technical Leadership' => [
                        'Mentored a team of 4 junior developers, conducting code reviews and technical training',
                        'Established coding standards and best practices across development teams',
                        'Collaborated with product and design teams to deliver user-centric solutions'
                    ]
                ]
            ],
            [
                'company_name' => 'Startup Innovations',
                'position' => 'Full-Stack Developer',
                'start_date' => '2016-05-01',
                'end_date' => '2018-12-31',
                'description' => 'Developed web applications from concept to deployment, working across the entire stack.',
                'responsibilities' => [
                    'Full-Stack Development' => [
                        'Built responsive web applications using React, Node.js, and PostgreSQL',
                        'Implemented RESTful APIs and real-time features using WebSockets',
                        'Optimized database queries and application performance'
                    ]
                ]
            ],
            [
                'company_name' => 'Web Development Agency',
                'position' => 'Junior Developer',
                'start_date' => '2014-09-01',
                'end_date' => '2016-04-30',
                'description' => 'Developed websites and web applications for various clients using modern web technologies.',
                'responsibilities' => [
                    'Web Development' => [
                        'Developed custom websites using HTML, CSS, JavaScript, and PHP',
                        'Maintained and updated existing client websites',
                        'Collaborated with designers to implement responsive layouts'
                    ]
                ]
            ]
        ],
        'education' => [
            [
                'institution' => 'University of Edinburgh',
                'degree' => 'Bachelor of Science in Computer Science',
                'field_of_study' => 'Computer Science',
                'start_date' => '2011-09-01',
                'end_date' => '2014-06-30'
            ]
        ],
        'skills' => [
            ['name' => 'Python', 'category' => 'Programming'],
            ['name' => 'JavaScript', 'category' => 'Programming'],
            ['name' => 'React', 'category' => 'Frontend'],
            ['name' => 'Node.js', 'category' => 'Backend'],
            ['name' => 'PostgreSQL', 'category' => 'Database'],
            ['name' => 'AWS', 'category' => 'Cloud'],
            ['name' => 'Docker', 'category' => 'DevOps'],
            ['name' => 'Git', 'category' => 'Tools']
        ],
        'projects' => [
            [
                'title' => 'E-Commerce Platform',
                'description' => 'Led development of a scalable e-commerce platform handling 10,000+ transactions daily using microservices architecture.',
                'start_date' => '2020-06-01',
                'end_date' => '2021-03-31',
                'url' => 'https://github.com/mchen/ecommerce-platform'
            ]
        ],
        'certifications' => [
            ['name' => 'AWS Certified Solutions Architect', 'issuer' => 'Amazon Web Services', 'date_obtained' => '2020-11-20'],
            ['name' => 'Scrum Master Certification', 'issuer' => 'Scrum Alliance', 'date_obtained' => '2019-08-15']
        ],
        'job_applications' => [
            [
                'company_name' => 'BigTech Inc',
                'job_title' => 'Principal Software Engineer',
                'job_description' => 'We are looking for a Principal Software Engineer to lead our platform engineering team. The ideal candidate will have extensive experience with cloud architecture, microservices, and leading technical teams.',
                'status' => 'interviewing',
                'application_date' => date('Y-m-d', strtotime('-7 days')),
                'salary_range' => '£85,000 - £110,000',
                'job_location' => 'London',
                'remote_type' => 'remote',
                'notes' => 'Technical interview completed, waiting for next round'
            ],
            [
                'company_name' => 'FinTech Solutions',
                'job_title' => 'Senior Full-Stack Engineer',
                'job_description' => 'Senior Full-Stack Engineer needed for our growing fintech platform. Must have strong experience with React, Node.js, and cloud technologies.',
                'status' => 'applied',
                'application_date' => date('Y-m-d', strtotime('-3 days')),
                'salary_range' => '£65,000 - £85,000',
                'job_location' => 'Edinburgh',
                'remote_type' => 'hybrid'
            ]
        ]
    ],
    [
        'username' => 'test-design',
        'email' => 'test.design@example.com',
        'password' => 'TestAccount123!',
        'full_name' => 'Emily Rodriguez',
        'phone' => '0745 345 6789',
        'location' => 'Birmingham, UK',
        'linkedin_url' => 'https://www.linkedin.com/in/emily-rodriguez',
        'bio' => 'UX/UI Designer | Product Design | Design Systems',
        'professional_summary' => 'Creative UX/UI designer with 7 years of experience designing intuitive digital products. Expert in user research, prototyping, and design systems. Passionate about creating user-centered designs that solve real problems.',
        'work_experience' => [
            [
                'company_name' => 'Design Studio Pro',
                'position' => 'Senior UX/UI Designer',
                'start_date' => '2018-08-01',
                'end_date' => null,
                'description' => 'Leading design for mobile and web applications, conducting user research, and maintaining design systems for various product teams.',
                'responsibilities' => [
                    'Product Design' => [
                        'Designed user interfaces for mobile and web applications used by 500,000+ users',
                        'Conducted user research and usability testing to inform design decisions',
                        'Created and maintained comprehensive design systems'
                    ],
                    'Collaboration' => [
                        'Collaborated with product managers and developers to deliver cohesive experiences',
                        'Presented design concepts and user research findings to stakeholders',
                        'Mentored junior designers and provided design feedback'
                    ]
                ]
            ],
            [
                'company_name' => 'Creative Agency',
                'position' => 'UI Designer',
                'start_date' => '2016-03-01',
                'end_date' => '2018-07-31',
                'description' => 'Designed user interfaces for various client projects including e-commerce sites and mobile apps.',
                'responsibilities' => [
                    'UI Design' => [
                        'Created high-fidelity mockups and prototypes for client projects',
                        'Designed responsive layouts for websites and mobile applications',
                        'Collaborated with developers to ensure design implementation accuracy'
                    ]
                ]
            ]
        ],
        'education' => [
            [
                'institution' => 'Birmingham City University',
                'degree' => 'Bachelor of Arts in Graphic Design',
                'field_of_study' => 'Graphic Design',
                'start_date' => '2013-09-01',
                'end_date' => '2016-06-30'
            ]
        ],
        'skills' => [
            ['name' => 'Figma', 'category' => 'Design Tools'],
            ['name' => 'Adobe XD', 'category' => 'Design Tools'],
            ['name' => 'Sketch', 'category' => 'Design Tools'],
            ['name' => 'User Research', 'category' => 'UX'],
            ['name' => 'Prototyping', 'category' => 'UX'],
            ['name' => 'Design Systems', 'category' => 'Design'],
            ['name' => 'HTML/CSS', 'category' => 'Frontend']
        ],
        'projects' => [
            [
                'title' => 'Design System Library',
                'description' => 'Created a comprehensive design system library used across 10+ product teams, reducing design inconsistencies by 90%.',
                'start_date' => '2020-01-01',
                'end_date' => '2020-06-30',
                'url' => 'https://figma.com/design-system-library'
            ]
        ],
        'certifications' => [
            ['name' => 'Google UX Design Certificate', 'issuer' => 'Google', 'date_obtained' => '2021-03-10'],
            ['name' => 'Figma Design Mastery', 'issuer' => 'Figma Academy', 'date_obtained' => '2020-09-15']
        ],
        'job_applications' => [
            [
                'company_name' => 'Product Innovations Ltd',
                'job_title' => 'Lead Product Designer',
                'job_description' => 'We are seeking a Lead Product Designer to join our product team. The ideal candidate will have extensive UX/UI design experience, strong leadership skills, and experience with design systems.',
                'status' => 'applied',
                'application_date' => date('Y-m-d', strtotime('-4 days')),
                'salary_range' => '£60,000 - £80,000',
                'job_location' => 'Birmingham',
                'remote_type' => 'hybrid'
            ],
            [
                'company_name' => 'Tech Startup Co',
                'job_title' => 'Senior UX Designer',
                'job_description' => 'Senior UX Designer needed to lead user research and design initiatives for our mobile-first platform.',
                'status' => 'applied',
                'application_date' => date('Y-m-d', strtotime('-1 day')),
                'salary_range' => '£55,000 - £75,000',
                'job_location' => 'London',
                'remote_type' => 'remote'
            ]
        ]
    ]
];

$created = 0;
$skipped = 0;

foreach ($testAccounts as $accountData) {
    $username = $accountData['username'];
    
    echo "\nProcessing: {$username}...\n";
    
    // Check if account already exists
    $existing = db()->fetchOne("SELECT id FROM profiles WHERE username = ?", [$username]);
    
    if ($existing) {
        echo "  ⚠ Account already exists, skipping...\n";
        $skipped++;
        continue;
    }
    
    try {
        // Create profile
        $userId = generateUuid();
        $passwordHash = hashPassword($accountData['password']);
        
        db()->insert('profiles', [
            'id' => $userId,
            'email' => $accountData['email'],
            'password_hash' => $passwordHash,
            'full_name' => $accountData['full_name'],
            'username' => $username,
            'phone' => $accountData['phone'],
            'location' => $accountData['location'],
            'linkedin_url' => $accountData['linkedin_url'],
            'bio' => $accountData['bio'],
            'show_photo' => 1,
            'show_photo_pdf' => 1,
            'cv_visibility' => 'public',
            'email_verified' => 1,
            'account_type' => 'individual',
            'plan' => 'lifetime', // Full subscription
            'subscription_status' => 'active',
            'subscription_current_period_end' => null, // Lifetime subscription
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        echo "  ✓ Created profile\n";
        
        // Create professional summary
        $summaryId = generateUuid();
        db()->insert('professional_summary', [
            'id' => $summaryId,
            'profile_id' => $userId,
            'description' => $accountData['professional_summary'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        echo "  ✓ Created professional summary\n";
        
        // Create work experience
        $workSortOrder = 0;
        foreach ($accountData['work_experience'] as $work) {
            $workExpId = generateUuid();
            db()->insert('work_experience', [
                'id' => $workExpId,
                'profile_id' => $userId,
                'company_name' => $work['company_name'],
                'position' => $work['position'],
                'start_date' => $work['start_date'],
                'end_date' => $work['end_date'],
                'description' => $work['description'],
                'sort_order' => $workSortOrder++,
                'hide_date' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Create responsibility categories and items
            $catSortOrder = 0;
            foreach ($work['responsibilities'] as $catName => $items) {
                $respCatId = generateUuid();
                db()->insert('responsibility_categories', [
                    'id' => $respCatId,
                    'work_experience_id' => $workExpId,
                    'name' => $catName,
                    'sort_order' => $catSortOrder++,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                $itemSortOrder = 0;
                foreach ($items as $itemContent) {
                    db()->insert('responsibility_items', [
                        'id' => generateUuid(),
                        'category_id' => $respCatId,
                        'content' => $itemContent,
                        'sort_order' => $itemSortOrder++,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
        
        echo "  ✓ Created work experience\n";
        
        // Create education
        foreach ($accountData['education'] as $edu) {
            db()->insert('education', [
                'id' => generateUuid(),
                'profile_id' => $userId,
                'institution' => $edu['institution'],
                'degree' => $edu['degree'],
                'field_of_study' => $edu['field_of_study'] ?? null,
                'start_date' => $edu['start_date'],
                'end_date' => $edu['end_date'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        echo "  ✓ Created education\n";
        
        // Create skills
        foreach ($accountData['skills'] as $skill) {
            db()->insert('skills', [
                'id' => generateUuid(),
                'profile_id' => $userId,
                'name' => $skill['name'],
                'category' => $skill['category'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        echo "  ✓ Created skills\n";
        
        // Create projects
        foreach ($accountData['projects'] as $project) {
            db()->insert('projects', [
                'id' => generateUuid(),
                'profile_id' => $userId,
                'title' => $project['title'],
                'description' => $project['description'],
                'start_date' => $project['start_date'],
                'end_date' => $project['end_date'],
                'url' => $project['url'] ?? null,
                'image_url' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        echo "  ✓ Created projects\n";
        
        // Create certifications
        foreach ($accountData['certifications'] as $cert) {
            db()->insert('certifications', [
                'id' => generateUuid(),
                'profile_id' => $userId,
                'name' => $cert['name'],
                'issuer' => $cert['issuer'],
                'date_obtained' => $cert['date_obtained'],
                'expiry_date' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        echo "  ✓ Created certifications\n";
        
        // Create job applications
        $jobAppCount = 0;
        foreach ($accountData['job_applications'] as $jobApp) {
            $result = createJobApplication([
                'company_name' => $jobApp['company_name'],
                'job_title' => $jobApp['job_title'],
                'job_description' => $jobApp['job_description'],
                'status' => $jobApp['status'],
                'application_date' => $jobApp['application_date'],
                'salary_range' => $jobApp['salary_range'],
                'job_location' => $jobApp['job_location'],
                'remote_type' => $jobApp['remote_type'],
                'notes' => $jobApp['notes'] ?? null
            ], $userId);
            
            if (isset($result['success']) && $result['success']) {
                $jobAppCount++;
            } else {
                echo "  ⚠ Warning: Failed to create job application: " . ($result['error'] ?? 'Unknown error') . "\n";
            }
        }
        
        if ($jobAppCount > 0) {
            echo "  ✓ Created {$jobAppCount} job application(s)\n";
        } else {
            echo "  ⚠ No job applications created\n";
        }
        
        // Create master CV variant (required for assessments)
        require_once __DIR__ . '/../php/cv-variants.php';
        try {
            getOrCreateMasterVariant($userId);
            echo "  ✓ Created master CV variant\n";
        } catch (Exception $e) {
            echo "  ⚠ Warning: Could not create master CV variant: " . $e->getMessage() . "\n";
        }
        
        echo "  ✓ Account created successfully!\n";
        echo "     Username: {$username}\n";
        echo "     Password: {$accountData['password']}\n";
        echo "     Profile ID: {$userId}\n";
        echo "     View CV: " . APP_URL . "/cv/@{$username}\n";
        
        $created++;
        
    } catch (Exception $e) {
        echo "  ✗ Error creating account: " . $e->getMessage() . "\n";
        error_log("Error creating test account {$username}: " . $e->getMessage());
        continue;
    }
}

echo "\n";
echo "========================================\n";
echo "Summary\n";
echo "========================================\n";
echo "Created: {$created} accounts\n";
echo "Skipped: {$skipped} accounts (already exist)\n";
echo "\n";
echo "All test accounts use password: TestAccount123!\n";
echo "\n";

