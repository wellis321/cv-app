<?php
/**
 * QR Codes in PDFs – feature page
 * Describes QR codes included in PDF exports that link back to the online CV.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'QR Codes in PDFs';
$canonicalUrl = APP_URL . '/qr-codes-pdf.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Optionally include a QR code in your PDF exports linking back to your online CV. Employers can scan to view the latest version instantly—perfect for printed CVs and networking.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-gray-800 via-gray-700 to-gray-900 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1481627834810-7b0dc0a24339?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-gray-800/90 via-gray-700/90 to-gray-900/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">PDF exports</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    QR Codes in PDFs
                </h1>
                <p class="mt-6 text-xl text-gray-200 max-w-2xl mx-auto leading-relaxed">
                    Optionally include a <strong class="text-white">QR code</strong> in your PDF exports linking back to your online CV. Employers can scan to view the latest version instantly—<strong class="text-white">perfect for printed CVs and networking</strong>.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-gray-800 shadow-lg hover:bg-gray-100 transition-colors">
                            Export PDF
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-gray-800 shadow-lg hover:bg-gray-100 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        How it works
                    </a>
                </div>
            </div>
        </section>

        <!-- The QR Code Feature -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Bridge print and digital
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        PDFs are great for printing and email attachments, but they become outdated. QR codes solve this—optionally include a QR code in your PDF exports that links to your always-current online CV.
                    </p>
                </div>

                <!-- QR Code Example -->
                <div class="mb-12 flex justify-center">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-8 border-2 border-gray-200">
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-4 font-medium">Example QR code in PDF:</p>
                            <div class="bg-white rounded-lg p-6 inline-block border-2 border-gray-300 shadow-sm">
                                <?php
                                $exampleCvUrl = 'https://simple-cv-builder.com/cv/@simple-cv-example';
                                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&ecc=M&margin=0&color=374151&bgcolor=ffffff&data=' . urlencode($exampleCvUrl);
                                ?>
                                <img src="<?php echo e($qrCodeUrl); ?>" alt="QR Code linking to example CV" class="w-48 h-48 mx-auto mb-4" style="image-rendering: crisp-edges;" width="200" height="200" />
                                <p class="text-xs text-gray-600 mb-2">Scan to view online CV</p>
                                <a href="/cv/@simple-cv-example" target="_blank" class="text-xs text-gray-500 hover:text-gray-700 underline">View example CV →</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-8 md:grid-cols-2">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gray-600 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Optional QR Code in PDFs</h3>
                        </div>
                        <p class="text-gray-700 mb-4">When you export your CV as a PDF, you can choose to include a QR code. Simply check the "Include QR Code" option before exporting. The QR code links directly to your online CV at <code class="bg-gray-200 px-1 rounded text-sm">/cv/@your-username</code>.</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-gray-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Easy to include—just check the option</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-gray-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Placed prominently on the PDF</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-gray-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Works with any QR code scanner app</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gray-600 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Always Current</h3>
                        </div>
                        <p class="text-gray-700 mb-4">When someone scans the QR code, they see your online CV—which always shows your latest information. Even if they have an old PDF, scanning the QR code shows your current CV.</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-gray-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Update your CV anytime—QR code stays the same</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-gray-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Perfect for networking events and printed CVs</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-gray-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Employers see your latest achievements instantly</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Use Cases -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    Perfect for these situations
                </h2>
                <div class="grid gap-6 md:grid-cols-3">
                    <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-600 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Networking Events</h3>
                        <p class="text-sm text-gray-600">Print PDFs with QR codes for business cards or handouts. People scan to view your latest CV on their phone.</p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-600 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Email Attachments</h3>
                        <p class="text-sm text-gray-600">Attach PDFs to job applications. If employers want the latest version, they can scan the QR code.</p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-600 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Printed CVs</h3>
                        <p class="text-sm text-gray-600">Print your CV for in-person interviews or career fairs. The QR code ensures employers can access your latest information.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How QR codes work
                </h2>
                <div class="space-y-8">
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-600 text-white font-bold text-lg">1</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Export your CV as PDF</h3>
                            <p class="text-gray-600">Click "Export PDF" in your CV editor. Check the "Include QR Code" option if you want a QR code in your PDF. The PDF is generated with your CV content and optionally includes a QR code.</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-600 text-white font-bold text-lg">2</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">QR code links to your online CV</h3>
                            <p class="text-gray-600">The QR code contains your unique CV link (<code class="bg-gray-100 px-1 rounded text-sm">/cv/@your-username</code>). When scanned, it opens your online CV in a browser.</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-600 text-white font-bold text-lg">3</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Employers scan and see latest version</h3>
                            <p class="text-gray-600">Anyone with your PDF can scan the QR code with their phone's camera or a QR scanner app. They'll see your always-current online CV.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 bg-gradient-to-br from-gray-800 via-gray-700 to-gray-900">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start using QR codes in your PDFs
                </h2>
                <p class="mt-4 text-gray-200 max-w-xl mx-auto">
                    Choose to include a QR code when exporting your PDF. Check the option before generating—it's that simple.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-gray-800 shadow-lg hover:bg-gray-100 transition-colors">
                            Export PDF
                        </a>
                        <a href="/online-cv-username.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Learn about online CVs
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-gray-800 shadow-lg hover:bg-gray-100 transition-colors">
                            Create free account
                        </button>
                        <a href="/online-cv-username.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Learn about online CVs
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
