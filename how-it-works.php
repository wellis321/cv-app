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
    <?php partial('head', [
        'pageTitle' => 'How It Works - Simple CV Builder',
        'metaDescription' => 'Learn how to use Simple CV Builder to create, customize, and share your professional CV.',
        'canonicalUrl' => APP_URL . '/how-it-works.php',
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
    </style>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Learn how to create, customize, and share your professional CV with Simple CV Builder
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
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Customize & Share</h3>
                        <p class="text-gray-600">Choose a template, customize colors, and share your CV with a simple link or download as PDF.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Personal Profile -->
        <section id="personal-profile" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Personal Profile</h2>
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
            <p class="text-gray-600 mb-6">
                Add a professional photo to personalize your CV. You can control where it appears.
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
                    <li><strong>Key Responsibilities</strong> - Organize responsibilities by categories with bullet points</li>
                </ul>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">Responsibility Categories:</h3>
                <p class="text-gray-700 mb-2">Organize your achievements into categories like:</p>
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
            <p class="text-gray-600 mb-6">
                Highlight your skills and competencies, organized by category.
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
            <p class="text-gray-600 mb-6">
                Choose from professional templates and customize your CV's appearance.
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
                <h3 class="font-semibold text-gray-900 mb-2">Customization Options:</h3>
                <ul class="list-disc list-inside text-gray-700 space-y-1">
                    <li>Customize header colors with gradient options</li>
                    <li>Choose from predefined color schemes</li>
                    <li>Select which sections to include</li>
                    <li>Control photo and QR code visibility</li>
                </ul>
            </div>
        </section>

        <!-- PDF Export -->
        <section id="pdf-export" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">PDF Export</h2>
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
                    <li><strong>Print Ready:</strong> Optimized for A4 paper size</li>
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
