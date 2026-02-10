<?php
/**
 * Application Questions – feature page
 * Describes the AI-powered application question answering feature.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'AI Application Questions';
$canonicalUrl = APP_URL . '/application-questions-feature.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Get AI-powered help answering application form questions. Add questions from job applications and generate tailored answers based on the role and your CV.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-green-600 via-emerald-600 to-teal-600 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1450101491212-3f7e0d4dff11?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-green-600/90 via-emerald-600/90 to-teal-600/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">AI-powered</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    AI Application Questions
                </h1>
                <p class="mt-6 text-xl text-green-50 max-w-2xl mx-auto leading-relaxed">
                    Application forms often ask tricky questions. <strong class="text-white">Get AI-powered answers</strong> tailored to each role and your CV. Add questions from any application form, generate draft answers, and refine them to match your voice.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-green-600 shadow-lg hover:bg-green-50 transition-colors">
                            Open job list
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-green-600 shadow-lg hover:bg-green-50 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        How it works
                    </a>
                </div>
            </div>
        </section>

        <!-- Why Application Questions Matter -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Why application questions matter
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Many job applications include questions beyond your CV. Answering them well can make the difference between getting an interview and being overlooked.
                    </p>
                </div>

                <div class="grid gap-12 lg:grid-cols-2 lg:items-stretch">
                    <div class="flex flex-col">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Stand out with tailored answers</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Application forms often ask questions like "Why do you want this role?" or "What makes you a good fit?" Generic answers won't cut it—employers want to see that you've researched the role and understand how your experience aligns with their needs.
                        </p>
                        <p class="text-gray-600 leading-relaxed">
                            With AI-powered question answering, you get draft answers that are tailored to the specific job description and your CV. The AI analyzes the role requirements, your experience, and generates answers that highlight relevant skills and achievements—giving you a strong starting point that you can then refine to match your authentic voice.
                        </p>
                    </div>
                    <div class="flex items-center">
                        <img src="<?php echo e($img('1557804506-669a67965ba0', 800)); ?>" alt="Application form with questions" class="w-full rounded-xl border border-gray-200 shadow-lg object-cover aspect-video" width="800" height="450" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Key Features -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Everything you need to answer application questions
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        From adding questions to generating tailored answers, everything you need is in one place.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-white rounded-xl border-2 border-green-200 p-6 shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Add questions easily</h3>
                        <p class="text-sm text-gray-600">Paste or type questions directly from application forms. Add optional instructions like "max 100 words" or "use bullet points" to guide the AI.</p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-emerald-200 p-6 shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">AI-generated answers</h3>
                        <p class="text-sm text-gray-600">Generate tailored answers using free Browser AI. The AI analyzes the job description, your CV, and the question to create relevant, compelling answers.</p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-teal-200 p-6 shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Edit and refine</h3>
                        <p class="text-sm text-gray-600">Review and edit AI-generated answers to match your voice and style. Save your answers and keep them linked to each job application.</p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-green-200 p-6 shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Answer instructions</h3>
                        <p class="text-sm text-gray-600">Specify format requirements like word limits, bullet points, or specific structure. The AI follows these instructions when generating answers.</p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-emerald-200 p-6 shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Linked to applications</h3>
                        <p class="text-sm text-gray-600">Each question and answer stays linked to its job application. Never lose track of which answers go with which role.</p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-teal-200 p-6 shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">100% free</h3>
                        <p class="text-sm text-gray-600">All AI features run in your browser using free Browser AI—no API keys, no costs, no limits. Generate as many answers as you need.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-20 bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How it works
                </h2>
                <div class="space-y-12">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-green-100 text-green-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Add questions from application forms</h3>
                            <p class="mt-3 text-gray-600">
                                When viewing a job application, scroll to the "Application questions" section. Paste or type questions from the application form. You can also add optional instructions like "max 150 words" or "use bullet points" to guide the AI.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <img src="<?php echo e($img('1504384308090-c894fdcc538d', 600)); ?>" alt="Adding questions to a job application" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-green-100 text-green-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Generate AI answers</h3>
                            <p class="mt-3 text-gray-600">
                                Click "Generate answer with AI" for any question. Our free Browser AI analyzes the job description, your CV, and the question to create a tailored answer. The AI considers your experience, skills, and achievements to craft responses that highlight your fit for the role.
                            </p>
                            <p class="mt-3 text-sm text-gray-500">
                                The AI runs entirely in your browser—no cloud services, no API keys, completely free and private.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <img src="<?php echo e($img('1531403009284-440f080d1e12', 600)); ?>" alt="AI generating answers to application questions" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-green-100 text-green-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Review and refine</h3>
                            <p class="mt-3 text-gray-600">
                                Review the AI-generated answer and edit it to match your voice and style. Add personal touches, adjust the tone, and ensure it accurately reflects your experience. When you're happy with it, click "Save answer" to store it with the job application.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <img src="<?php echo e($img('1586281380349-632531db7ed4', 600)); ?>" alt="Editing and refining AI-generated answers" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section class="py-20 bg-gradient-to-br from-green-50 to-emerald-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Why use AI for application questions?
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Save time, improve quality, and never struggle with application forms again.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2">
                    <div class="bg-white rounded-xl border-2 border-green-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-green-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Save time</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Application forms can take hours to complete, especially when you're applying to multiple roles. AI-generated answers give you a strong starting point in seconds, so you can focus on refining rather than starting from scratch.
                        </p>
                        <p class="text-gray-700">
                            Instead of staring at a blank text box wondering how to answer "Why do you want this role?", you get a tailored draft that you can edit and personalize.
                        </p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-emerald-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Better answers</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            The AI analyzes the job description and your CV to create answers that highlight relevant experience and skills. It connects your background to the role requirements, helping you demonstrate fit more effectively.
                        </p>
                        <p class="text-gray-700">
                            Answers are tailored to each specific role, so you're not submitting generic responses that could apply to any job.
                        </p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-teal-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-teal-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Stay organized</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            All questions and answers are stored with each job application. When you're ready to submit, everything is in one place—no searching through notes or documents to find your answers.
                        </p>
                        <p class="text-gray-700">
                            Copy answers directly from the app when filling out application forms, or reference them during interviews to stay consistent.
                        </p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-green-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-green-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">100% private</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            All AI processing happens in your browser using free Browser AI technology. Your CV data, job descriptions, and answers never leave your device—complete privacy and security.
                        </p>
                        <p class="text-gray-700">
                            No cloud services, no API keys, no data sharing. Everything runs locally in your browser, so your information stays private.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Example Use Cases -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Common application questions
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Application forms often ask similar questions. Here's how AI can help you answer them effectively.
                    </p>
                </div>

                <div class="space-y-8">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-200 p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">"Why do you want this role?"</h3>
                        <p class="text-gray-700 mb-4">
                            The AI analyzes the job description to identify what the employer values, then connects your relevant experience and skills to show genuine interest and fit.
                        </p>
                        <div class="bg-white rounded-lg p-4 border border-green-200">
                            <p class="text-sm text-gray-600 mb-2"><strong>Example AI approach:</strong></p>
                            <p class="text-sm text-gray-700 italic">"Based on your CV, the AI highlights your experience in [relevant area] and connects it to the role's focus on [key requirement]. It emphasizes specific achievements that align with the company's goals."</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border-2 border-emerald-200 p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">"What makes you a good fit for this position?"</h3>
                        <p class="text-gray-700 mb-4">
                            The AI identifies key requirements from the job description and matches them with your skills and experience, creating a compelling case for why you're the right candidate.
                        </p>
                        <div class="bg-white rounded-lg p-4 border border-emerald-200">
                            <p class="text-sm text-gray-600 mb-2"><strong>Example AI approach:</strong></p>
                            <p class="text-sm text-gray-700 italic">"The AI extracts skills like 'project management' and 'team leadership' from the job description, then finds matching experiences in your CV to demonstrate fit with concrete examples."</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-teal-50 to-green-50 rounded-xl border-2 border-teal-200 p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">"Describe a challenging project you've worked on"</h3>
                        <p class="text-gray-700 mb-4">
                            The AI reviews your CV to identify relevant projects, then structures the answer using frameworks like STAR (Situation, Task, Action, Result) to create a compelling narrative.
                        </p>
                        <div class="bg-white rounded-lg p-4 border border-teal-200">
                            <p class="text-sm text-gray-600 mb-2"><strong>Example AI approach:</strong></p>
                            <p class="text-sm text-gray-700 italic">"The AI selects a project from your work experience that aligns with the role, structures it clearly, and emphasizes outcomes and skills relevant to the position."</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Free Browser AI Section -->
        <section class="py-20 bg-gradient-to-br from-blue-50 to-indigo-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl border-2 border-blue-200 shadow-xl p-10 md:p-12">
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <div class="flex items-center gap-4 mb-6">
                                <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-blue-500 text-white">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                </div>
                                <h2 class="text-3xl font-bold text-gray-900">100% Free Browser AI</h2>
                            </div>
                            <p class="text-lg text-gray-700 mb-6">
                                All AI features run directly in your browser using free Browser AI technology. No cloud services, no API keys, no costs—everything happens locally on your device.
                            </p>
                            <div class="space-y-4 mb-8">
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">No API keys needed</h4>
                                        <p class="text-sm text-gray-600">Works immediately for all users—no configuration, no setup, no costs.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Completely private</h4>
                                        <p class="text-sm text-gray-600">All processing happens in your browser. Your CV data and answers never leave your device.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Unlimited use</h4>
                                        <p class="text-sm text-gray-600">Generate as many answers as you need—no limits, no restrictions, completely free.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-800"><strong>Note:</strong> Browser AI requires a modern browser with WebGPU or WebGL support. The first time you generate an answer, the AI model loads (this may take a few minutes), but subsequent generations are faster.</p>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-8 border-2 border-blue-200">
                            <div class="bg-white rounded-lg p-6 shadow-lg">
                                <p class="text-sm text-gray-600 mb-4 font-medium">Example workflow:</p>
                                <div class="space-y-3">
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                            <span class="text-xs font-semibold text-green-700">1</span>
                                        </div>
                                        <p class="text-sm text-gray-700">Add question: "Why do you want this role?"</p>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                            <span class="text-xs font-semibold text-green-700">2</span>
                                        </div>
                                        <p class="text-sm text-gray-700">Click "Generate answer with AI"</p>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                            <span class="text-xs font-semibold text-green-700">3</span>
                                        </div>
                                        <p class="text-sm text-gray-700">Review and edit the AI-generated answer</p>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                            <span class="text-xs font-semibold text-green-700">4</span>
                                        </div>
                                        <p class="text-sm text-gray-700">Save and copy when ready to submit</p>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        <section class="py-16 bg-gradient-to-br from-green-600 to-emerald-700">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start answering application questions with AI
                </h2>
                <p class="mt-4 text-green-100 max-w-xl mx-auto">
                    Application questions are included with every account. Add your first question and see how AI can help you craft compelling answers.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-green-600 shadow-lg hover:bg-green-50 transition-colors">
                            Open job list
                        </a>
                        <a href="/all-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            All features
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-green-600 shadow-lg hover:bg-green-50 transition-colors">
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
</body>
</html>
