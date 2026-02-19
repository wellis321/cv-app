<?php
/**
 * CV Preview & PDF Generation Page
 * Allows users to select sections and generate PDF with QR code
 * When variant_id is in the URL, loads that variant's data for preview/PDF.
 */

require_once __DIR__ . '/php/helpers.php';
require_once __DIR__ . '/php/cv-data.php';
require_once __DIR__ . '/php/cv-variants.php';

requireAuth();

$userId = getUserId();
$variantId = get('variant_id');

// Load CV data - from variant if variant_id given and valid, else master
$cvData = null;
if ($variantId) {
    $cvVariant = getCvVariant($variantId, $userId);
    if ($cvVariant) {
        $cvData = loadCvVariantData($variantId);
        if ($cvData && isset($cvData['variant'])) {
            $profile = db()->fetchOne("SELECT * FROM profiles WHERE id = ?", [$userId]);
            $cvData['profile'] = $profile;
        } else {
            $cvData = null;
        }
    }
}
if (!$cvData) {
    $cvData = loadCvData($userId);
}
$profile = $cvData['profile'];

// Ensure default visibility flags are present
if ($profile) {
    $profile['show_photo'] = $profile['show_photo'] ?? 1;
    $profile['show_photo_pdf'] = $profile['show_photo_pdf'] ?? 1;
    $profile['show_qr_code'] = $profile['show_qr_code'] ?? ($profile['show_photo'] ? 0 : 1);
}

// Format date helper
function formatCvDate($date, $format = 'dd/mm/yyyy') {
    if (empty($date)) return '';
    $timestamp = strtotime($date);
    if ($timestamp === false) return $date;

    switch ($format) {
        case 'mm/dd/yyyy':
            return date('m/d/Y', $timestamp);
        case 'yyyy-mm-dd':
            return date('Y-m-d', $timestamp);
        case 'dd/mm/yyyy':
        default:
            return date('d/m/Y', $timestamp);
    }
}

$dateFormat = $profile['date_format_preference'] ?? 'dd/mm/yyyy';
$cvUrl = APP_URL . '/cv/@' . $profile['username'];
$profileShowPhotoCv = $profile['show_photo'] ?? 1;
$profileShowPhotoPdf = $profile['show_photo_pdf'] ?? 1;
$profileShowQrCode = $profile['show_qr_code'] ?? ($profileShowPhotoCv ? 0 : 1);
$profileShowQrCodePdfDefault = $profileShowPhotoPdf ? $profileShowQrCode : 1;

$subscriptionContext = getUserSubscriptionContext($userId);
$subscriptionFrontendContext = buildSubscriptionFrontendContext($subscriptionContext);

