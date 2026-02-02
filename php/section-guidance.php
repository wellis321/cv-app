<?php
/**
 * Section Guidance Content
 * Provides contextual guidance and tips for each CV section
 */

if (!function_exists('getSectionGuidance')) {
function getSectionGuidance($sectionId) {
    $guidance = [
        'professional-summary' => [
            'title' => 'Professional Summary',
            'description' => 'The Professional Summary section of your resume is a place for you to write a narrative or summary of your relevant professional experience, skills, and accomplishments. Think of your Professional Summary as a teaser for the employer to read the rest of your resume.',
            'tips' => [
                'Get clear on audience - Know who will be reading your resume and tailor your summary accordingly.',
                'Outline what information you want to include:',
                '  • Years of experience',
                '  • Notable industries or companies',
                '  • Core skills & keywords',
                '  • 1-2 notable accomplishments',
                'Use first person and make sure your first sentence hooks the reader in.',
                'Remember to use well known companies or products and numbers when possible.'
            ],
            'examples' => [],
            'common_mistakes' => [
                'Being too generic - avoid clichés like "hard-working" or "team player"',
                'Making it too long - keep it concise and impactful',
                'Repeating information that\'s already in other sections',
                'Using third person instead of first person'
            ]
        ],
        'work-experience' => [
            'title' => 'Work Experience',
            'description' => 'Your work experience section showcases your professional history and demonstrates your career progression. This is often the most important section for employers.',
            'tips' => [
                'Quantify achievements - Use numbers, percentages, and metrics to show impact (e.g., "Increased sales by 25%")',
                'Use action verbs - Start bullet points with strong verbs like "Led", "Developed", "Managed", "Implemented"',
                'Focus on results, not just responsibilities - Show what you accomplished, not just what you were supposed to do',
                'Tailor to the job - Highlight experiences most relevant to the position you\'re applying for',
                'Use the STAR method - Situation, Task, Action, Result - to structure your achievements',
                'Include relevant keywords from the job description'
            ],
            'examples' => [],
            'common_mistakes' => [
                'Listing only job duties without showing impact',
                'Using weak verbs like "was responsible for" or "helped with"',
                'Including irrelevant or outdated experience',
                'Not quantifying achievements with numbers'
            ]
        ],
        'education' => [
            'title' => 'Education',
            'description' => 'Your education section demonstrates your academic background and qualifications. Include relevant coursework, honors, and achievements.',
            'tips' => [
                'List in reverse chronological order (most recent first)',
                'Include GPA if it\'s strong (3.5 or higher)',
                'Mention relevant coursework if you\'re a recent graduate',
                'Include honors, awards, or dean\'s list if applicable',
                'For advanced degrees, you can omit high school',
                'Include study abroad or exchange programs if relevant'
            ],
            'examples' => [],
            'common_mistakes' => [
                'Including high school when you have a college degree',
                'Listing GPA if it\'s below 3.0',
                'Including irrelevant coursework',
                'Not formatting dates consistently'
            ]
        ],
        'skills' => [
            'title' => 'Skills',
            'description' => 'Your skills section helps employers quickly identify your technical and soft skills. Group related skills together and match them to job requirements.',
            'tips' => [
                'Group skills by category (Technical, Languages, Soft Skills, etc.)',
                'Match skills to the job description - include keywords from the posting',
                'Be honest about your proficiency level',
                'Include both hard skills (technical) and soft skills (interpersonal)',
                'Update regularly as you learn new skills',
                'Consider including certifications or training related to skills'
            ],
            'examples' => [],
            'common_mistakes' => [
                'Listing skills you don\'t actually have',
                'Being too vague (e.g., "computer skills" instead of "Python, JavaScript")',
                'Including outdated or irrelevant skills',
                'Not organizing skills into categories'
            ]
        ],
        'projects' => [
            'title' => 'Projects',
            'description' => 'Projects showcase your practical experience and ability to apply your skills. Include personal, academic, or professional projects that demonstrate your capabilities.',
            'tips' => [
                'Show impact - Describe what problem you solved or what you achieved',
                'Include technologies used - List programming languages, tools, frameworks',
                'Highlight outcomes - What was the result? Did it improve something?',
                'Add links - Include GitHub, live demos, or portfolio links when available',
                'Focus on recent and relevant projects',
                'Use action-oriented descriptions'
            ],
            'examples' => [],
            'common_mistakes' => [
                'Not explaining the project\'s purpose or impact',
                'Listing technologies without context',
                'Including outdated or irrelevant projects',
                'Not providing links to view the work'
            ]
        ],
        'certifications' => [
            'title' => 'Certifications',
            'description' => 'Certifications demonstrate your commitment to professional development and validate your expertise in specific areas.',
            'tips' => [
                'Include expiration dates if applicable',
                'Add credential IDs or verification numbers',
                'List in reverse chronological order',
                'Include the issuing organization',
                'Only include current and relevant certifications',
                'Consider including completion dates'
            ],
            'examples' => [],
            'common_mistakes' => [
                'Including expired certifications without noting expiration',
                'Not including credential IDs for verification',
                'Listing irrelevant certifications',
                'Not updating when certifications expire'
            ]
        ],
        'memberships' => [
            'title' => 'Professional Memberships',
            'description' => 'Professional memberships show your involvement in industry organizations and commitment to your field.',
            'tips' => [
                'Show active involvement - Mention any leadership roles or contributions',
                'Include dates of membership',
                'Focus on relevant professional organizations',
                'Mention any committees or special projects you\'ve worked on',
                'Keep it current - remove inactive memberships'
            ],
            'examples' => [],
            'common_mistakes' => [
                'Including inactive or expired memberships',
                'Not showing your level of involvement',
                'Including irrelevant memberships',
                'Not including dates'
            ]
        ],
        'interests' => [
            'title' => 'Interests & Activities',
            'description' => 'Your interests section adds personality to your resume and can help you connect with employers who share similar interests.',
            'tips' => [
                'Keep it professional - Avoid controversial topics',
                'Show personality - Include hobbies that reveal character traits',
                'Be specific - "Cycling" is better than "Sports"',
                'Limit to 3-5 interests',
                'Consider how interests relate to the job (e.g., "Reading" for research roles)',
                'Include unique interests that make you memorable'
            ],
            'examples' => [],
            'common_mistakes' => [
                'Including too many interests (clutters the resume)',
                'Being too generic or vague',
                'Including controversial or inappropriate interests',
                'Not connecting interests to professional qualities'
            ]
        ],
        'qualification-equivalence' => [
            'title' => 'Professional Qualification Equivalence',
            'description' => 'This section helps employers understand how international or non-standard qualifications align with local standards.',
            'tips' => [
                'Provide clear evidence - Include documentation or verification',
                'Explain the equivalence clearly',
                'Include the original qualification details',
                'Mention any recognition or accreditation',
                'Provide context about the qualification system',
                'Include dates and issuing organizations'
            ],
            'examples' => [],
            'common_mistakes' => [
                'Not providing sufficient evidence',
                'Making unclear equivalency claims',
                'Not explaining the original qualification system',
                'Missing important details like dates or issuing bodies'
            ]
        ],
        'jobs' => [
            'title' => 'Job Applications',
            'description' => 'Track and manage your job applications in one place. Link CV variants to specific jobs and use AI to tailor your CV for each application.',
            'tips' => [
                'From a job view use "Generate AI CV for this job" for a one-click tailored CV, or "Tailor CV for this job…" to choose which sections to tailor',
                'Add detailed job descriptions - The more information you provide, the better AI can tailor your CV',
                'Upload job description files - PDF, Word, or Excel files are automatically read by AI',
                'Track application status - Keep track of where you are in the process',
                'Generate AI CVs - Create job-specific CV variants automatically',
                'Assess CV quality - Get AI feedback on how well your CV matches each job',
                'Set follow-up reminders - Don\'t forget to follow up on applications',
                'Link CV variants - Associate specific CV versions with each job application'
            ],
            'examples' => [],
            'common_mistakes' => [
                'Not including full job descriptions - AI needs details to tailor your CV effectively',
                'Forgetting to update application status',
                'Not generating job-specific CV variants',
                'Missing follow-up dates'
            ]
        ],
        'ai-tools' => [
            'title' => 'AI Tools',
            'description' => 'Use AI-powered tools to improve your CV, generate job-specific variants, and get quality assessments.',
            'tips' => [
                'Generate CV from Job - Create tailored CVs automatically based on job descriptions',
                'Assess CV Quality - Get comprehensive feedback on your CV with scores and recommendations',
                'Assess Individual Sections - Get section-specific feedback for Professional Summary, Work Experience, and Skills',
                'Use AI Assessment regularly - Check your CV quality as you make changes',
                'Generate multiple variants - Create different CVs for different types of jobs'
            ],
            'examples' => [],
            'common_mistakes' => [
                'Not using AI tools to improve your CV',
                'Ignoring AI recommendations',
                'Not generating job-specific variants'
            ]
        ],
        'cv-variants' => [
            'title' => 'CV Variants',
            'description' => 'Manage different versions of your CV for specific job applications. Recommended order: complete your CV sections, add job applications, then generate a CV variant for each job. Create tailored CVs using AI or manually edit variants.',
            'tips' => [
                'Open a job in Manage Jobs and use "Generate AI CV for this job" or "Tailor CV for this job…" to create a variant without leaving the editor',
                'Create variants for different job types - Have separate CVs for different industries or roles',
                'Use descriptive names - Name variants clearly so you can easily identify them (e.g., "Software Developer - Tech", "Project Manager - Healthcare")',
                'Link variants to jobs - Variants linked to jobs show a "Linked" badge; associate CV variants with job applications for easy tracking',
                'Generate with AI - Use "Create New CV with AI" or, from a job view, "Generate AI CV for this job" or "Tailor CV for this job…"',
                'Review AI-generated content - Always review and edit AI-generated variants to ensure accuracy',
                'Keep Master CV updated - Your Master CV is the base for all variants, so keep it current',
                'Assess variant quality - Use AI Assessment to check how well each variant matches its target job',
                'Rename variants - Keep variant names clear and descriptive as you create more'
            ],
            'examples' => [],
            'common_mistakes' => [
                'Not creating job-specific variants - Using the same CV for all applications',
                'Using vague variant names - Makes it hard to find the right CV later',
                'Not reviewing AI-generated content - Always check AI output for accuracy',
                'Forgetting to link variants to jobs - Makes it harder to track which CV was used for which application',
                'Not keeping Master CV updated - Outdated base CV affects all variants'
            ]
        ]
    ];

    return $guidance[$sectionId] ?? [
        'title' => 'Section',
        'description' => 'Guidance for this section.',
        'tips' => [],
        'examples' => [],
        'common_mistakes' => []
    ];
}
} // End function_exists check
