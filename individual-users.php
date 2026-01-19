<?php
/**
 * For Individual Users - Documentation and Guide
 * Information for individual users building their CVs
 */

require_once __DIR__ . '/php/helpers.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'For Individual Users | Simple CV Builder',
        'metaDescription' => 'Learn how to create a professional CV with Simple CV Builder. Build, share, and manage your CV with our comprehensive platform.',
        'canonicalUrl' => APP_URL . '/individual-users.php',
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content" role="main">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">
                        For Individual Users
                    </h1>
                    <p class="mt-4 text-xl text-blue-100 max-w-3xl mx-auto">
                        Create a professional CV that stands out, updates in real-time, and can be shared as a simple link. Build your career profile with our comprehensive CV builder.
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Navigation -->
        <div class="bg-white border-b border-gray-200 sticky top-16 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex space-x-8 overflow-x-auto py-4" aria-label="Documentation sections">
                    <a href="#getting-started" class="whitespace-nowrap text-sm font-medium text-gray-700 hover:text-blue-600">Getting Started</a>
                    <a href="#why-choose-us" class="whitespace-nowrap text-sm font-medium text-gray-700 hover:text-blue-600">Why Choose Us</a>
                    <a href="#what-to-expect" class="whitespace-nowrap text-sm font-medium text-gray-700 hover:text-blue-600">What to Expect</a>
                    <a href="#job-tracking" class="whitespace-nowrap text-sm font-medium text-gray-700 hover:text-blue-600">Job Application Tracking</a>
                    <a href="#pricing" class="whitespace-nowrap text-sm font-medium text-gray-700 hover:text-blue-600">Pricing & Plans</a>
                    <a href="#security" class="whitespace-nowrap text-sm font-medium text-gray-700 hover:text-blue-600">Security & Privacy</a>
                </nav>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Getting Started -->
            <section id="getting-started" class="mb-16 scroll-mt-64">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Getting Started</h2>
                
                <div class="space-y-8">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <div class="grid md:grid-cols-2 gap-6 items-center mb-6">
                            <div>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    Creating your professional CV takes just minutes. No downloads, no software installation—everything works right in your browser on any device.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed">
                                    Click "Register" on our homepage and enter your name, email, and password. We'll send a verification link to activate your account. No credit card required, and you can start building immediately on our free plan. Explore the platform, see what we offer, then upgrade when you're ready for premium features.
                                </p>
                            </div>
                            <div class="bg-gray-100 rounded-lg p-8 flex items-center justify-center">
                                <p class="text-gray-500 text-sm">[Image placeholder: Person using laptop/tablet to create CV]</p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <?php if (!isLoggedIn()): ?>
                                <button type="button" data-open-register class="inline-block bg-blue-600 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                    Create Your Free Account
                                </button>
                            <?php else: ?>
                                <a href="/profile.php" class="inline-block bg-blue-600 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-blue-700 transition-colors">
                                    Go to Your Profile
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Building Your Professional Profile</h3>
                        <p class="text-gray-700 text-lg leading-relaxed mb-4">
                            Start with your personal information: name (required), email, phone, location, and optionally your LinkedIn profile and professional photo. We keep it simple—only your name is required. Everything else is optional, though we've found that complete profiles make stronger impressions on employers.
                        </p>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            Next, build out your CV sections: work experience, education, projects, skills, certifications, professional memberships, and interests. Work in any order and edit anytime—your CV grows with your career.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Why Choose Us -->
            <section id="why-choose-us" class="mb-16 scroll-mt-64">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Why Choose Simple CV Builder?</h2>
                
                <div class="space-y-8">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">A Living, Breathing CV</h3>
                        <div class="grid md:grid-cols-2 gap-6 items-center mb-4">
                            <div>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    Your CV isn't a static document—it's a dynamic webpage you can share with a simple link. Update your job title or add a certification once, and everyone with your link sees the latest version instantly. No more sending updated PDFs or wondering which version employers are viewing.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed">
                                    Your CV link (<code class="bg-gray-100 px-2 py-1 rounded text-sm font-mono">/cv/@your-username</code>) always shows current information. But we haven't forgotten traditional PDFs—download print-ready versions whenever you need them, complete with a QR code linking back to your online CV.
                                </p>
                            </div>
                            <div class="bg-gray-100 rounded-lg p-8 flex items-center justify-center">
                                <p class="text-gray-500 text-sm">[Image placeholder: CV link being shared on mobile/desktop]</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">More Than Just a CV Builder</h3>
                        <p class="text-gray-700 text-lg leading-relaxed mb-4">
                            We're a complete career management platform. Every account includes an <a href="/job-applications-features.php" class="text-blue-600 hover:text-blue-800 underline font-medium">integrated job application tracker</a>. Manage your entire job search from one place: track applications, monitor progress, set follow-up reminders, and attach documents. See at a glance which applications are pending, interviewing, or resulted in offers.
                        </p>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            This integration is seamless. Your CV and job applications live together, making your search more organised and less stressful. And it's included with every account, even the free one.
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Flexibility That Adapts to You</h3>
                        <p class="text-gray-700 text-lg leading-relaxed mb-4">
                            Tailor your CV for different opportunities. With paid plans, drag-and-drop to reorder work experience, select which sections to include in PDFs, and choose from professional templates with customisable colours. Your CV evolves with your needs—add entries as you gain experience, reorganise as your focus shifts, and adjust presentation for your industry.
                        </p>
                        <div class="bg-gray-100 rounded-lg p-8 flex items-center justify-center mt-4">
                            <p class="text-gray-500 text-sm">[Image placeholder: CV customisation interface showing drag-and-drop]</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- What to Expect -->
            <section id="what-to-expect" class="mb-16 scroll-mt-64">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">What to Expect</h2>
                
                <div class="space-y-8">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Comprehensive CV Sections</h3>
                        <p class="text-gray-700 text-lg leading-relaxed mb-4">
                            Build a complete professional profile with these sections:
                        </p>
                        <div class="grid md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <p class="text-gray-700 leading-relaxed mb-2"><strong>Personal Profile</strong> — Contact information, professional photo, LinkedIn, bio, and tagline</p>
                                <p class="text-gray-700 leading-relaxed mb-2"><strong>Professional Summary</strong> — Overview of your background, key strengths, and specialisations</p>
                                <p class="text-gray-700 leading-relaxed mb-2"><strong>Work Experience</strong> — Unlimited positions with job titles, companies, dates, descriptions, and categorised responsibilities (much more readable than long bullet lists)</p>
                            </div>
                            <div>
                                <p class="text-gray-700 leading-relaxed mb-2"><strong>Additional Sections</strong> — Education, projects with images and links, categorised skills, certifications, professional qualifications, memberships, and interests</p>
                            </div>
                        </div>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            You're not locked into a rigid template. Include as much or little detail as you want, and adjust anytime.
                        </p>
                        <div class="bg-gray-100 rounded-lg p-8 flex items-center justify-center mt-4">
                            <p class="text-gray-500 text-sm">[Image placeholder: CV sections interface showing all available sections]</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Real-Time Updates and Instant Sharing</h3>
                        <p class="text-gray-700 text-lg leading-relaxed mb-4">
                            Changes appear instantly on your online CV. Add an achievement before tomorrow's interview, and your CV link automatically shows it—no new PDF needed. Your unique, memorable link works on any device and displays your most current information.
                        </p>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            Share your link via email, LinkedIn, social media, email signature, or business cards. This convenience saves countless hours during your job search.
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Professional PDF Downloads</h3>
                        <div class="grid md:grid-cols-2 gap-6 items-center">
                            <div>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    Generate professional, print-ready PDFs that are ATS-friendly (Applicant Tracking System compatible). Every PDF includes a QR code linking to your online CV. With paid plans, access multiple templates with customisable colours and choose which sections to include—create different versions for different opportunities while maintaining one master CV online.
                                </p>
                            </div>
                            <div class="bg-gray-100 rounded-lg p-8 flex items-center justify-center">
                                <p class="text-gray-500 text-sm">[Image placeholder: PDF download example with QR code]</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Job Application Tracking -->
            <section id="job-tracking" class="mb-16 scroll-mt-64">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Job Application Tracking</h2>
                
                <div class="space-y-8">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Your Complete Job Search Command Centre</h3>
                        <div class="grid md:grid-cols-2 gap-6 items-center mb-4">
                            <div>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    Track every application from one dashboard. Record company names, job titles, application dates, and status—from "Interested" through "Applied," "Interviewing," to "Offered" or "Accepted." Add notes about conversations, set follow-up reminders, attach your CV and cover letters, and track salary ranges and locations.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    Everything is organised and searchable. View statistics about your job search: applications submitted, success rates, and process stages. No more scrambling to find application details or remember which jobs you've applied to.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed">
                                    It's all integrated with your CV. Access, share, or update your CV while tracking applications—no switching between tools. This integration is included with every account, even the free one.
                                </p>
                            </div>
                            <div class="bg-gray-100 rounded-lg p-8 flex items-center justify-center">
                                <p class="text-gray-500 text-sm">[Image placeholder: Job application tracker dashboard]</p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <?php if (isLoggedIn()): ?>
                                <a href="/job-applications.php" class="inline-block bg-green-600 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-green-700 transition-colors">
                                    Go to Job Applications
                                </a>
                            <?php else: ?>
                                <button type="button" data-open-register class="inline-block bg-green-600 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-green-700 transition-colors">
                                    Create Account to Get Started
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pricing & Plans -->
            <section id="pricing" class="mb-16 scroll-mt-64">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Pricing & Plans</h2>
                
                <div class="space-y-8">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Start Free, Upgrade When You're Ready</h3>
                        <div class="grid md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    <strong>Free Plan</strong> — One work experience entry, one project, three skills, basic template, and your online CV link. No credit card required.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    <strong>Pro Monthly</strong> — TBC/month for unlimited sections, professional templates, customisable colours, PDF downloads, and priority support.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    <strong>Pro Annual</strong> — TBC/year (save over 40% vs monthly billing).
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed">
                                    <strong>Lifetime Plan</strong> — TBC one-time payment for lifetime access to all premium features. A special beta offer—pay once, never worry about subscriptions again, and get all future features at no additional cost.
                                </p>
                            </div>
                            <div class="bg-gray-100 rounded-lg p-8 flex items-center justify-center">
                                <p class="text-gray-500 text-sm">[Image placeholder: Pricing comparison or plan features]</p>
                            </div>
                        </div>
                        <p class="text-gray-700 text-lg leading-relaxed mb-4">
                            All payments are processed securely through Stripe. Upgrade or downgrade anytime from your dashboard. Changes take effect immediately for upgrades, or at the end of your billing period for downgrades.
                        </p>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            <strong>How to upgrade:</strong> Create your free account, start building, then upgrade from your dashboard when ready. No pressure.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Security & Privacy -->
            <section id="security" class="mb-16 scroll-mt-64">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Security & Privacy</h2>
                
                <div class="space-y-8">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Your Data, Your Control</h3>
                        <div class="grid md:grid-cols-2 gap-6 items-center mb-4">
                            <div>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    Your data is encrypted and stored securely using industry-standard practices. All data transmission uses secure connections (HTTPS), and we regularly update our systems against security threats.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    Control who sees your CV with privacy settings: "Private" (only you), "Organisation" (your organisation if applicable), or "Public" (anyone with your link). Change visibility anytime.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed">
                                    We never share your data with third parties for marketing, never sell your information, and never use it for targeted ads. Your data is yours—we're just the platform that helps you manage it.
                                </p>
                            </div>
                            <div class="bg-gray-100 rounded-lg p-8 flex items-center justify-center">
                                <p class="text-gray-500 text-sm">[Image placeholder: Security/privacy illustration]</p>
                            </div>
                        </div>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            Payments are processed through Stripe. We never see or store your card details. Your financial information is protected by bank-level encryption and security standards.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Call to Action -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg p-10 text-center text-white">
                <h2 class="text-3xl font-bold mb-4">Ready to Build Your Professional CV?</h2>
                <p class="text-blue-100 text-lg mb-6 max-w-2xl mx-auto leading-relaxed">
                    Creating a standout CV doesn't have to be complicated or expensive. Start with our free plan, build your CV at your own pace, and upgrade when you're ready to unlock premium features. Your career journey starts here.
                </p>
                <?php if (isLoggedIn()): ?>
                    <a href="/profile.php" class="inline-block bg-white text-blue-600 px-8 py-4 rounded-md font-semibold text-lg hover:bg-blue-50 transition-colors">
                        Go to Your Profile
                    </a>
                <?php else: ?>
                    <button type="button" data-open-register class="inline-block bg-white text-blue-600 px-8 py-4 rounded-md font-semibold text-lg hover:bg-blue-50 transition-colors">
                        Create Your Free Account
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>
    <?php partial('auth-modals'); ?>

    <script>
        // Smooth scroll for anchor links with offset for sticky header
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const headerOffset = 150; // Header (64px) + sticky nav (~56px) + extra spacing (30px)
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>
