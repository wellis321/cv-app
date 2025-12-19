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

            <img src="/static/images/how-it-works/getting-started.jpeg" alt="Getting Started" class="w-full max-w-2xl mx-auto h-auto rounded-xl border border-gray-200 shadow-lg mb-6" loading="lazy" width="600" height="300" decoding="async">

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

            <img src="/static/images/how-it-works/Personal Profile.jpeg" alt="Personal Profile" class="w-full max-w-2xl mx-auto h-auto rounded-xl border border-gray-200 shadow-lg mb-6" loading="lazy" width="600" height="300" decoding="async">

            <p class="text-gray-600 mb-6">
                Your personal profile is the foundation of your CV. It appears at the top and includes your name, contact information, and professional summary.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-blue-900 mb-2">What You Can Add:</h3>
                <p class="text-blue-800 mb-2">
                    Your personal profile includes your <strong>full name</strong> (required), which appears prominently at the top of your CV. You can also add a <strong>username</strong> that creates your unique CV URL (<code class="bg-blue-100 px-1 rounded">/cv/@yourusername</code>), making it easy to share your CV with employers.
                </p>
                <p class="text-blue-800 mb-2">
                    Include your <strong>location</strong> (city or region), <strong>phone number</strong>, and <strong>professional email address</strong> so employers can easily contact you. You can also add a <strong>LinkedIn URL</strong> to connect your online professional profile.
                </p>
                <p class="text-blue-800">
                    Finally, add a <strong>strapline</strong>—a short professional tagline or summary that captures your professional identity in a few words. This appears right below your name and helps employers quickly understand who you are.
                </p>
            </div>

            <p class="text-sm text-gray-500">
                💡 <strong>Tip:</strong> Your username creates your unique CV link. Choose something professional and memorable!
            </p>
        </section>

        <!-- CV Visibility -->
        <section id="cv-visibility" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">CV Visibility Control</h2>

            <img src="/static/images/how-it-works/Visibility Control.jpeg" alt="CV Visibility Control" class="w-full max-w-2xl mx-auto h-auto rounded-xl border border-gray-200 shadow-lg mb-6" loading="lazy" width="600" height="300" decoding="async">

            <p class="text-gray-600 mb-6">
                You have full control over who can see your CV. Toggle visibility on or off at any time.
            </p>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-green-900 mb-2">How It Works:</h3>
                <p class="text-green-800 mb-2">
                    When your CV is set to <strong>Public (On)</strong>, anyone with your CV link can view it. This is perfect for sharing with employers, recruiters, or networking contacts. Your CV becomes accessible to anyone who has the link, making it easy to distribute during your job search.
                </p>
                <p class="text-green-800">
                    When set to <strong>Private (Off)</strong>, only you can view your CV when logged in. This is great for privacy when you're still editing, or if you want to keep your CV hidden until you're ready to share it. Even if someone has your CV link, they won't be able to access it when it's private.
                </p>
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

            <img src="/static/images/how-it-works/Profile Photo.jpeg" alt="Profile Photo" class="w-full max-w-2xl mx-auto h-auto rounded-xl border border-gray-200 shadow-lg mb-6" loading="lazy" width="600" height="300" decoding="async">

            <p class="text-gray-600 mb-6">
                Add a professional photo to personalise your CV. You can control where it appears.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-blue-900 mb-2">Photo Options:</h3>
                <p class="text-blue-800 mb-2">
                    You can <strong>upload or take a photo</strong> directly from your device. We support JPG, PNG, GIF, or WebP formats with a maximum file size of 5MB. Choose a professional headshot that presents you well.
                </p>
                <p class="text-blue-800 mb-2">
                    Control where your photo appears with two separate options: <strong>Show on Online CV</strong> displays your photo on your public CV page, while <strong>Show in PDF</strong> includes your photo when generating PDF downloads. You can enable one, both, or neither option depending on your preferences.
                </p>
                <p class="text-blue-800">
                    If you choose to hide your photo, a <strong>QR code alternative</strong> will appear instead. This QR code links directly to your CV, allowing employers to quickly scan and access your live CV online from your printed PDF.
                </p>
            </div>

            <p class="text-sm text-gray-500">
                💡 <strong>Tip:</strong> Use a professional headshot with good lighting and a neutral background for best results.
            </p>
        </section>

        <!-- Work Experience -->
        <section id="work-experience" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Work Experience</h2>

            <img src="/static/images/how-it-works/Work Experience.jpeg" alt="Work Experience" class="w-full max-w-2xl mx-auto h-auto rounded-xl border border-gray-200 shadow-lg mb-6" loading="lazy" width="600" height="300" decoding="async">

            <p class="text-gray-600 mb-6">
                Showcase your professional experience with detailed job entries.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-blue-900 mb-2">For Each Job, You Can Add:</h3>
                <p class="text-blue-800 mb-2">
                    Start with the basics: your <strong>job title or position</strong> and the <strong>company name</strong>. Add your <strong>start and end dates</strong> to show your employment period, or mark the position as "Current Job" if you're still working there.
                </p>
                <p class="text-blue-800 mb-2">
                    Include a <strong>description</strong> that provides an overview of your role and the company. This gives context to employers about your position and the organisation you worked for.
                </p>
                <p class="text-blue-800">
                    Most importantly, add your <strong>key responsibilities</strong> organised by categories with bullet points. This allows you to group your achievements logically—for example, separating project management tasks from technical achievements or client relations.
                </p>
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

            <img src="/static/images/how-it-works/Education.jpeg" alt="Education" class="w-full max-w-2xl mx-auto h-auto rounded-xl border border-gray-200 shadow-lg mb-6" loading="lazy" width="600" height="300" decoding="async">

            <p class="text-gray-600 mb-6">
                List your educational qualifications, degrees, and certifications.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Education Details Include:</h3>
                <p class="text-blue-800 mb-2">
                    For each qualification, include the <strong>degree or course name</strong>, the <strong>institution</strong> (school or university), and your <strong>field of study</strong> or major. This helps employers understand your educational background and specialisation.
                </p>
                <p class="text-blue-800">
                    Add the <strong>start and end dates</strong> of your studies to show when you completed your education. List your qualifications in reverse chronological order, with the most recent first.
                </p>
            </div>
        </section>

        <!-- Skills -->
        <section id="skills" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Skills</h2>

            <img src="/static/images/how-it-works/Skills.jpeg" alt="Skills" class="w-full max-w-2xl mx-auto h-auto rounded-xl border border-gray-200 shadow-lg mb-6" loading="lazy" width="600" height="300" decoding="async">

            <p class="text-gray-600 mb-6">
                Highlight your skills and competencies, organised by category.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Skill Features:</h3>
                <p class="text-blue-800 mb-2">
                    Add each <strong>skill or technology</strong> you want to highlight. Organise your skills into <strong>categories</strong> like "Technical", "Languages", or "Software" to make it easy for employers to see your competencies at a glance.
                </p>
                <p class="text-blue-800">
                    Optionally, you can add a <strong>proficiency level</strong> (such as "Beginner", "Intermediate", or "Expert") to indicate your level of expertise with each skill. This helps employers understand your capabilities more precisely.
                </p>
            </div>
        </section>

        <!-- Projects -->
        <section id="projects" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Projects</h2>

            <img src="/static/images/how-it-works/Projects.jpeg" alt="Projects" class="w-full max-w-2xl mx-auto h-auto rounded-xl border border-gray-200 shadow-lg mb-6" loading="lazy" width="600" height="300" decoding="async">

            <p class="text-gray-600 mb-6">
                Showcase your portfolio projects, side projects, or major work achievements.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Project Details:</h3>
                <p class="text-blue-800 mb-2">
                    Start with a <strong>project title</strong> and a <strong>description</strong> that explains what the project is about and what you accomplished. Include the <strong>dates</strong> when you worked on it to show the timeline of your work.
                </p>
                <p class="text-blue-800 mb-2">
                    If your project is live or available online, add a <strong>project URL</strong> linking to the live project or your GitHub repository. This allows employers to see your work in action.
                </p>
                <p class="text-blue-800">
                    You can also upload a <strong>project image</strong>—a screenshot or visual representation of your project. This helps employers quickly understand what you built and makes your CV more visually engaging.
                </p>
            </div>
        </section>

        <!-- Certifications -->
        <section id="certifications" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Certifications</h2>

            <img src="/static/images/how-it-works/Certifications.jpeg" alt="Certifications" class="w-full max-w-2xl mx-auto h-auto rounded-xl border border-gray-200 shadow-lg mb-6" loading="lazy" width="600" height="300" decoding="async">

            <p class="text-gray-600 mb-6">
                Display your professional certifications and credentials.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Certification Information:</h3>
                <p class="text-blue-800 mb-2">
                    For each certification, include the <strong>certification name</strong> and the <strong>issuer</strong>—the organisation that issued it. This helps employers verify your credentials and understand the source of your certification.
                </p>
                <p class="text-blue-800">
                    Add the <strong>date obtained</strong> to show when you earned the certification. If your certification has an expiry date, include that as well so employers know whether your certification is still current.
                </p>
            </div>
        </section>

        <!-- Interests -->
        <section id="interests" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Interests & Activities</h2>

            <img src="/static/images/how-it-works/Interests & Activities.jpeg" alt="Interests & Activities" class="w-full max-w-2xl mx-auto h-auto rounded-xl border border-gray-200 shadow-lg mb-6" loading="lazy" width="600" height="300" decoding="async">

            <p class="text-gray-600 mb-6">
                Add your hobbies, interests, and activities to show your personality and well-roundedness.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">What to Include:</h3>
                <p class="text-blue-800 mb-2">
                    Include your <strong>hobbies and interests</strong> to show your personality and what you enjoy outside of work. This helps employers get a sense of who you are as a person.
                </p>
                <p class="text-blue-800 mb-2">
                    Add any <strong>volunteer work</strong> you've done, as this demonstrates your commitment to giving back and can showcase relevant skills. Include <strong>sports and activities</strong> that highlight teamwork, discipline, or leadership qualities.
                </p>
                <p class="text-blue-800">
                    Don't forget to mention any <strong>professional memberships</strong> or other relevant activities that demonstrate your engagement with your industry or community. These details help paint a complete picture of your professional and personal development.
                </p>
            </div>
        </section>

        <!-- Templates & Styling -->
        <section id="templates" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Templates & Styling</h2>

            <img src="/static/images/how-it-works/Templates & Styling.jpeg" alt="Templates & Styling" class="w-full max-w-2xl mx-auto h-auto rounded-xl border border-gray-200 shadow-lg mb-6" loading="lazy" width="600" height="300" decoding="async">

            <p class="text-gray-600 mb-6">
                Choose from professional templates and customise your CV's appearance.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-blue-900 mb-2">Available Templates:</h3>
                <p class="text-blue-800 mb-2">
                    Choose from four professional templates designed for different styles and industries. <strong>Professional Blue</strong> offers a classic business layout with blue accents, perfect for corporate roles. <strong>Minimal</strong> provides a clean, simple design with subtle dividers for a modern, understated look.
                </p>
                <p class="text-blue-800">
                    The <strong>Classic</strong> template features a traditional formal layout ideal for conservative industries, while <strong>Modern</strong> uses a contemporary two-column design that maximises space and visual appeal.
                </p>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">Customisation Options:</h3>
                <p class="text-gray-700 mb-2">
                    Personalise your CV's appearance by customising header colours with gradient options or choosing from predefined colour schemes. This allows you to match your CV to your personal brand or industry standards.
                </p>
                <p class="text-gray-700">
                    You have full control over which sections to include in your CV, and you can manage photo and QR code visibility independently. This flexibility lets you tailor your CV for different applications while maintaining a consistent professional look.
                </p>
            </div>
        </section>

        <!-- PDF Export -->
        <section id="pdf-export" class="feature-section bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">PDF Export</h2>

            <img src="/static/images/how-it-works/PDF Export.jpeg" alt="PDF Export" class="w-full max-w-2xl mx-auto h-auto rounded-xl border border-gray-200 shadow-lg mb-6" loading="lazy" width="600" height="300" decoding="async">

            <p class="text-gray-600 mb-6">
                Generate professional PDF versions of your CV for printing or emailing.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-blue-900 mb-2">PDF Features:</h3>
                <p class="text-blue-800 mb-2">
                    When generating your PDF, you can <strong>choose which sections to include</strong>, giving you complete control over what employers see. This is perfect for tailoring your CV to specific job applications. You can also <strong>optionally include your profile photo</strong> if you want a more personal touch.
                </p>
                <p class="text-blue-800 mb-2">
                    One of our standout features is the <strong>QR code</strong> option. When included, the QR code links directly to your personal CV page. Employers can simply scan it with their phone to instantly view your live, up-to-date CV online—perfect for networking events or printed CVs.
                </p>
                <p class="text-blue-800">
                    Your PDF will <strong>match your selected template design</strong> exactly, ensuring consistency between your online and printed CV. All PDFs are <strong>optimised for A4 paper size</strong> and print-ready, so you can confidently share them via email or print them for interviews.
                </p>
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
                    <p class="text-green-800 mb-2">
                        Generate a professional PDF version of your CV for <strong>email attachments</strong>, <strong>printing</strong>, <strong>job application portals</strong>, or <strong>offline sharing</strong>. PDFs preserve your formatting perfectly, ensuring your CV looks professional regardless of how it's viewed.
                    </p>
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
                <a href="#" data-open-register class="inline-block bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
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
