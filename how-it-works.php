<?php
/**
 * How It Works / Documentation Page
 * Explains the CV Builder features using the example CV
 */

require_once __DIR__ . '/php/helpers.php';

$exampleCvUrl = APP_URL . '/cv/@simple-cv-example';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    // Prepare HowTo structured data for AI SEO
    $howToSteps = [
        ['name' => 'Create Your Free Account', 'text' => 'Sign up for a free account to get started. No credit card required.'],
        ['name' => 'Complete Your Profile', 'text' => 'Add your personal information, including your name, contact details, and a professional strapline.'],
        ['name' => 'Add Your Experience', 'text' => 'Build your CV by adding work experience, education, skills, projects, and more.'],
        ['name' => 'Customise & Share', 'text' => 'Choose a template, customise colours, and share your CV with a simple link or download as PDF.']
    ];

    partial('head', [
        'pageTitle' => 'How It Works - Simple CV Builder',
        'metaDescription' => 'Learn how to use Simple CV Builder to create, customise, and share your professional CV.',
        'canonicalUrl' => APP_URL . '/how-it-works.php',
        'structuredDataType' => 'howto',
        'structuredData' => [
            'name' => 'How to Create Your CV with Simple CV Builder',
            'description' => 'Step-by-step guide to creating, customising, and sharing your professional CV.',
            'steps' => $howToSteps
        ],
    ]); ?>
    <style>
        .feature-section {
            scroll-margin-top: 100px;
        }
        .example-cv-embed {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin: 20px 0;
        }
        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: #3b82f6;
            color: white;
            border-radius: 50%;
            font-weight: bold;
            margin-right: 12px;
        }
        .section-image {
            width: 100%;
            max-width: 600px;
            height: 300px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        .section-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></svg>');
            opacity: 0.3;
        }
        .section-image-placeholder {
            color: white;
            font-size: 48px;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .section-image-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            z-index: 1;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Learn how to create, customise, and share your professional CV with Simple CV Builder
            </p>
        </div>

        <!-- Example CV Preview -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-12">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Example CV</h2>
            <p class="text-gray-600 mb-4">
                Here's an example of what your CV can look like. Click the link below to see it in action:
            </p>
            <a href="<?php echo e($exampleCvUrl); ?>" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                View Example CV →
            </a>
        </div>

        <!-- Table of Contents -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Navigation</h2>
            <nav class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <a href="#getting-started" class="text-blue-600 hover:text-blue-800">Getting Started</a>
                <a href="#personal-profile" class="text-blue-600 hover:text-blue-800">Personal Profile</a>
                <a href="#cv-visibility" class="text-blue-600 hover:text-blue-800">CV Visibility</a>
                <a href="#profile-photo" class="text-blue-600 hover:text-blue-800">Profile Photo</a>
                <a href="#work-experience" class="text-blue-600 hover:text-blue-800">Work Experience</a>
                <a href="#education" class="text-blue-600 hover:text-blue-800">Education</a>
                <a href="#skills" class="text-blue-600 hover:text-blue-800">Skills</a>
                <a href="#projects" class="text-blue-600 hover:text-blue-800">Projects</a>
                <a href="#certifications" class="text-blue-600 hover:text-blue-800">Certifications</a>
                <a href="#interests" class="text-blue-600 hover:text-blue-800">Interests & Activities</a>
                <a href="#templates" class="text-blue-600 hover:text-blue-800">Templates & Styling</a>
                <a href="#pdf-export" class="text-blue-600 hover:text-blue-800">PDF Export</a>
            </nav>
        </div>

        <!-- Getting Started -->
        <section id="getting-started" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Getting Started</h2>

            <div class="section-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="section-image-icon">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-start">
                    <span class="step-number">1</span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Create Your Free Account</h3>
                        <p class="text-gray-600">Sign up for a free account to get started. No credit card required.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <span class="step-number">2</span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Complete Your Profile</h3>
                        <p class="text-gray-600">Add your personal information, including your name, contact details, and a professional strapline.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <span class="step-number">3</span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Add Your Experience</h3>
                        <p class="text-gray-600">Build your CV by adding work experience, education, skills, projects, and more.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <span class="step-number">4</span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Customise & Share</h3>
                        <p class="text-gray-600">Choose a template, customise colours, and share your CV with a simple link or download as PDF.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Personal Profile -->
        <section id="personal-profile" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Personal Profile</h2>

            <div class="section-image" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="section-image-icon">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>

            <p class="text-gray-600 mb-6">
                Your personal profile is the foundation of your CV. It appears at the top and includes your name, contact information, and professional summary.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-blue-900 mb-2">What You Can Add:</h3>
                <ul class="list-disc list-inside text-blue-800 space-y-1">
                    <li><strong>Full Name</strong> - Your professional name (required)</li>
                    <li><strong>Username</strong> - Used in your CV URL: <code class="bg-blue-100 px-1 rounded">/cv/@yourusername</code></li>
                    <li><strong>Location</strong> - Your city or region</li>
                    <li><strong>Phone Number</strong> - Contact phone number</li>
                    <li><strong>Email</strong> - Your professional email address</li>
                    <li><strong>LinkedIn URL</strong> - Link to your LinkedIn profile</li>
                    <li><strong>Strapline</strong> - A short professional tagline or summary</li>
                </ul>
            </div>

            <p class="text-sm text-gray-500">
                💡 <strong>Tip:</strong> Your username creates your unique CV link. Choose something professional and memorable!
            </p>
        </section>

        <!-- CV Visibility -->
        <section id="cv-visibility" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">CV Visibility Control</h2>

            <div class="section-image" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="section-image-icon">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
            </div>

            <p class="text-gray-600 mb-6">
                You have full control over who can see your CV. Toggle visibility on or off at any time.
            </p>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-green-900 mb-2">How It Works:</h3>
                <ul class="list-disc list-inside text-green-800 space-y-2">
                    <li><strong>Public (On):</strong> Anyone with your CV link can view it - perfect for sharing with employers</li>
                    <li><strong>Private (Off):</strong> Only you can view it when logged in - great for privacy or when you're still editing</li>
                </ul>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">To Change Visibility:</h3>
                <ol class="list-decimal list-inside text-gray-700 space-y-1">
                    <li>Go to your <strong>Profile</strong> page</li>
                    <li>Click the <strong>"CV Visibility"</strong> tab</li>
                    <li>Toggle <strong>"Make my CV publicly accessible"</strong> on or off</li>
                    <li>Click <strong>"Save Profile"</strong></li>
                </ol>
            </div>
        </section>

        <!-- Profile Photo -->
        <section id="profile-photo" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Profile Photo</h2>

            <div class="section-image" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="section-image-icon">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <p class="text-gray-600 mb-6">
                Add a professional photo to personalise your CV. You can control where it appears.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-blue-900 mb-2">Photo Options:</h3>
                <ul class="list-disc list-inside text-blue-800 space-y-2">
                    <li><strong>Upload or Take Photo:</strong> Add a professional headshot (JPG, PNG, GIF, or WebP, max 5MB)</li>
                    <li><strong>Show on Online CV:</strong> Display your photo on your public CV page</li>
                    <li><strong>Show in PDF:</strong> Include your photo when generating PDFs</li>
                    <li><strong>QR Code Alternative:</strong> If you hide your photo, a QR code linking to your CV will appear instead</li>
                </ul>
            </div>

            <p class="text-sm text-gray-500">
                💡 <strong>Tip:</strong> Use a professional headshot with good lighting and a neutral background for best results.
            </p>
        </section>

        <!-- Work Experience -->
        <section id="work-experience" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Work Experience</h2>

            <div class="section-image" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                <div class="section-image-icon">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <p class="text-gray-600 mb-6">
                Showcase your professional experience with detailed job entries.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-blue-900 mb-2">For Each Job, You Can Add:</h3>
                <ul class="list-disc list-inside text-blue-800 space-y-1">
                    <li><strong>Job Title / Position</strong> - Your role title</li>
                    <li><strong>Company Name</strong> - Employer name</li>
                    <li><strong>Start & End Dates</strong> - Employment period (or mark as "Current Job")</li>
                    <li><strong>Description</strong> - Overview of your role</li>
                    <li><strong>Key Responsibilities</strong> - Organise responsibilities by categories with bullet points</li>
                </ul>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">Responsibility Categories:</h3>
                <p class="text-gray-700 mb-2">Organise your achievements into categories like:</p>
                <ul class="list-disc list-inside text-gray-700 space-y-1 ml-4">
                    <li>Project Management</li>
                    <li>Team Leadership</li>
                    <li>Technical Achievements</li>
                    <li>Client Relations</li>
                    <li>And more...</li>
                </ul>
            </div>
        </section>

        <!-- Education -->
        <section id="education" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Education</h2>

            <div class="section-image" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                <div class="section-image-icon">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7m0-7l-6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 006.824-2.998 12.078 12.078 0 01-.665-6.479L12 14z"/>
                    </svg>
                </div>
            </div>

            <p class="text-gray-600 mb-6">
                List your educational qualifications, degrees, and certifications.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Education Details Include:</h3>
                <ul class="list-disc list-inside text-blue-800 space-y-1">
                    <li><strong>Degree / Course</strong> - Qualification name</li>
                    <li><strong>Institution</strong> - School or university name</li>
                    <li><strong>Field of Study</strong> - Your major or specialization</li>
                    <li><strong>Dates</strong> - Start and end dates of your studies</li>
                </ul>
            </div>
        </section>

        <!-- Skills -->
        <section id="skills" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Skills</h2>

            <div class="section-image" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                <div class="section-image-icon">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
            </div>

            <p class="text-gray-600 mb-6">
                Highlight your skills and competencies, organised by category.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Skill Features:</h3>
                <ul class="list-disc list-inside text-blue-800 space-y-1">
                    <li><strong>Skill Name</strong> - The skill or technology</li>
                    <li><strong>Category</strong> - Group skills (e.g., "Technical", "Languages", "Software")</li>
                    <li><strong>Level</strong> - Optional proficiency level (e.g., "Beginner", "Intermediate", "Expert")</li>
                </ul>
            </div>
        </section>

        <!-- Projects -->
        <section id="projects" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Projects</h2>

            <div class="section-image" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                <div class="section-image-icon">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>

            <p class="text-gray-600 mb-6">
                Showcase your portfolio projects, side projects, or major work achievements.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Project Details:</h3>
                <ul class="list-disc list-inside text-blue-800 space-y-1">
                    <li><strong>Project Title</strong> - Name of your project</li>
                    <li><strong>Description</strong> - What the project is about</li>
                    <li><strong>Dates</strong> - When you worked on it</li>
                    <li><strong>Project URL</strong> - Link to live project or GitHub repository</li>
                    <li><strong>Project Image</strong> - Optional screenshot or image</li>
                </ul>
            </div>
        </section>

        <!-- Certifications -->
        <section id="certifications" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Certifications</h2>

            <div class="section-image" style="background: linear-gradient(135deg, #ff6e7f 0%, #bfe9ff 100%);">
                <div class="section-image-icon">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
            </div>

            <p class="text-gray-600 mb-6">
                Display your professional certifications and credentials.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Certification Information:</h3>
                <ul class="list-disc list-inside text-blue-800 space-y-1">
                    <li><strong>Certification Name</strong> - Name of the certification</li>
                    <li><strong>Issuer</strong> - Organization that issued it</li>
                    <li><strong>Date Obtained</strong> - When you earned it</li>
                    <li><strong>Expiry Date</strong> - If applicable, when it expires</li>
                </ul>
            </div>
        </section>

        <!-- Interests -->
        <section id="interests" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Interests & Activities</h2>

            <div class="section-image" style="background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);">
                <div class="section-image-icon">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <p class="text-gray-600 mb-6">
                Add your hobbies, interests, and activities to show your personality and well-roundedness.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">What to Include:</h3>
                <ul class="list-disc list-inside text-blue-800 space-y-1">
                    <li>Hobbies and interests</li>
                    <li>Volunteer work</li>
                    <li>Sports and activities</li>
                    <li>Professional memberships</li>
                    <li>Any other relevant activities</li>
                </ul>
            </div>
        </section>

        <!-- Templates & Styling -->
        <section id="templates" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Templates & Styling</h2>

            <div class="section-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="section-image-icon">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-4m-6 0a2 2 0 01-2-2v-4a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2z"/>
                    </svg>
                </div>
            </div>

            <p class="text-gray-600 mb-6">
                Choose from professional templates and customise your CV's appearance.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-blue-900 mb-2">Available Templates:</h3>
                <ul class="list-disc list-inside text-blue-800 space-y-2">
                    <li><strong>Professional Blue</strong> - Classic business layout with blue accents</li>
                    <li><strong>Minimal</strong> - Clean, simple design with subtle dividers</li>
                    <li><strong>Classic</strong> - Traditional formal layout</li>
                    <li><strong>Modern</strong> - Contemporary two-column design</li>
                </ul>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">Customisation Options:</h3>
                <ul class="list-disc list-inside text-gray-700 space-y-1">
                    <li>Customise header colours with gradient options</li>
                    <li>Choose from predefined colour schemes</li>
                    <li>Select which sections to include</li>
                    <li>Control photo and QR code visibility</li>
                </ul>
            </div>
        </section>

        <!-- PDF Export -->
        <section id="pdf-export" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">PDF Export</h2>

            <div class="section-image" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="section-image-icon">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <p class="text-gray-600 mb-6">
                Generate professional PDF versions of your CV for printing or emailing.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-blue-900 mb-2">PDF Features:</h3>
                <ul class="list-disc list-inside text-blue-800 space-y-2">
                    <li><strong>Choose Sections:</strong> Select which sections to include in your PDF</li>
                    <li><strong>Include Photo:</strong> Optionally add your profile photo</li>
                    <li><strong>QR Code:</strong> Add a QR code that links directly to your personal CV page - when scanned, it opens your live CV online</li>
                    <li><strong>Template Matching:</strong> PDF matches your selected template design</li>
                    <li><strong>Print Ready:</strong> Optimised for A4 paper size</li>
                </ul>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">How to Generate PDF:</h3>
                <ol class="list-decimal list-inside text-gray-700 space-y-1">
                    <li>Go to <strong>Preview & PDF</strong> page</li>
                    <li>Select your preferred template</li>
                    <li>Choose which sections to include</li>
                    <li>Toggle photo and QR code options</li>
                    <li>Click <strong>"Generate PDF"</strong></li>
                    <li>Download your professional CV PDF</li>
                </ol>
            </div>
        </section>

        <!-- Sharing Your CV -->
        <section id="sharing" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Sharing Your CV</h2>
            <p class="text-gray-600 mb-6">
                Share your CV easily with employers and recruiters.
            </p>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-2">Online Link</h3>
                    <p class="text-blue-800 mb-2">Your CV has a unique URL:</p>
                    <code class="block bg-blue-100 px-2 py-1 rounded text-sm mb-2"><?php echo APP_URL; ?>/cv/@yourusername</code>
                    <p class="text-sm text-blue-700">Share this link directly with employers. They'll see your live, up-to-date CV.</p>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="font-semibold text-green-900 mb-2">PDF Download</h3>
                    <p class="text-green-800 mb-2">Generate a PDF version for:</p>
                    <ul class="list-disc list-inside text-green-700 space-y-1">
                        <li>Email attachments</li>
                        <li>Printing</li>
                        <li>Job application portals</li>
                        <li>Offline sharing</li>
                    </ul>
                    <p class="text-sm text-green-700 mt-3">
                        <strong>💡 QR Code Feature:</strong> When you include a QR code in your PDF, it links directly to your personal CV page (<code class="bg-green-100 px-1 rounded"><?php echo APP_URL; ?>/cv/@yourusername</code>). Employers can scan it with their phone to instantly view your live, up-to-date CV online!
                    </p>
                </div>
            </div>
        </section>

        <!-- Tips & Best Practices -->
        <section id="tips" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Tips & Best Practices</h2>

            <div class="space-y-4">
                <div class="border-l-4 border-blue-500 pl-4">
                    <h3 class="font-semibold text-gray-900 mb-1">Keep It Updated</h3>
                    <p class="text-gray-600">Your online CV updates in real-time. Make changes anytime and they're instantly visible.</p>
                </div>

                <div class="border-l-4 border-green-500 pl-4">
                    <h3 class="font-semibold text-gray-900 mb-1">Be Consistent</h3>
                    <p class="text-gray-600">Use consistent formatting, dates, and descriptions throughout your CV for a professional look.</p>
                </div>

                <div class="border-l-4 border-purple-500 pl-4">
                    <h3 class="font-semibold text-gray-900 mb-1">Tailor for Applications</h3>
                    <p class="text-gray-600">You can temporarily hide sections or adjust content for specific job applications, then change it back.</p>
                </div>

                <div class="border-l-4 border-orange-500 pl-4">
                    <h3 class="font-semibold text-gray-900 mb-1">Use the QR Code</h3>
                    <p class="text-gray-600">Include a QR code in your PDF so employers can quickly scan it with their phone to access your personal CV page online. The QR code links directly to your CV at <code class="bg-gray-100 px-1 rounded"><?php echo APP_URL; ?>/cv/@yourusername</code>.</p>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-lg p-8 text-center text-white mb-8">
            <h2 class="text-2xl font-bold mb-4">Ready to Create Your CV?</h2>
            <p class="text-blue-100 mb-6 text-lg">Start building your professional CV today - it's free!</p>
            <?php if (isLoggedIn()): ?>
                <a href="/dashboard.php" class="inline-block bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                    Go to Dashboard
                </a>
            <?php else: ?>
                <a href="/#auth-section" class="inline-block bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                    Create Free Account
                </a>
            <?php endif; ?>
        </div>

        <!-- View Example CV -->
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">See It In Action</h3>
            <p class="text-gray-600 mb-4">Check out our example CV to see all these features in action:</p>
            <a href="<?php echo e($exampleCvUrl); ?>" target="_blank" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                View Example CV →
            </a>
        </div>
    </main>

    <?php partial('footer'); ?>
</body>
</html>
