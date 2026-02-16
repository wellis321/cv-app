<?php
/**
 * CV Templates – feature page
 * Describes professional CV templates with customisable colours and styling options.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Free CV Templates UK | Professional CV Templates';
$canonicalUrl = APP_URL . '/cv-templates-feature.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Free professional CV templates UK. Choose from customisable CV templates with different colours and styles. Create multiple CV versions for different job applications.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-pink-600 via-rose-600 to-red-600 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-pink-600/90 via-rose-600/90 to-red-600/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">Professional designs</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Free Professional CV Templates - Choose Your Style
                </h1>
                <p class="mt-6 text-xl text-pink-50 max-w-2xl mx-auto leading-relaxed">
                    Choose from professional CV templates with <strong class="text-white">customisable colours</strong>. Create different versions for different opportunities while maintaining <strong class="text-white">one master CV online</strong>.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-pink-600 shadow-lg hover:bg-pink-50 transition-colors">
                            Choose template
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-pink-600 shadow-lg hover:bg-pink-50 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="/cv/@simple-cv-example" target="_blank" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        View example CV →
                    </a>
                </div>
            </div>
        </section>

        <!-- Template Options -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Professional templates
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Choose a template that matches your industry and personal style. All templates are ATS-friendly and look great in print and online.
                    </p>
                </div>

                <p class="text-center text-sm text-gray-500 mb-8">
                    See what each template looks like when you generate a PDF.<br>
                    Add your content in the content editor, then go to <strong>Preview &amp; Generate PDF</strong> to try each template with your CV.
                </p>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 p-6">
                        <button type="button" class="aspect-[400/520] w-full rounded-lg border border-gray-200 bg-white overflow-hidden mb-4 shadow-sm block cursor-zoom-in hover:opacity-95 transition-opacity text-left" data-template-lightbox="/static/images/templates/minimal.png" aria-label="View Minimal template larger">
                            <img src="/static/images/templates/minimal.png" alt="Minimal template – PDF preview" class="w-full h-full object-cover object-top" onerror="this.onerror=null; this.src='/static/images/templates/placeholder-preview.svg'; this.alt='Minimal template preview placeholder';">
                        </button>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-500 text-white mb-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Minimal</h3>
                        <p class="text-sm text-gray-600 mb-4">Clean, simple design perfect for traditional industries. Available on free plan.</p>
                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">Free plan</span>
                        <?php if (isLoggedIn()): ?>
                            <p class="mt-3"><a href="/preview-cv.php" class="text-sm text-gray-600 hover:text-blue-600 underline">Preview &amp; generate PDF →</a></p>
                        <?php endif; ?>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 p-6">
                        <button type="button" class="aspect-[400/520] w-full rounded-lg border border-blue-200 bg-white overflow-hidden mb-4 shadow-sm block cursor-zoom-in hover:opacity-95 transition-opacity text-left" data-template-lightbox="/static/images/templates/blue.png" aria-label="View Professional Blue template larger">
                            <img src="/static/images/templates/blue.png" alt="Professional Blue template – PDF preview" class="w-full h-full object-cover object-top" onerror="this.onerror=null; this.src='/static/images/templates/placeholder-preview.svg'; this.alt='Professional Blue template preview placeholder';">
                        </button>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500 text-white mb-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Professional Blue</h3>
                        <p class="text-sm text-gray-600 mb-4">Modern, professional design with customisable colours. Perfect for corporate roles.</p>
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">Pro plan</span>
                        <?php if (isLoggedIn()): ?>
                            <p class="mt-3"><a href="/preview-cv.php" class="text-sm text-gray-600 hover:text-blue-600 underline">Preview &amp; generate PDF →</a></p>
                        <?php endif; ?>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border-2 border-purple-200 p-6">
                        <button type="button" class="aspect-[400/520] w-full rounded-lg border border-purple-200 bg-white overflow-hidden mb-4 shadow-sm block cursor-zoom-in hover:opacity-95 transition-opacity text-left" data-template-lightbox="/static/images/templates/modern.png" aria-label="View Modern template larger">
                            <img src="/static/images/templates/modern.png" alt="Modern template – PDF preview" class="w-full h-full object-cover object-top" onerror="this.onerror=null; this.src='/static/images/templates/placeholder-preview.svg'; this.alt='Modern template preview placeholder';">
                        </button>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500 text-white mb-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Modern</h3>
                        <p class="text-sm text-gray-600 mb-4">Contemporary design with bold accents. Great for creative and tech industries.</p>
                        <span class="inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800">Pro plan</span>
                        <?php if (isLoggedIn()): ?>
                            <p class="mt-3"><a href="/preview-cv.php" class="text-sm text-gray-600 hover:text-blue-600 underline">Preview &amp; generate PDF →</a></p>
                        <?php endif; ?>
                    </div>

                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl border-2 border-slate-200 p-6">
                        <button type="button" class="aspect-[400/520] w-full rounded-lg border border-slate-200 bg-white overflow-hidden mb-4 shadow-sm block cursor-zoom-in hover:opacity-95 transition-opacity text-left" data-template-lightbox="/static/images/templates/classic.png" aria-label="View Classic template larger">
                            <img src="/static/images/templates/classic.png" alt="Classic template – PDF preview" class="w-full h-full object-cover object-top" onerror="this.onerror=null; this.src='/static/images/templates/placeholder-preview.svg'; this.alt='Classic template preview placeholder';">
                        </button>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-600 text-white mb-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Classic</h3>
                        <p class="text-sm text-gray-600 mb-4">Traditional, formal design with navy accents. Ideal for academia, government and legal sectors.</p>
                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">Free plan</span>
                        <?php if (isLoggedIn()): ?>
                            <p class="mt-3"><a href="/preview-cv.php" class="text-sm text-gray-600 hover:text-blue-600 underline">Preview &amp; generate PDF →</a></p>
                        <?php endif; ?>
                    </div>

                    <div class="bg-gradient-to-br from-cyan-50 to-sky-50 rounded-xl border-2 border-cyan-200 p-6">
                        <button type="button" class="aspect-[400/520] w-full rounded-lg border border-cyan-200 bg-white overflow-hidden mb-4 shadow-sm block cursor-zoom-in hover:opacity-95 transition-opacity text-left" data-template-lightbox="/static/images/templates/structured.png" aria-label="View Structured template larger">
                            <img src="/static/images/templates/structured.png" alt="Structured template – PDF preview" class="w-full h-full object-cover object-top" onerror="this.onerror=null; this.src='/static/images/templates/placeholder-preview.svg'; this.alt='Structured template preview placeholder';">
                        </button>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-500 text-white mb-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Structured</h3>
                        <p class="text-sm text-gray-600 mb-4">Clean, professional layout with light blue accents. Career highlights and shaded section headers.</p>
                        <span class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-medium text-cyan-800">Pro plan</span>
                        <?php if (isLoggedIn()): ?>
                            <p class="mt-3"><a href="/preview-cv.php" class="text-sm text-gray-600 hover:text-blue-600 underline">Preview &amp; generate PDF →</a></p>
                        <?php endif; ?>
                    </div>

                    <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-xl border-2 border-red-200 p-6">
                        <button type="button" class="aspect-[400/520] w-full rounded-lg border border-red-200 bg-white overflow-hidden mb-4 shadow-sm block cursor-zoom-in hover:opacity-95 transition-opacity text-left" data-template-lightbox="/static/images/templates/academic.pdf.png" aria-label="View Academic template larger">
                            <img src="/static/images/templates/academic.pdf.png" alt="Academic template – PDF preview" class="w-full h-full object-cover object-top" onerror="this.onerror=null; this.src='/static/images/templates/placeholder-preview.svg'; this.alt='Academic template preview placeholder';">
                        </button>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-700 text-white mb-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Academic</h3>
                        <p class="text-sm text-gray-600 mb-4">Traditional academic CV with red accent headings and clean structure. Ideal for research and academia.</p>
                        <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">Pro plan</span>
                        <?php if (isLoggedIn()): ?>
                            <p class="mt-3"><a href="/preview-cv.php" class="text-sm text-gray-600 hover:text-blue-600 underline">Preview &amp; generate PDF →</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Customisation -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    Customise your template
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="bg-white rounded-xl border-2 border-pink-200 p-8">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-pink-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Customise Colours</h3>
                        </div>
                        <p class="text-gray-700 mb-4">With Pro plans, customise template colours to match your personal brand or industry preferences. Choose accent colours that make your CV stand out while remaining professional.</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-pink-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Choose from preset colour schemes</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-pink-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Customise accent colours</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-pink-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Preview changes instantly</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-rose-200 p-8">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-rose-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Multiple Templates</h3>
                        </div>
                        <p class="text-gray-700 mb-4">Create different CV variants with different templates for different opportunities. Your master CV stays online, and you can export PDFs with different templates as needed.</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Switch templates anytime</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Each CV variant can use a different template</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>All templates are ATS-friendly</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-blue-200 p-8">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">QR Code in PDF</h3>
                        </div>
                        <p class="text-gray-700 mb-4">Pro plans let you add a QR code to your PDF. Prospective employers can scan it to go straight to your online CV—keep your full profile one tap away.</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Scan to view your full online CV</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Easy for recruiters on the go</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Include or hide when generating PDF</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    Why templates matter
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl border-2 border-pink-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-pink-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">ATS-Compatible</h3>
                        <p class="text-sm text-gray-600">All templates are designed to be ATS-friendly, ensuring your CV passes through applicant tracking systems.</p>
                    </div>

                    <div class="bg-gradient-to-br from-rose-50 to-red-50 rounded-xl border-2 border-rose-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-rose-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Professional Design</h3>
                        <p class="text-sm text-gray-600">Templates are designed by professionals to look great in print and online, making a strong first impression.</p>
                    </div>

                    <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl border-2 border-red-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Save Time</h3>
                        <p class="text-sm text-gray-600">No need to design from scratch. Choose a template, customise colours, and focus on your content.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Explore All Features -->
        <section class="py-16 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                    Explore All Features
                </h2>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    This is just one of many features we offer. Discover everything Simple CV Builder can do for your job search and career development.
                </p>
                <div class="mt-8">
                    <a href="/all-features.php" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        View All Features
                    </a>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 bg-gradient-to-br from-pink-600 via-rose-600 to-red-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Choose your template today
                </h2>
                <p class="mt-4 text-pink-50 max-w-xl mx-auto">
                    Free plan includes the Minimal template. Upgrade to Pro for premium templates, customisable colours, and a QR code so employers can scan straight to your online CV.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-pink-600 shadow-lg hover:bg-pink-50 transition-colors">
                            Choose template
                        </a>
                        <a href="/all-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            All features
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-pink-600 shadow-lg hover:bg-pink-50 transition-colors">
                            Create free account
                        </button>
                        <a href="/all-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Explore features
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php partial('footer'); ?>
    <?php if (!isLoggedIn()): ?>
        <?php partial('auth-modals'); ?>
    <?php endif; ?>

    <!-- Template image lightbox -->
    <div id="template-lightbox" class="fixed inset-0 z-[60] hidden overflow-y-auto" role="dialog" aria-modal="true" aria-label="Template preview">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/70 transition-opacity" data-close-template-lightbox aria-hidden="true"></div>
            <div class="relative max-w-4xl w-full flex items-center justify-center">
                <button type="button" class="absolute right-2 top-2 z-10 rounded-full bg-white/90 p-2 text-gray-600 hover:bg-white hover:text-gray-900 transition-colors" data-close-template-lightbox aria-label="Close">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <img id="template-lightbox-image" src="" alt="" class="max-h-[90vh] w-auto rounded-lg shadow-2xl object-contain">
            </div>
        </div>
    </div>
    <script>
(function() {
    const lightbox = document.getElementById('template-lightbox');
    const lightboxImage = document.getElementById('template-lightbox-image');
    if (!lightbox || !lightboxImage) return;

    function openLightbox(src, alt) {
        lightboxImage.src = src;
        lightboxImage.alt = alt || 'Template preview';
        lightbox.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        var closeBtn = lightbox.querySelector('button[data-close-template-lightbox]');
        if (closeBtn) setTimeout(function() { closeBtn.focus(); }, 50);
    }
    function closeLightbox() {
        lightbox.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-template-lightbox]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            openLightbox(this.dataset.templateLightbox, this.getAttribute('aria-label') || 'Template preview');
        });
    });
    document.querySelectorAll('[data-close-template-lightbox]').forEach(function(btn) {
        btn.addEventListener('click', closeLightbox);
    });
    lightbox.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
    });
})();
    </script>
</body>
</html>
