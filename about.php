<?php
/**
 * About page - Creator story and tools
 */

require_once __DIR__ . '/php/helpers.php';

$projects = [
    [
        'title' => 'Service Design Thinking',
        'description' => 'My approach to service design and organisational development.',
        'url' => 'https://wellis321.github.io/org-playbook/',
        'image' => 'org-playbook.png',
    ],
    [
        'title' => 'Staff Service',
        'description' => 'Centralised staff management designed for organisations where data ownership is critical. You\'re in control - set up your workflows and systems to meet your needs. Integrate with existing HR, rota, and recruitment systems via API or MCP.',
        'url' => 'https://salmon-tarsier-739827.hostingersite.com/public/landing.php',
        'image' => 'staff-servicce.png',
    ],
    [
        'title' => 'Digital ID for Social Care Providers',
        'description' => 'Secure, verifiable employee identification designed for organisations where trust is critical. Replace paper-based ID cards with modern, secure technology that protects your staff and service users. Integrate with Microsoft 365 for seamless single sign-on and automatic user management.',
        'url' => 'https://lightslategrey-weasel-963972.hostingersite.com/index.php',
        'image' => 'Digital-ID.png',
    ],
    [
        'title' => 'EngageTrack',
        'description' => 'Behaviour Observation & Analysis. A powerful behaviour tracking app designed for Positive Behaviour Support (PBS) teams, educators, therapists, and behaviour analysts. Capture real-time interval-based observations with precision and data-driven insights.',
        'url' => 'https://lavenderblush-hummingbird-585670.hostingersite.com/',
        'image' => 'engagetrack.png',
    ],
    [
        'title' => 'Simple Strategic Plans',
        'description' => 'The comprehensive platform for organisations of all types to create, manage, and track their strategic plans. From charities to public sector bodies, we help you turn vision into action.',
        'url' => 'https://rosybrown-cod-114553.hostingersite.com/',
        'image' => 'Strategi-Plans.png',
    ],
    [
        'title' => 'Simple Data Cleaner',
        'description' => 'Clean Your UK Data Instantly. The only UK data validation tool that processes everything in your browser. Personally Identifiable Information (PII) never leaves your device - perfect for GDPR compliance. Phone numbers, NI numbers, postcodes, bank sort codes, bank account numbers, and name splitting.',
        'url' => 'https://simple-data-cleaner.com/',
        'image' => 'data-cleaner.png',
    ],
    [
        'title' => 'Simple CV Builder',
        'description' => 'Create a professional CV that stands out, updates in real-time, and can be shared as a simple link.',
        'url' => 'https://simple-cv-builder.com/',
        'image' => 'CV.png',
    ],
    [
        'title' => 'Scottish Digital Age Verification',
        'description' => 'Age verification for digital services.',
        'url' => 'https://wellis321.github.io/national-age/',
        'image' => 'age.png',
    ],
    [
        'title' => 'Policy Manager',
        'description' => 'Streamline policy management and compliance. Create, distribute, and track policies across your organisation.',
        'url' => 'https://policy-app-khaki.vercel.app/',
        'image' => 'new-policy.png',
    ],
    [
        'title' => 'Social Care Contracts Management',
        'description' => 'Social Care Contract Management System for Scotland. A comprehensive platform for social care providers in Scotland to manage contracts, track procurement processes, monitor rates, and stay informed about the latest developments in social care commissioning and funding.',
        'url' => 'https://whitesmoke-ostrich-519588.hostingersite.com/',
        'image' => 'SCCM.png',
    ],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'About | Simple CV Builder',
        'metaDescription' => 'About the creator of Simple CV Builder - tools and resources for social wellbeing, built with poka yoke and needs-led design.',
        'canonicalUrl' => APP_URL . '/about.php',
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content" role="main">
        <!-- Hero section -->
        <div class="relative h-[344px] flex flex-col justify-center bg-cover bg-center bg-no-repeat overflow-hidden" style="background-image: url('/static/images/about/projects/neilston.png');">
            <div class="absolute inset-0 bg-black/40" aria-hidden="true"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full">
                <div class="flex flex-col lg:flex-row items-center gap-8">
                    <div class="flex-1 text-white">
                        <h1 class="text-4xl font-bold mb-4 drop-shadow-lg" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">About</h1>
                        <p class="text-xl text-indigo-100 drop-shadow-md space-y-2" style="text-shadow: 0 1px 3px rgba(0,0,0,0.5);">
                            <span class="block">Hello. I'm William.</span>
                            <span class="block">I created this site as I want to help you build your professional CV to get the job you want.</span>
                            <span class="block mt-3 text-lg">If you're a charity, non-profit, or can't afford a subscription, get in touch — I'd like to help make Simple CV Builder accessible to everyone.</span>
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <img src="/static/images/about/profile-image.png" alt="William Ellis" class="w-48 h-48 lg:w-56 lg:h-56 rounded-full object-cover border-4 border-white/30 shadow-xl" />
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Philosophy section -->
            <section class="mb-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Philosophy</h2>
                <blockquote class="border-l-4 border-blue-500 pl-8 pr-8 py-8 bg-gray-50 rounded-r-xl shadow-sm italic text-gray-700">
                    <p class="leading-relaxed mb-4">
                        I build tools and resources to help make improvements to our social wellbeing. By working from principles and methodologies, such as <span class="inline-block relative group cursor-help not-italic"><strong class="border-b border-dotted border-gray-400">agile</strong><span class="absolute left-0 bottom-full mb-2 hidden group-hover:block w-72 p-3 text-sm text-left text-gray-800 bg-white border border-gray-200 rounded-lg shadow-lg z-50 not-italic">An iterative approach to development that emphasises collaboration, flexibility, and responding to change.</span></span>, <span class="inline-block relative group cursor-help not-italic"><strong class="border-b border-dotted border-gray-400">discovery</strong><span class="absolute left-0 bottom-full mb-2 hidden group-hover:block w-72 p-3 text-sm text-left text-gray-800 bg-white border border-gray-200 rounded-lg shadow-lg z-50 not-italic">The process of understanding user needs, problems, and context before designing solutions.</span></span>, <span class="inline-block relative group cursor-help not-italic"><strong class="border-b border-dotted border-gray-400">needs-led design</strong><span class="absolute left-0 bottom-full mb-2 hidden group-hover:block w-72 p-3 text-sm text-left text-gray-800 bg-white border border-gray-200 rounded-lg shadow-lg z-50 not-italic">Design that starts from and is driven by user needs rather than assumptions or technology. Understanding what people actually need comes first; the solution follows.</span></span>, and <span class="inline-block relative group cursor-help not-italic"><strong class="border-b border-dotted border-gray-400">poka yoke</strong><span class="absolute left-0 bottom-full mb-2 hidden group-hover:block w-72 p-3 text-sm text-left text-gray-800 bg-white border border-gray-200 rounded-lg shadow-lg z-50 not-italic">A Japanese term meaning &quot;mistake-proofing&quot; — design that prevents errors before they occur, making it hard or impossible to do the wrong thing.</span></span>, I believe systems, software and tools should be intuitive — they should do what people expect, without requiring training.
                    </p>
                    <p class="leading-relaxed mb-0">
                        You can see my service design thinking in the projects below.
                    </p>
                </blockquote>
            </section>

            <!-- Get in touch / bespoke work -->
            <section class="mb-16">
                <div class="rounded-xl border-2 border-blue-200 bg-blue-50 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Got an idea or a custom need?</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        If you have an improvement you'd like to see, or you're interested in a bespoke template or customisation, I'm very open to talking about it. Get in touch and we can discuss what might work for you.
                    </p>
                    <a href="#contact-form" class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Contact form below
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </a>
                </div>
            </section>

            <!-- Projects grid -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Other Projects I'm Working On</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($projects as $project): ?>
                        <a href="<?php echo e($project['url']); ?>" target="_blank" rel="noopener noreferrer" class="group bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md hover:border-blue-300 transition-all duration-200">
                            <div class="aspect-video bg-gray-100 flex items-center justify-center overflow-hidden">
                                <?php if (!empty($project['image'])): ?>
                                    <img src="/static/images/about/projects/<?php echo e($project['image']); ?>" alt="<?php echo e($project['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm"><?php echo e($project['title']); ?> image placeholder</span>
                                <?php endif; ?>
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-900 pb-3 mb-3 border-b border-gray-200 group-hover:text-blue-600 transition-colors">
                                    <?php echo e($project['title']); ?>
                                </h3>
                                <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">
                                    <?php echo e($project['description']); ?>
                                </p>
                                <span class="inline-flex items-center mt-4 pt-3 border-t border-gray-200 text-sm font-medium text-blue-600 group-hover:text-blue-700">
                                    Visit site
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Access for charities and those who can't afford subscriptions -->
            <section class="mb-16 pt-12">
                <div class="rounded-xl border-2 border-green-200 bg-green-50 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Access for charities and those who need support</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        If you're a charity, non-profit organisation, or someone who can't afford a subscription, I'd like to help. Get in touch and we can discuss how to make Simple CV Builder accessible to everyone.
                    </p>
                    <a href="#contact-form" class="inline-flex items-center rounded-lg bg-green-600 px-6 py-3 text-base font-semibold text-white shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        Contact me to discuss
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </a>
                </div>
            </section>

            <!-- Contact form - anyone can use, no account required -->
            <section id="contact-form" class="mb-16 scroll-mt-24">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Get in touch</h2>
                    <p class="text-gray-600 mb-6">Have a question, idea, or custom need? Send a message and I'll get back to you.</p>

                    <div id="contact-form-message" class="mb-4 hidden rounded-md border px-4 py-3 text-sm font-medium"></div>

                    <form id="about-contact-form">
                        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                        <input type="hidden" name="feedback_type" value="other">
                        <input type="hidden" name="page_url" value="">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="contact-name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" id="contact-name" name="name" class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500" placeholder="Your name">
                            </div>
                            <div>
                                <label for="contact-email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="contact-email" name="email" required class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500" placeholder="you@example.com" value="<?php if (isLoggedIn()) { $u = getCurrentUser(); echo !empty($u['email']) ? e($u['email']) : ''; } ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="contact-message" class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                            <textarea id="contact-message" name="message" rows="5" required minlength="10" class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500" placeholder="Your message..."></textarea>
                            <p class="mt-1 text-xs text-gray-500">Minimum 10 characters</p>
                        </div>
                        <button type="button" id="contact-form-submit" class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-2.5 text-base font-semibold text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="contact-submit-text">Send message</span>
                            <span id="contact-submit-loading" class="hidden">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Sending...
                            </span>
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <?php partial('footer'); ?>

    <?php
    $error = getFlash('error') ?: null;
    $success = getFlash('success') ?: null;
    $needsVerification = getFlash('needs_verification') ?: false;
    $verificationEmail = getFlash('verification_email') ?: null;
    $oldLoginEmail = getFlash('old_login_email') ?: null;
    partial('auth-modals', [
        'error' => $error,
        'success' => $success,
        'needsVerification' => $needsVerification,
        'verificationEmail' => $verificationEmail,
        'oldLoginEmail' => $oldLoginEmail,
    ]);
    ?>
<script>
(function() {
    const form = document.getElementById('about-contact-form');
    const submitBtn = document.getElementById('contact-form-submit');
    if (!form || !submitBtn) return;

    var pageUrlInput = form.querySelector('input[name="page_url"]');
    if (pageUrlInput) pageUrlInput.value = window.location.href;

    submitBtn.addEventListener('click', function() {
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const msgEl = document.getElementById('contact-form-message');
        const submitText = document.getElementById('contact-submit-text');
        const submitLoading = document.getElementById('contact-submit-loading');

        msgEl.classList.add('hidden');
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');

        var pageUrlInput = form.querySelector('input[name="page_url"]');
        if (pageUrlInput) pageUrlInput.value = window.location.href;

        const formData = new FormData(form);
        fetch('/api/submit-feedback.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                msgEl.classList.remove('hidden');
                msgEl.textContent = data.message || data.error || 'Done.';
                msgEl.className = 'mb-4 rounded-md border px-4 py-3 text-sm font-medium ' +
                    (data.success ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-800');
                if (data.success) {
                    form.reset();
                    var pu = form.querySelector('input[name="page_url"]');
                    if (pu) pu.value = window.location.href;
                }
                msgEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            })
            .catch(err => {
                msgEl.classList.remove('hidden');
                msgEl.textContent = 'Something went wrong. Please try again.';
                msgEl.className = 'mb-4 rounded-md border px-4 py-3 text-sm font-medium border-red-200 bg-red-50 text-red-800';
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitText.classList.remove('hidden');
                submitLoading.classList.add('hidden');
            });
    });
})();
</script>
</body>
</html>