// Nav bar (same as content-editor): variants + quick links back to editor
$cvVariants = getUserCvVariants($userId);
$masterVariantId = getOrCreateMasterVariant($userId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview & PDF - CV Builder</title>
    <link rel="stylesheet" href="/static/css/tailwind.css">
    <script>
        window.addEventListener('load', function() {
            if (typeof QRCode === 'undefined') {
                console.warn('QRCode library not available after page load')
            }
        })
    </script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfmake@0.3.3/build/pdfmake.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfmake@0.3.3/build/vfs_fonts.js"></script>
    <script type="module" src="/js/pdf-generator.js?v=<?php echo time(); ?>"></script>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>
    <?php partial('content-editor/cv-nav-bar', [
        'cvVariants' => $cvVariants,
        'masterVariantId' => $masterVariantId,
        'isPreviewPage' => true,
    ]); ?>

    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Preview & Generate PDF</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Section Selection Panel: sticky on wrapper (no overflow); card scrolls when taller than viewport -->
            <div id="generate-pdf" class="lg:col-span-1 lg:sticky lg:top-24 lg:self-start scroll-mt-4">
                <div class="bg-white shadow rounded-lg p-6 lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto">
                    <!-- Primary action: always visible -->
                    <button id="generate-pdf-button" onclick="generatePDF()" class="w-full bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-sm mb-2">
                        Generate PDF
                    </button>
                    <button type="button" id="update-preview-button" class="w-full bg-gray-100 text-gray-800 px-6 py-2 rounded-md border border-gray-300 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium mb-6">
                        Update Preview
                    </button>
                    <?php if (!planPdfEnabled($subscriptionContext)): ?>
                        <p class="mb-6 text-sm text-gray-500">
                            PDF downloads are available on Pro plans.
                            <a href="/subscription.php" class="text-blue-600 hover:text-blue-800 underline">Upgrade now</a>.
                        </p>
                    <?php endif; ?>

                    <!-- Collapsible: PDF Style (template selector) -->
                    <details class="sidebar-section group border-b border-gray-200 pb-4 mb-4">
                        <summary class="flex items-center justify-between cursor-pointer list-none py-1 text-sm font-medium text-gray-700 hover:text-gray-900 select-none">
                            <span>PDF Style</span>
                            <svg class="h-4 w-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                    <div class="mt-3">
                        <label for="template-select" class="block text-sm font-medium text-gray-700 mb-2">
                            Template
                        </label>
                        <select id="template-select" class="w-full bg-white border-2 border-gray-300 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 transition-colors">
                            <option value="">Loading templates...</option>
                        </select>
                        <p class="mt-2 text-xs text-gray-500" id="template-description">
                            Clean layout with blue accent lines and structured typography.
                        </p>
                        <?php if (!empty($subscriptionFrontendContext['allowedTemplateIds']) && count($subscriptionFrontendContext['allowedTemplateIds']) === 1): ?>
                            <p class="mt-2 text-xs text-gray-500">
                                Upgrade to unlock additional template designs and colour themes.
                            </p>
                        <?php endif; ?>
                    </div>
                    </details>

                    <!-- Collapsible: Photo & QR -->
                    <details class="sidebar-section group border-b border-gray-200 pb-4 mb-4">
                        <summary class="flex items-center justify-between cursor-pointer list-none py-1 text-sm font-medium text-gray-700 hover:text-gray-900 select-none">
                            <span>Photo & QR Code</span>
                            <svg class="h-4 w-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                    <div class="mt-3 space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   id="include-photo"
                                   class="mr-2"
                                   <?php echo $profileShowPhotoPdf ? 'checked' : ''; ?>>
                            <span>Include Profile Photo</span>
                        </label>
                        <p class="mt-2 text-xs text-gray-500">
                            This sets the default when you first open the preview. You can still toggle the photo before generating the PDF.
                        </p>
                    </div>

                    <div class="mt-6">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   id="include-qr"
                                   class="mr-2"
                                   <?php echo $profileShowQrCodePdfDefault ? 'checked' : ''; ?>>
                            <span>Include QR Code</span>
                        </label>
                        <p class="mt-2 text-xs text-gray-500">
                            The QR code will appear in the header if the photo is hidden; otherwise it is placed at the bottom of the PDF.
                        </p>
                    </div>
                    </details>

                    <!-- Collapsible: Select Sections -->
                    <details class="sidebar-section group border-b border-gray-200 pb-4 mb-4">
                        <summary class="flex items-center justify-between cursor-pointer list-none py-1 text-sm font-medium text-gray-700 hover:text-gray-900 select-none">
                            <span>Select Sections</span>
                            <svg class="h-4 w-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                    <div class="mt-3 space-y-3 pl-0">
                        <label class="flex items-center">
                            <input type="checkbox" id="section-profile" checked class="mr-2">
                            <span>Personal Profile</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="section-summary" checked class="mr-2">
                            <span>Professional Summary</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="section-work" checked class="mr-2">
                            <span>Work Experience</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="section-education" checked class="mr-2">
                            <span>Education</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="section-skills" checked class="mr-2">
                            <span>Skills</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="section-projects" checked class="mr-2">
                            <span>Projects</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="section-certifications" checked class="mr-2">
                            <span>Certifications</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="section-memberships" checked class="mr-2">
                            <span>Professional Memberships</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="section-interests" checked class="mr-2">
                            <span>Interests & Activities</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="section-qualifications" checked class="mr-2">
                            <span>Professional Qualification Equivalence</span>
                        </label>
                    </div>
                    </details>

                    <?php if (planPdfEnabled($subscriptionContext)): ?>
                    <!-- Collapsible: PDF Footer -->
                    <details class="sidebar-section group border-b border-gray-200 pb-4 mb-4">
                        <summary class="flex items-center justify-between cursor-pointer list-none py-1 text-sm font-medium text-gray-700 hover:text-gray-900 select-none">
                            <span>PDF Footer</span>
                            <svg class="h-4 w-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                    <div class="mt-3">
                        <?php if (!subscriptionIsPaid($subscriptionContext)): ?>
                        <p class="text-xs text-gray-600 mb-2">
                            Your PDF includes our branding at the bottom (Simple CV Builder, credit line, and link). Upgrade to a Pro or Lifetime plan to remove the extended message—paid plans show only a simple copyright line and web address.
                        </p>
                        <a href="/subscription.php" class="inline-block mt-2 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">Upgrade to remove branding →</a>
                        <?php else: ?>
                        <p class="text-xs text-gray-500">
                            Paid plans include a minimal footer (© <?php echo date('Y'); ?>, simple-cv-builder.com).
                        </p>
                        <?php endif; ?>
                    </div>
                    </details>
                    <?php endif; ?>

                    <?php if (!empty($subscriptionFrontendContext['templateCustomizationEnabled'])): ?>
                    <!-- Collapsible: Customise Colours -->
                    <details class="sidebar-section group border-b border-gray-200 pb-4 mb-4">
                        <summary class="flex items-center justify-between cursor-pointer list-none py-1 text-sm font-medium text-gray-700 hover:text-gray-900 select-none">
                            <span>Customise Colours</span>
                            <svg class="h-4 w-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                    <div class="mt-3" id="colour-customization-container">
                        <p class="text-xs text-gray-500 mb-3">Choose a preset or pick a custom accent colour.</p>
                        <div class="space-y-2 mb-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="colour-preset" value="default" checked class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">Default (template colours)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="colour-preset" value="conservative" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">Conservative Navy</span>
                                <span class="w-4 h-4 rounded-full border border-gray-300" style="background:#1e3a8a" title="#1e3a8a"></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="colour-preset" value="professional" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">Professional Blue</span>
                                <span class="w-4 h-4 rounded-full border border-gray-300" style="background:#2563eb" title="#2563eb"></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="colour-preset" value="teal" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">Teal</span>
                                <span class="w-4 h-4 rounded-full border border-gray-300" style="background:#0d9488" title="#0d9488"></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="colour-preset" value="purple" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">Purple</span>
                                <span class="w-4 h-4 rounded-full border border-gray-300" style="background:#7c3aed" title="#7c3aed"></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="colour-preset" value="rose" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">Rose</span>
                                <span class="w-4 h-4 rounded-full border border-gray-300" style="background:#e11d48" title="#e11d48"></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="colour-preset" value="custom" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">Custom accent</span>
                            </label>
                        </div>
                        <div id="custom-accent-row" class="hidden mt-2">
                            <div class="flex items-center gap-2">
                                <input type="color" id="custom-accent-color" value="#2563eb" class="h-8 w-12 rounded border border-gray-300 cursor-pointer">
                                <input type="text" id="custom-accent-hex" value="#2563eb" class="flex-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 py-1.5 px-2" maxlength="7" placeholder="#2563eb">
                            </div>
                        </div>
                    </div>
                    </details>
                    <?php endif; ?>

                    <!-- Skill Selection UI (shown when skills section is enabled) -->
                    <details id="skill-selection-container" class="sidebar-section group border-b border-gray-200 pb-4 mb-4 hidden">
                        <summary class="flex items-center justify-between cursor-pointer list-none py-1 text-sm font-medium text-gray-700 hover:text-gray-900 select-none">
                            <span>Select Skills</span>
                            <svg class="h-4 w-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span id="skill-section-title">Select Skills</span>
                            <span id="skill-limit-badge" class="ml-2 text-xs text-gray-500 hidden"></span>
                        </label>
                        <p class="text-xs text-gray-500 mb-3" id="skill-selection-help">
                            Select which skills to include in your PDF. Grouped by category; only selected skills appear in the export.
                        </p>
                        
                        <!-- Skills Checkbox List (grouped by category) -->
                        <div id="skills-checkbox-list" class="border border-gray-200 rounded-md p-3 max-h-72 overflow-y-auto space-y-4">
                            <!-- Skills will be populated here -->
                        </div>
                        
                        <!-- Grid Preview (for grid/column layouts) -->
                        <div id="skills-grid-preview" class="mt-4 hidden">
                            <p class="text-xs font-medium text-gray-700 mb-2">Preview Layout:</p>
                            <div id="skills-grid-container" class="border border-gray-200 rounded-md p-3 bg-gray-50">
                                <!-- Grid will be rendered here -->
                            </div>
                        </div>
                    </div>
                    </details>

                    <a href="/cv.php" class="mt-4 block w-full text-center px-4 py-2.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors">
                        View Online CV →
                    </a>
                </div>
            </div>

            <!-- Preview Panel -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">CV Preview</h2>
                    <div id="cv-preview" class="border border-gray-200 p-8 bg-white min-h-[600px]">
                        <!-- Preview will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

        <script type="module">
        import { DEFAULT_TEMPLATE_ID, getTemplateMeta, getPreviewRenderer, listTemplates } from '/templates/index.js?v=<?php echo time(); ?>';

        <?php
        // Helper to decode HTML entities recursively in arrays
        function decodeEntitiesRecursive($data) {
            if (is_array($data)) {
                return array_map('decodeEntitiesRecursive', $data);
            } elseif (is_string($data)) {
                return html_entity_decode($data, ENT_QUOTES, 'UTF-8');
            }
            return $data;
        }
        $cvDataDecoded = decodeEntitiesRecursive($cvData);
        $profileDecoded = decodeEntitiesRecursive($profile);
        ?>
        const SubscriptionContext = <?php echo json_encode($subscriptionFrontendContext, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        window.SubscriptionContext = SubscriptionContext;
        const siteUrl = <?php echo json_encode(APP_URL, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const allowedTemplateIds = new Set(SubscriptionContext?.allowedTemplateIds || []);
        const previewVariantId = <?php echo json_encode($variantId ?? null, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        let cvData = <?php echo json_encode($cvDataDecoded, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        let profile = <?php echo json_encode($profileDecoded, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const dateFormat = <?php echo json_encode($dateFormat, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const cvUrl = <?php echo json_encode($cvUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        let selectedTemplate = SubscriptionContext?.defaultTemplateId || DEFAULT_TEMPLATE_ID;

        function updateTemplateDescription(templateId) {
            const template = getTemplateMeta(templateId);
            const descEl = document.getElementById('template-description');
            if (descEl) {
                descEl.textContent = template && template.description ? template.description : '';
            }
        }

        function loadQRCodeLibrary() {
            return new Promise((resolve, reject) => {
                if (typeof QRCode !== 'undefined' || typeof window.QRCode !== 'undefined') {
                    resolve();
                    return;
                }

                const existingScript = document.querySelector('script[src*="qrcode"]');
                if (existingScript) {
                    let attempts = 0;
                    const checkInterval = setInterval(() => {
                        attempts++;
                        if (typeof QRCode !== 'undefined' || typeof window.QRCode !== 'undefined') {
                            clearInterval(checkInterval);
                            resolve();
                        } else if (attempts > 20) {
                            clearInterval(checkInterval);
                            console.warn('QRCode library still not loaded after waiting');
                            loadScript();
                        }
                    }, 100);
                    return;
                }

                function loadScript() {
                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js';
                    script.onload = () => {
                        setTimeout(() => {
                            if (typeof QRCode !== 'undefined' || typeof window.QRCode !== 'undefined') {
                                resolve();
                            } else {
                                reject(new Error('QRCode library loaded but not available'));
                            }
                        }, 100);
                    };
                    script.onerror = () => {
                        console.error('Failed to load QRCode library from unpkg');
                        reject(new Error('Failed to load QRCode library'));
                    };
                    document.head.appendChild(script);
                }

                loadScript();
            });
        }

        function getSections() {
            return {
                profile: document.getElementById('section-profile')?.checked ?? true,
                summary: document.getElementById('section-summary')?.checked ?? true,
                work: document.getElementById('section-work')?.checked ?? true,
                education: document.getElementById('section-education')?.checked ?? true,
                skills: document.getElementById('section-skills')?.checked ?? true,
                projects: document.getElementById('section-projects')?.checked ?? true,
                certifications: document.getElementById('section-certifications')?.checked ?? true,
                memberships: document.getElementById('section-memberships')?.checked ?? true,
                interests: document.getElementById('section-interests')?.checked ?? true,
                qualificationEquivalence: document.getElementById('section-qualifications')?.checked ?? true
            };
        }

        function getSelectedTemplate() {
            const templateSelect = document.getElementById('template-select');
            const candidate = templateSelect && templateSelect.value ? templateSelect.value : selectedTemplate || DEFAULT_TEMPLATE_ID;
            if (allowedTemplateIds.size > 0 && !allowedTemplateIds.has(candidate)) {
                return selectedTemplate;
            }
            return candidate;
        }

        const COLOUR_PRESETS = {
            default: {},
            conservative: { header: '#1e3a8a', accent: '#1e3a8a', divider: '#1e3a8a', link: '#1e40af' },
            professional: { header: '#1f2937', accent: '#2563eb', divider: '#2563eb', link: '#2563eb' },
            teal: { header: '#0f172a', accent: '#0d9488', divider: '#0d9488', link: '#0891b2' },
            purple: { header: '#3b0764', accent: '#7c3aed', divider: '#7c3aed', link: '#7c3aed' },
            rose: { header: '#881337', accent: '#e11d48', divider: '#e11d48', link: '#e11d48' }
        };

        function getCustomization() {
            const container = document.getElementById('colour-customization-container');
            if (!container) return {};
            const preset = document.querySelector('input[name="colour-preset"]:checked')?.value || 'default';
            if (preset === 'default') return {};
            if (preset === 'custom') {
                const hex = document.getElementById('custom-accent-hex')?.value?.trim() || '#2563eb';
                const valid = /^#[0-9A-Fa-f]{6}$/.test(hex) ? hex : '#2563eb';
                return { colors: { accent: valid, divider: valid, link: valid, header: valid } };
            }
            const colors = COLOUR_PRESETS[preset];
            return colors ? { colors } : {};
        }

        async function generatePDF() {
            try {
                if (!SubscriptionContext?.pdfEnabled) {
                    const message = 'PDF export is available on Pro plans.';
                    if (SubscriptionContext?.upgradeUrl) {
                        if (confirm(message + ' View upgrade options now?')) {
                            window.location.href = SubscriptionContext.upgradeUrl;
                        }
                    } else {
                        alert(message);
                    }
                    return;
                }

                if (!cvData || !profile) {
                    alert('Error: CV data not loaded. Please refresh the page and try again.');
                    console.error('CV data or profile not loaded');
                    return;
                }

                if (typeof pdfMake === 'undefined') {
                    throw new Error('PDF library not loaded. Please refresh the page.');
                }

                // Show loading state
                const button = document.getElementById('generate-pdf-button');
                const originalText = button?.textContent;
                if (button) {
                    button.disabled = true;
                    button.textContent = 'Generating PDF...';
                }

                // Get selected sections and map to PDF template format
                const sectionsObj = getSections();
                const qualificationsCheckbox = document.getElementById('section-qualifications');
                const sections = {
                    profile: sectionsObj.profile,
                    professionalSummary: sectionsObj.summary,
                    summary: sectionsObj.summary,
                    workExperience: sectionsObj.work,
                    work: sectionsObj.work,
                    education: sectionsObj.education,
                    skills: sectionsObj.skills,
                    projects: sectionsObj.projects,
                    certifications: sectionsObj.certifications,
                    qualificationEquivalence: qualificationsCheckbox ? qualificationsCheckbox.checked : false,
                    memberships: sectionsObj.memberships,
                    interests: sectionsObj.interests
                };

                // Get include photo and QR code settings
                const includePhoto = document.getElementById('include-photo')?.checked ?? true;
                const includeQr = document.getElementById('include-qr')?.checked ?? true;

                // Get selected template
                const selectedTemplate = getSelectedTemplate();

                // Prepare config (include colour customization for Pro users, free plan branding)
                const customization = getCustomization();
                const config = {
                    sections: sections,
                    includePhoto: includePhoto,
                    includeQRCode: includeQr,
                    showFreePlanBranding: !SubscriptionContext?.isPaid,
                    siteUrl: siteUrl || window.location.origin
                };
                if (customization?.colors && Object.keys(customization.colors).length > 0) {
                    config.customization = customization;
                }

                // Build cvData with skills filtered by user's skill selection (only include checked skills)
                const filteredSkills = (cvData.skills || []).filter(s => currentSkillSelection.includes(s.id));
                const cvDataForPdf = { ...cvData, skills: filteredSkills };

                // Build preview-photo URL for pdfmake (supports various photo_url formats)
                const profileWithPhoto = { ...profile };
                if (includePhoto && profile.photo_url) {
                    const origin = window.location.origin;
                    let pdfUrl = null;
                    const m = profile.photo_url.match(/\/storage\/(.+)$/);
                    if (m) {
                        const path = m[1];
                        pdfUrl = origin + '/api/preview-photo.php?path=' + encodeURIComponent(path);
                    } else if (/^profiles\//.test(profile.photo_url) || /^uploads\//.test(profile.photo_url)) {
                        pdfUrl = origin + '/api/preview-photo.php?path=' + encodeURIComponent(profile.photo_url);
                    } else if (/^https?:\/\//.test(profile.photo_url)) {
                        pdfUrl = profile.photo_url;
                    } else if (profile.photo_url.startsWith('/')) {
                        pdfUrl = origin + profile.photo_url;
                    }
                    if (pdfUrl) {
                        profileWithPhoto.photo_url_pdf = pdfUrl;
                    } else {
                        console.warn('[PDF] Could not parse photo_url for PDF:', profile.photo_url?.substring?.(0, 80));
                    }
                }

                let docDefinition = await window.PdfGenerator.buildDocDefinition(
                    cvDataForPdf,
                    profileWithPhoto,
                    config,
                    selectedTemplate,
                    cvUrl,
                    null
                );

                const scrollY = window.scrollY;
                const scrollX = window.scrollX;
                const filename = `${(profile.full_name || 'CV').replace(/[^a-z0-9_\-]/gi, '_')}_CV.pdf`;

                // Try clean copy (fix for React/proxy mutation issues per Stack Overflow)
                const imgs = docDefinition?.images;
                if (imgs?.profilePhoto && typeof imgs.profilePhoto === 'string') {
                    docDefinition.images = { profilePhoto: String(imgs.profilePhoto) };
                }

                // Academic template uses serif font (Georgia/Times) to match preview - register Liberation Serif as 'Times'
                if (selectedTemplate === 'academic' && typeof pdfMake !== 'undefined') {
                    const origin = window.location.origin;
                    const fontBase = origin + '/static/fonts/liberation-serif/';
                    if (!pdfMake.fonts?.Times) {
                        pdfMake.fonts = { ...(pdfMake.fonts || {}), Times: {
                            normal: fontBase + 'LiberationSerif-Regular.ttf',
                            bold: fontBase + 'LiberationSerif-Bold.ttf',
                            italics: fontBase + 'LiberationSerif-Italic.ttf',
                            bolditalics: fontBase + 'LiberationSerif-BoldItalic.ttf'
                        }};
                    }
                }

                try {
                    await pdfMake.createPdf(docDefinition).download(filename);
                } catch (imgErr) {
                    console.error('[PDF] Image error:', imgErr?.message, imgErr);
                    const msg = (imgErr?.message || '').toLowerCase();
                    const isImageError = msg.includes('unknown image format') || msg.includes('invalid image');
                    const hasPhoto = !!profileWithPhoto.photo_url_pdf;
                    if (isImageError && hasPhoto) {
                        delete profileWithPhoto.photo_url_pdf;
                        docDefinition = await window.PdfGenerator.buildDocDefinition(
                            cvDataForPdf,
                            profileWithPhoto,
                            config,
                            selectedTemplate,
                            cvUrl,
                            null
                        );
                        await pdfMake.createPdf(docDefinition).download(filename);
                        alert('PDF downloaded. The profile photo could not be embedded and was omitted.');
                    } else {
                        throw imgErr;
                    }
                }

                // Restore scroll position (PDF download can scroll page)
                requestAnimationFrame(() => {
                    window.scrollTo(scrollX, scrollY);
                });

                // Restore button
                if (button) {
                    button.disabled = false;
                    button.textContent = originalText;
                }

            } catch (error) {
                console.error('PDF generation error:', error);
                alert('Error generating PDF: ' + (error?.message || 'Unknown error'));

                // Restore button on error
                const button = document.getElementById('generate-pdf-button');
                if (button) {
                    button.disabled = false;
                    button.textContent = 'Generate PDF';
                }
            }
        }

        async function renderPreview() {
            const scrollY = window.scrollY;
            const scrollX = window.scrollX;
            try {
                const previewDiv = document.getElementById('cv-preview');
                if (!previewDiv) {
                    console.error('Preview div not found');
                    return;
                }

                if (!cvData || !profile) {
                    previewDiv.innerHTML = '<p class="text-red-600">Error: CV data not loaded. Please refresh the page.</p>';
                    return;
                }

                selectedTemplate = getSelectedTemplate();
                const sections = getSections();
                const includePhoto = document.getElementById('include-photo')?.checked ?? true;
                const includeQr = document.getElementById('include-qr')?.checked ?? true;

                let templateMeta = getTemplateMeta(selectedTemplate);
                const customization = getCustomization();
                if (customization?.colors && Object.keys(customization.colors).length > 0) {
                    templateMeta = { ...templateMeta, colors: { ...templateMeta.colors, ...customization.colors } };
                }
                const previewRenderer = getPreviewRenderer(selectedTemplate);

                if (!previewRenderer || typeof previewRenderer.render !== 'function') {
                    console.warn('Preview renderer not available for template:', selectedTemplate);
                    previewDiv.innerHTML = '<p class="text-red-600">Preview not available for the selected template.</p>';
                    return;
                }

                // Load the actual render function (async loader)
                const renderFunction = await previewRenderer.render();

                // Filter skills to only those selected in the skill selection checkboxes
                const filteredSkills = (cvData.skills || []).filter(s => currentSkillSelection.includes(s.id));
                const cvDataForPreview = { ...cvData, skills: filteredSkills };

                // Normalize photo_url to use current origin (fixes port mismatch when stored URL points to different port)
                let profileForPreview = profile;
                if (profile?.photo_url) {
                    const m = String(profile.photo_url).match(/(\/storage\/.+)$/);
                    if (m) {
                        profileForPreview = { ...profile, photo_url: window.location.origin + m[1] };
                    } else if (profile.photo_url.startsWith('/')) {
                        profileForPreview = { ...profile, photo_url: window.location.origin + profile.photo_url };
                    }
                }

                renderFunction(previewDiv, {
                    cvData: cvDataForPreview,
                    profile: profileForPreview,
                    sections,
                    includePhoto,
                    includeQr,
                    cvUrl,
                    template: templateMeta
                });
            } catch (error) {
                console.error('Error rendering preview:', error);
                const previewDiv = document.getElementById('cv-preview');
                if (previewDiv) {
                    previewDiv.innerHTML = '<p class="text-red-600">Error rendering preview: ' + error.message + '</p>';
                }
            } finally {
                requestAnimationFrame(() => {
                    window.scrollTo(scrollX, scrollY);
                });
            }
        }

        const PREVIEW_STORAGE_KEY = 'preview-cv-prefs';

        function loadPreviewPrefs() {
            try {
                const raw = localStorage.getItem(PREVIEW_STORAGE_KEY);
                return raw ? JSON.parse(raw) : {};
            } catch (e) {
                return {};
            }
        }

        function savePreviewPrefs(prefs) {
            try {
                const current = loadPreviewPrefs();
                localStorage.setItem(PREVIEW_STORAGE_KEY, JSON.stringify({ ...current, ...prefs }));
            } catch (e) { /* ignore */ }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const prefs = loadPreviewPrefs();
            const includePhotoEl = document.getElementById('include-photo');
            const includeQrEl = document.getElementById('include-qr');
            if (includePhotoEl && prefs.includePhoto !== undefined) {
                includePhotoEl.checked = !!prefs.includePhoto;
            }
            if (includeQrEl && prefs.includeQr !== undefined) {
                includeQrEl.checked = !!prefs.includeQr;
            }
            const colourContainer = document.getElementById('colour-customization-container');
            if (colourContainer && prefs.colourPreset) {
                const presetRadio = colourContainer.querySelector(`input[name="colour-preset"][value="${prefs.colourPreset}"]`);
                if (presetRadio) {
                    presetRadio.checked = true;
                    const customRow = document.getElementById('custom-accent-row');
                    if (customRow) customRow.classList.toggle('hidden', prefs.colourPreset !== 'custom');
                }
                if (prefs.customAccentHex && /^#[0-9A-Fa-f]{6}$/.test(prefs.customAccentHex)) {
                    const customColor = document.getElementById('custom-accent-color');
                    const customHex = document.getElementById('custom-accent-hex');
                    if (customColor) customColor.value = prefs.customAccentHex;
                    if (customHex) customHex.value = prefs.customAccentHex;
                }
            }

            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    if (checkbox.id === 'include-photo') {
                        savePreviewPrefs({ includePhoto: checkbox.checked });
                    } else if (checkbox.id === 'include-qr') {
                        savePreviewPrefs({ includeQr: checkbox.checked });
                    }
                    renderPreview();
                });
            });

            const templateSelectEl = document.getElementById('template-select');
            if (templateSelectEl) {
                // Check if listTemplates is available
                if (typeof listTemplates !== 'function') {
                    console.error('listTemplates is not a function. Available:', typeof listTemplates, listTemplates);
                    templateSelectEl.innerHTML = '<option value="professional">Professional Blue (default)</option>';
                    selectedTemplate = DEFAULT_TEMPLATE_ID || 'professional';
                    updateTemplateDescription(selectedTemplate);
                } else {
                    let allTemplates;
                    try {
                        allTemplates = listTemplates();
                        if (!Array.isArray(allTemplates) || allTemplates.length === 0) {
                            console.warn('listTemplates() returned invalid data, using fallback');
                            allTemplates = [{ id: DEFAULT_TEMPLATE_ID || 'professional', name: 'Professional Blue', description: 'Clean layout with blue accent accents and structured typography.' }];
                        }
                    } catch (error) {
                        console.error('Error calling listTemplates():', error);
                        allTemplates = [{ id: DEFAULT_TEMPLATE_ID || 'professional', name: 'Professional Blue', description: 'Clean layout with blue accent accents and structured typography.' }];
                    }
                    
                    let availableTemplates = allTemplates.filter((templateMeta) => {
                    if (allowedTemplateIds.size === 0) {
                        return true;
                    }
                    return allowedTemplateIds.has(templateMeta.id);
                });

                if (availableTemplates.length === 0) {
                    availableTemplates = allTemplates;
                }

                templateSelectEl.innerHTML = '';

                if (Array.isArray(availableTemplates) && availableTemplates.length > 0) {
                    if (!availableTemplates.some((templateMeta) => templateMeta.id === selectedTemplate)) {
                        selectedTemplate = availableTemplates[0]?.id || DEFAULT_TEMPLATE_ID;
                    }

                    availableTemplates.forEach((templateMeta) => {
                        const option = document.createElement('option');
                        option.value = templateMeta.id;
                        option.textContent = templateMeta.name;
                        if (templateMeta.id === selectedTemplate) {
                            option.selected = true;
                        }
                        templateSelectEl.appendChild(option);
                    });
                } else {
                    const fallbackOption = document.createElement('option');
                    fallbackOption.value = DEFAULT_TEMPLATE_ID;
                    fallbackOption.textContent = 'Professional Blue (default)';
                    templateSelectEl.appendChild(fallbackOption);
                    selectedTemplate = DEFAULT_TEMPLATE_ID;
                }

                selectedTemplate = templateSelectEl.value || DEFAULT_TEMPLATE_ID;
                updateTemplateDescription(selectedTemplate);
                templateSelectEl.addEventListener('change', (event) => {
                    if (allowedTemplateIds.size > 0 && !allowedTemplateIds.has(event.target.value)) {
                        event.target.value = selectedTemplate;
                        alert('Upgrade to access this template.');
                        return;
                    }
                    selectedTemplate = event.target.value || DEFAULT_TEMPLATE_ID;
                    updateTemplateDescription(selectedTemplate);
                    renderPreview();
                });
                } // Close the else block for listTemplates check

            // Colour customization – preset change, custom picker, preview updates
            const colourContainer = document.getElementById('colour-customization-container');
            if (colourContainer) {
                const presetRadios = colourContainer.querySelectorAll('input[name="colour-preset"]');
                const customRow = document.getElementById('custom-accent-row');
                const customColor = document.getElementById('custom-accent-color');
                const customHex = document.getElementById('custom-accent-hex');
                presetRadios.forEach((radio) => {
                    radio.addEventListener('change', () => {
                        if (customRow) customRow.classList.toggle('hidden', radio.value !== 'custom');
                        if (radio.value === 'custom' && customColor) customHex.value = customColor.value;
                        savePreviewPrefs({ colourPreset: radio.value });
                        renderPreview();
                    });
                });
                if (customColor) {
                    customColor.addEventListener('input', () => {
                        customHex.value = customColor.value;
                        savePreviewPrefs({ customAccentHex: customColor.value });
                        if (document.querySelector('input[name="colour-preset"]:checked')?.value === 'custom') renderPreview();
                    });
                }
                if (customHex) {
                    customHex.addEventListener('input', () => {
                        const hex = customHex.value.trim();
                        if (/^#[0-9A-Fa-f]{6}$/.test(hex) && customColor) {
                            customColor.value = hex;
                            savePreviewPrefs({ customAccentHex: hex });
                        }
                        if (document.querySelector('input[name="colour-preset"]:checked')?.value === 'custom') renderPreview();
                    });
                }
            }
            } else {
                updateTemplateDescription(selectedTemplate);
            }

            const pdfButton = document.getElementById('generate-pdf-button');
            if (pdfButton && !SubscriptionContext?.pdfEnabled) {
                pdfButton.disabled = true;
                pdfButton.classList.add('opacity-60', 'cursor-not-allowed');
                pdfButton.textContent = 'Upgrade to download PDF';
            }

            // Update Preview button – fetch fresh CV data and re-render
            const updatePreviewBtn = document.getElementById('update-preview-button');
            if (updatePreviewBtn) {
                updatePreviewBtn.addEventListener('click', async () => {
                    const originalText = updatePreviewBtn.textContent;
                    updatePreviewBtn.textContent = 'Updating…';
                    updatePreviewBtn.disabled = true;
                    try {
                        let url = '/api/content-editor/get-cv-data.php';
                        if (previewVariantId) url += '?variant_id=' + encodeURIComponent(previewVariantId);
                        const res = await fetch(url);
                        if (!res.ok) throw new Error('Failed to load CV data');
                        const { cvData: newCvData, profile: newProfile } = await res.json();
                        if (!newCvData || !newProfile) throw new Error('Invalid CV data');
                        cvData = newCvData;
                        profile = newProfile;
                        currentSkillSelection = (cvData.skills || []).map(s => s.id);
                        await renderPreview();
                        if (typeof renderSkillSelection === 'function') renderSkillSelection();
                    } catch (err) {
                        console.error('Update preview error:', err);
                        alert('Could not update preview. Please refresh the page.');
                    } finally {
                        updatePreviewBtn.textContent = originalText;
                        updatePreviewBtn.disabled = false;
                    }
                });
            }

            renderPreview();
        });

        // Skill Selection Functions
        let currentSkillSelection = [];
        let templateSkillConfig = null;

        async function loadSkillSelectionForTemplate(templateId) {
            if (!templateId || !cvData?.skills || cvData.skills.length === 0) {
                document.getElementById('skill-selection-container')?.classList.add('hidden');
                return;
            }

            // Check if skills section is enabled
            const skillsCheckbox = document.getElementById('section-skills');
            if (!skillsCheckbox?.checked) {
                document.getElementById('skill-selection-container')?.classList.add('hidden');
                return;
            }

            try {
                // Get template config to check for skill settings
                // For now, we'll check if template has skill limits by trying to get template metadata
                // In a real implementation, you'd fetch template config from the database
                templateSkillConfig = null; // Will be populated from template metadata/config

                // Load saved skill selection
                const response = await fetch(`/api/get-template-skill-selection.php?template_id=${encodeURIComponent(templateId)}`);
                const data = await response.json();
                
                if (data.success) {
                    currentSkillSelection = data.selected_skill_ids || [];
                } else {
                    currentSkillSelection = [];
                }

                // If no selection exists and template has max skills, select first N skills
                if (currentSkillSelection.length === 0 && templateSkillConfig?.maxSkills) {
                    currentSkillSelection = cvData.skills.slice(0, templateSkillConfig.maxSkills).map(s => s.id);
                }

                renderSkillSelection();
                renderPreview();
            } catch (error) {
                console.error('Error loading skill selection:', error);
                currentSkillSelection = [];
                renderSkillSelection();
                renderPreview();
            }
        }

        function renderSkillSelection() {
            const container = document.getElementById('skill-selection-container');
            const checkboxList = document.getElementById('skills-checkbox-list');
            const gridPreview = document.getElementById('skills-grid-preview');
            const gridContainer = document.getElementById('skills-grid-container');
            const sectionTitle = document.getElementById('skill-section-title');
            const limitBadge = document.getElementById('skill-limit-badge');

            if (!container || !checkboxList || !cvData?.skills) {
                return;
            }

            // Show container if skills section is enabled
            const skillsCheckbox = document.getElementById('section-skills');
            if (skillsCheckbox?.checked && cvData.skills.length > 0) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
                return;
            }

            // Update section title
            if (sectionTitle && templateSkillConfig?.skillSectionTitle) {
                sectionTitle.textContent = templateSkillConfig.skillSectionTitle;
            } else {
                sectionTitle.textContent = 'Select Skills';
            }

            // Update limit badge
            if (limitBadge && templateSkillConfig?.maxSkills) {
                limitBadge.textContent = `(Max ${templateSkillConfig.maxSkills})`;
                limitBadge.classList.remove('hidden');
            } else {
                limitBadge.classList.add('hidden');
            }

            // Group skills by category
            const byCategory = {};
            cvData.skills.forEach(skill => {
                const cat = (skill.category && String(skill.category).trim()) || 'Other';
                if (!byCategory[cat]) byCategory[cat] = [];
                byCategory[cat].push(skill);
            });
            const categoryOrder = Object.keys(byCategory).sort((a, b) =>
                a === 'Other' ? 1 : b === 'Other' ? -1 : a.localeCompare(b)
            );

            // Render checkboxes grouped by category
            checkboxList.innerHTML = '';
            categoryOrder.forEach(cat => {
                const skills = byCategory[cat];
                const block = document.createElement('div');
                block.className = 'space-y-1';
                const header = document.createElement('div');
                header.className = 'text-xs font-semibold text-gray-500 uppercase tracking-wide pb-1.5 mb-1.5 border-b border-gray-200';
                header.textContent = cat;
                block.appendChild(header);
                skills.forEach(skill => {
                    const isSelected = currentSkillSelection.includes(skill.id);
                    const label = document.createElement('label');
                    label.className = 'flex items-center cursor-pointer hover:bg-gray-50 px-2 py-1 rounded text-sm';
                    label.innerHTML = `
                        <input type="checkbox" 
                               class="mr-2 skill-checkbox" 
                               data-skill-id="${skill.id}"
                               ${isSelected ? 'checked' : ''}
                               ${templateSkillConfig?.maxSkills && currentSkillSelection.length >= templateSkillConfig.maxSkills && !isSelected ? 'disabled' : ''}>
                        <span class="text-gray-700">${escapeHtml(skill.name)}${skill.level ? ` <span class="text-gray-400">(${escapeHtml(skill.level)})</span>` : ''}</span>
                    `;
                    block.appendChild(label);
                });
                checkboxList.appendChild(block);
            });

            // Add event listeners
            checkboxList.querySelectorAll('.skill-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', handleSkillSelectionChange);
            });

            // Render grid preview if layout is grid/columns
            if (templateSkillConfig?.skillLayout === 'grid' || templateSkillConfig?.skillLayout === 'columns') {
                renderGridPreview();
                gridPreview.classList.remove('hidden');
            } else {
                gridPreview.classList.add('hidden');
            }
        }

        function handleSkillSelectionChange(event) {
            const skillId = event.target.dataset.skillId;
            const isChecked = event.target.checked;

            if (isChecked) {
                // Check max skills limit
                if (templateSkillConfig?.maxSkills && currentSkillSelection.length >= templateSkillConfig.maxSkills) {
                    event.target.checked = false;
                    alert(`Maximum ${templateSkillConfig.maxSkills} skills allowed for this template.`);
                    return;
                }
                if (!currentSkillSelection.includes(skillId)) {
                    currentSkillSelection.push(skillId);
                }
            } else {
                currentSkillSelection = currentSkillSelection.filter(id => id !== skillId);
            }

            // Update disabled state of other checkboxes
            updateCheckboxStates();
            
            // Update grid preview
            if (templateSkillConfig?.skillLayout === 'grid' || templateSkillConfig?.skillLayout === 'columns') {
                renderGridPreview();
            }

            // Save selection
            saveSkillSelection();
        }

        function updateCheckboxStates() {
            const checkboxes = document.querySelectorAll('.skill-checkbox');
            const maxSkills = templateSkillConfig?.maxSkills;
            
            checkboxes.forEach(checkbox => {
                const isChecked = checkbox.checked;
                if (maxSkills && !isChecked && currentSkillSelection.length >= maxSkills) {
                    checkbox.disabled = true;
                } else {
                    checkbox.disabled = false;
                }
            });
        }

        function renderGridPreview() {
            const gridContainer = document.getElementById('skills-grid-container');
            if (!gridContainer || !templateSkillConfig) return;

            const selectedSkills = cvData.skills.filter(s => currentSkillSelection.includes(s.id));
            const columns = templateSkillConfig.skillColumns || 4;
            const rows = templateSkillConfig.skillRows || 3;
            const maxItems = columns * rows;

            gridContainer.innerHTML = '';
            gridContainer.style.display = 'grid';
            gridContainer.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
            gridContainer.style.gap = '8px';

            const skillsToShow = selectedSkills.slice(0, maxItems);
            skillsToShow.forEach(skill => {
                const skillBox = document.createElement('div');
                skillBox.className = 'bg-white border border-gray-300 rounded p-2 text-xs text-center';
                skillBox.textContent = skill.name;
                gridContainer.appendChild(skillBox);
            });

            // Show placeholder boxes for remaining slots
            const remainingSlots = maxItems - skillsToShow.length;
            for (let i = 0; i < remainingSlots; i++) {
                const placeholder = document.createElement('div');
                placeholder.className = 'bg-gray-100 border border-gray-200 rounded p-2 text-xs text-center text-gray-400';
                placeholder.textContent = '—';
                gridContainer.appendChild(placeholder);
            }
        }

        async function saveSkillSelection() {
            const templateId = getSelectedTemplate();
            if (!templateId) return;

            try {
                const formData = new FormData();
                formData.append('template_id', templateId);
                formData.append('selected_skill_ids', JSON.stringify(currentSkillSelection));
                formData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo csrfToken(); ?>');

                const response = await fetch('/api/save-template-skill-selection.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (!data.success) {
                    console.error('Failed to save skill selection:', data.error);
                }
            } catch (error) {
                console.error('Error saving skill selection:', error);
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Watch for skills checkbox changes
        document.getElementById('section-skills')?.addEventListener('change', function() {
            if (this.checked) {
                loadSkillSelectionForTemplate(getSelectedTemplate());
            } else {
                document.getElementById('skill-selection-container')?.classList.add('hidden');
            }
        });

        // Watch for template changes
        document.getElementById('template-select')?.addEventListener('change', function() {
            loadSkillSelectionForTemplate(getSelectedTemplate());
        });

        // Initial load
        if (document.getElementById('section-skills')?.checked) {
            setTimeout(() => loadSkillSelectionForTemplate(selectedTemplate), 100);
        }

        window.addEventListener('load', renderPreview);
        window.generatePDF = generatePDF;
    </script>

    <?php partial('footer'); ?>
</body>
</html>