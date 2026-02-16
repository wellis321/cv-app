<?php
/**
 * Smart Text Extraction – feature page
 * Describes extracting text from job description files (PDF, Word, Excel) into the job application.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Smart Text Extraction';
$canonicalUrl = APP_URL . '/smart-text-extraction.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Extract text from job description files (PDF, Word, Excel) with one click. Populate your job application field instantly—optional AI formatting for clearer sections and paragraphs.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gray-900 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1481627834810-7b0dc0a24339?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gray-900/70" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
                <span class="inline-flex items-center rounded-full bg-green-500/90 px-4 py-1.5 text-sm font-semibold text-white shadow-sm">Job applications</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Smart Text Extraction
                </h1>
                <p class="mt-6 text-xl text-gray-200 max-w-2xl leading-relaxed">
                    Upload a job description file—PDF, Word, or Excel—and <strong class="text-white">extract the text in one click</strong>. It fills your job description field so you can edit, use AI to format it, or generate a tailored CV. No copy‑paste.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                            Open job list
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        How it works
                    </a>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-16 bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center">
                    How it works
                </h2>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto text-center">
                    Add a job application, upload a file, then extract its text into the job description—optionally with AI formatting.
                </p>

                <div class="mt-16 space-y-20">
                    <!-- Step 1 -->
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-green-100 text-green-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Add an application and upload a file</h3>
                            <p class="mt-3 text-gray-600">
                                In <strong>My CV → Job applications</strong> (or the content editor job list), add a new application or open an existing one. Upload the job description as a PDF, Word, or Excel file using the file upload area.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <figure>
                                <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/file-uploads/extract-file.png" aria-label="View upload job description file image larger">
                                    <img src="/static/images/file-uploads/extract-file.png" alt="Upload job description file" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                                </button>
                                <figcaption class="mt-2 text-sm text-gray-500">Upload PDF, Word, or Excel to your job application</figcaption>
                            </figure>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-green-100 text-green-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Click Extract Text</h3>
                            <p class="mt-3 text-gray-600">
                                Next to the uploaded file, click <strong>Extract Text</strong>. The text is pulled from the document and inserted into the <strong>Job description</strong> field. You can replace existing text or extract into an empty description.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <figure>
                                <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/keyword-extraction/add-a-job-description.png" aria-label="View extract text button image larger">
                                    <img src="/static/images/keyword-extraction/add-a-job-description.png" alt="Extract text button" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                                </button>
                                <figcaption class="mt-2 text-sm text-gray-500">Extract Text fills the job description field in one click</figcaption>
                            </figure>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-green-100 text-green-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Edit, format with AI, or generate your CV</h3>
                            <p class="mt-3 text-gray-600">
                                Use the extracted text as-is or tick <strong>Format with AI when extracting</strong> for clearer sections and paragraphs. The same description is used when you generate an AI CV variant or extract keywords for the role.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <figure>
                                <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/template-customisation/choose-a-template.png" aria-label="View use description for AI CV image larger">
                                    <img src="/static/images/template-customisation/choose-a-template.png" alt="Use description for AI CV" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                                </button>
                                <figcaption class="mt-2 text-sm text-gray-500">Use the job description for AI CV generation and keyword extraction</figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Supported formats -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    Supported file types
                </h2>
                <p class="mt-4 text-gray-600">
                    Text is extracted from <strong>PDF</strong>, <strong>Word</strong> (.doc, .docx), <strong>Excel</strong> (.xls, .xlsx), and <strong>plain text / CSV</strong>. Tables and structure are preserved where possible. For images, OCR is used when available on the server.
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-white border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 shadow-sm">PDF</span>
                    <span class="inline-flex items-center rounded-full bg-white border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 shadow-sm">Word (.doc, .docx)</span>
                    <span class="inline-flex items-center rounded-full bg-white border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 shadow-sm">Excel (.xls, .xlsx)</span>
                    <span class="inline-flex items-center rounded-full bg-white border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 shadow-sm">Text / CSV</span>
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
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-green-600 to-emerald-700 px-8 py-12 md:px-12 text-center text-white shadow-xl">
                    <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image: url('<?php echo e($img('1557804506-669a67965ba0', 1200)); ?>');" aria-hidden="true"></div>
                    <div class="relative">
                        <h2 class="text-2xl font-bold sm:text-3xl">
                            Stop copy‑pasting job descriptions
                        </h2>
                        <p class="mt-4 text-green-100 max-w-xl mx-auto">
                            Upload the file, click Extract Text, and use it for your application and AI CV. Part of the job application tracker—no extra setup.
                        </p>
                        <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                            <?php if (isLoggedIn()): ?>
                                <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-green-600 shadow-lg hover:bg-green-50 transition-colors">
                                    Open job list
                                </a>
                                <a href="/job-applications.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                                    Job applications
                                </a>
                            <?php else: ?>
                                <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-green-600 shadow-lg hover:bg-green-50 transition-colors">
                                    Create free account
                                </button>
                                <a href="/job-applications-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                                    Job tracker features
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php partial('footer'); ?>
    <?php if (!isLoggedIn()): ?>
        <?php partial('auth-modals'); ?>
    <?php endif; ?>
    <?php partial('image-lightbox'); ?>
</body>
</html>
