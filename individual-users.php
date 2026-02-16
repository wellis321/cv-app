<?php
/**
 * For Individual Users - Documentation and Guide
 * Information for individual users building their CVs
 */

require_once __DIR__ . '/php/helpers.php';

$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };

?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
    <?php partial('head', [
        'pageTitle' => 'Free CV Builder UK | Create Professional CVs Online | Simple CV Builder',
        'metaDescription' => 'Free CV builder UK for job seekers. Create professional CVs online, share a link, track job applications, and generate AI cover letters. Start free.',
        'canonicalUrl' => APP_URL . '/individual-users.php',
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content" role="main">
        <!-- Hero Section -->
        <div class="relative min-h-[42vh] flex flex-col justify-center bg-gray-900 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/static/images/individuals/individuals.png');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gray-900/70" aria-hidden="true"></div>
            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Free CV Builder UK – Create Professional CVs Online
                </h1>
                <p class="mt-6 text-xl text-gray-200 max-w-2xl mx-auto leading-relaxed">
                    Free CV builder UK for job seekers. Create a professional CV that stands out, updates in real-time, and can be shared as a simple link. Track job applications and generate AI cover letters—all free.
                </p>
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
                        <div class="grid md:grid-cols-2 gap-6 items-stretch mb-6">
                            <div class="flex flex-col">
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    Creating your professional CV takes just minutes. No downloads, no software installation—everything works right in your browser on any device.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed">
                                    Click "Register" on our homepage and enter your name, email, and password. We'll send a verification link to activate your account. Start free, or <strong>try any paid plan with a 7-day free trial</strong>—1 week, 1 month, or 3 months. Cancel anytime. No pressure.
                                </p>
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
                            <div class="min-h-0">
                                <button type="button" class="w-full h-full block cursor-zoom-in hover:opacity-95 transition-opacity text-left rounded-lg overflow-hidden" data-image-lightbox="/static/images/individuals/getting-started.png" aria-label="View getting started image larger">
                                    <img src="/static/images/individuals/getting-started.png" alt="Free CV builder UK – example CV profile showing completed professional profile" class="h-full w-full rounded-lg border border-gray-200 shadow-sm object-cover" width="600" height="340" />
                                </button>
                            </div>
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
                        <div class="grid md:grid-cols-2 gap-6 items-stretch mb-4">
                            <div>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    Your CV isn't a static document—it's a dynamic webpage you can share with a simple link. Update your job title or add a certification once, and everyone with your link sees the latest version instantly. No more sending updated PDFs or wondering which version employers are viewing.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed">
                                    Your CV link (<code class="bg-gray-100 px-2 py-1 rounded text-sm font-mono">/cv/@your-username</code>) always shows current information. But we haven't forgotten traditional PDFs—download print-ready versions whenever you need them, complete with a QR code linking back to your online CV.
                                </p>
                            </div>
                            <div class="min-h-0">
                                <img src="/static/images/individuals/living--breathimg.png" alt="QR code CV being scanned on mobile devices, showing a living breathing CV" class="h-full w-full rounded-lg border border-gray-200 shadow-sm object-cover" width="600" height="340" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">More Than Just a CV Builder</h3>
                        <p class="text-gray-700 text-lg leading-relaxed mb-4">
                            We're a complete career management platform. Every account includes an <a href="/job-applications-features.php" class="text-blue-600 hover:text-blue-800 underline font-medium">integrated job application tracker</a>. <strong>Save job listings in one click</strong> from any job board or company site—then fill in the details later. Manage your entire job search from one place: track applications, set priorities and closing-date reminders, monitor progress, and attach documents. See at a glance which applications are pending, interviewing, or resulted in offers.
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
                        <div class="mt-4">
                            <img src="/static/images/individuals/flexibility.png" alt="Wireframe sketches showing flexible design and customization options" class="w-full rounded-lg border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
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
                        <div class="mt-4">
                            <img src="<?php echo e($img('1531403009284-440f080d1e12', 600)); ?>" alt="CV sections interface showing all available sections" class="w-full rounded-lg border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
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
                        <div class="grid md:grid-cols-2 gap-6 items-stretch">
                            <div>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    Generate professional, print-ready PDFs that are ATS-friendly (Applicant Tracking System compatible). All plans include PDF export. Pro plans add multiple templates with customisable colours, a QR code in the PDF linking to your online CV, and the ability to choose which sections to include—create different versions for different opportunities while maintaining one master CV online.
                                </p>
                            </div>
                            <div class="min-h-0">
                                <button type="button" class="w-full h-full block cursor-zoom-in hover:opacity-95 transition-opacity text-left rounded-lg overflow-hidden" data-image-lightbox="/static/images/individuals/professional-pdf.png" aria-label="View professional PDF download example larger">
                                    <img src="/static/images/individuals/professional-pdf.png" alt="Professional PDF download - example CV with two-column layout" class="h-full w-full rounded-lg border border-gray-200 shadow-sm object-cover" width="600" height="340" />
                                </button>
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
                        <div class="grid md:grid-cols-2 gap-6 items-stretch mb-4">
                            <div>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    Track every application from one dashboard. Record company names, job titles, application dates, and status—from "Interested" through "Applied," "Interviewing," to "Offered" or "Accepted." Add notes about conversations, set follow-up reminders, attach your CV and cover letters, and track salary ranges and locations.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    Everything is organised and searchable. View statistics about your job search: applications submitted, success rates, and process stages. No more scrambling to find application details or remember which jobs you've applied to.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    It's all integrated with your CV. Access, share, or update your CV while tracking applications—no switching between tools. From any job (in the content editor under Manage Jobs), you can click <strong>Generate AI CV for this job</strong> to create a tailored CV in one click, or <strong>Tailor CV for this job…</strong> to choose which sections to tailor. Variants are linked to the job so you always know which CV was used for which application.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed">
                                    This integration is included with every account, even the free one.
                                </p>
                            </div>
                            <div class="min-h-0">
                                <button type="button" class="w-full h-full block cursor-zoom-in hover:opacity-95 transition-opacity text-left rounded-lg overflow-hidden" data-image-lightbox="/static/images/individuals/complete-job-search.png" aria-label="View complete job search image larger">
                                    <img src="/static/images/individuals/complete-job-search.png" alt="Complete job search - interconnected network of opportunities and applications" class="h-full w-full rounded-lg border border-gray-200 shadow-sm object-cover" width="600" height="340" />
                                </button>
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
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Pricing & Plans</h3>
                        <div class="grid md:grid-cols-2 gap-6 items-stretch mb-4">
                            <div>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    <strong>Basic access (Free)</strong> — CV builder, templates, resume sharing, PDF export. Limited job tracking and AI. No credit card required.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                                    <strong>1 week</strong> — £4.99/week. 7-day free trial. Cancel anytime.
                                </p>
                                <p class="text-gray-700 text-lg leading-relaxed">
                                    <strong>1 month</strong> — £14.99/month. 7-day free trial. Cancel anytime.
                                </p>
                            </div>
                            <div class="min-h-0">
                                <button type="button" class="w-full h-full block cursor-zoom-in hover:opacity-95 transition-opacity text-left rounded-lg overflow-hidden" data-image-lightbox="/static/images/individuals/pricing.png" aria-label="View pricing and plans image larger">
                                    <img src="/static/images/individuals/pricing.png" alt="Pricing and plans - Basic access, 7-day unlimited, monthly" class="h-full w-full rounded-lg border border-gray-200 shadow-sm object-cover" width="600" height="340" />
                                </button>
                            </div>
                        </div>
                        <p class="text-gray-700 text-lg leading-relaxed mb-4">
                            All payments are processed securely through Stripe. Start free, or choose 1 week (£4.99), 1 month (£14.99), or 3 months (£34.99)—all include a 7-day free trial. Cancel anytime. See your <a href="/subscription.php" class="text-blue-600 hover:text-blue-800 underline font-medium">Plan</a> page for details.
                        </p>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            <strong>How it works:</strong> Create your account and use the free plan, or start a paid plan (1 week, 1 month, or 3 months). All paid plans include a 7-day free trial—cancel before the trial ends to avoid being charged. No pressure.
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
                        <div class="grid md:grid-cols-2 gap-6 items-stretch mb-4">
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
                            <div class="min-h-0">
                                <button type="button" class="w-full h-full block cursor-zoom-in hover:opacity-95 transition-opacity text-left rounded-lg overflow-hidden" data-image-lightbox="/static/images/individuals/your-data-your-control.png" aria-label="View your data your control image larger">
                                    <img src="/static/images/individuals/your-data-your-control.png" alt="Your data, your control - security and privacy illustration" class="h-full w-full rounded-lg border border-gray-200 shadow-sm object-cover" width="600" height="340" />
                                </button>
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
                    Start free, or try any paid plan with a 7-day free trial. Build your CV at your own pace—cancel anytime. Your career journey starts here.
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

    <!-- Image lightbox -->
    <div id="image-lightbox" class="fixed inset-0 z-[60] hidden overflow-y-auto" role="dialog" aria-modal="true" aria-label="Image preview">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/70 transition-opacity" data-close-image-lightbox aria-hidden="true"></div>
            <div class="relative max-w-4xl w-full flex items-center justify-center">
                <button type="button" class="absolute right-2 top-2 z-10 rounded-full bg-white/90 p-2 text-gray-600 hover:bg-white hover:text-gray-900 transition-colors" data-close-image-lightbox aria-label="Close">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <img id="image-lightbox-img" src="" alt="" class="max-h-[90vh] w-auto rounded-lg shadow-2xl object-contain">
            </div>
        </div>
    </div>

    <script>
        // Image lightbox for getting-started and other images
        (function() {
            var lightbox = document.getElementById('image-lightbox');
            var lightboxImg = document.getElementById('image-lightbox-img');
            if (!lightbox || !lightboxImg) return;
            function openLightbox(src, alt) {
                lightboxImg.src = src;
                lightboxImg.alt = alt || 'Image preview';
                lightbox.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                var closeBtn = lightbox.querySelector('button[data-close-image-lightbox]');
                if (closeBtn) setTimeout(function() { closeBtn.focus(); }, 50);
            }
            function closeLightbox() {
                lightbox.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
            document.querySelectorAll('[data-image-lightbox]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    openLightbox(this.dataset.imageLightbox, this.getAttribute('aria-label') || 'Image preview');
                });
            });
            document.querySelectorAll('[data-close-image-lightbox]').forEach(function(btn) {
                btn.addEventListener('click', closeLightbox);
            });
            lightbox.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeLightbox();
            });
        })();

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
