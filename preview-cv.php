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
if ($variantId) {
    $cvUrl .= '?variant_id=' . rawurlencode($variantId);
}
$profileShowPhotoCv = $profile['show_photo'] ?? 1;
$profileShowPhotoPdf = $profile['show_photo_pdf'] ?? 1;
$profileShowQrCode = $profile['show_qr_code'] ?? ($profileShowPhotoCv ? 0 : 1);
$profileShowQrCodePdfDefault = $profileShowPhotoPdf ? $profileShowQrCode : 1;

// These settings are edited in the content editor (Appearance / Visibility sections).
// This page just displays the resolved, DB-backed values - variant wins if set, else profile -
// using the same shared precedence helpers those sections read/write.
$previewCvVariant = $cvVariant ?? null;
$resolvedPdfPrefs = getPdfPreferencesForCv($profile, $previewCvVariant);
$resolvedTemplateId = $profile['preferred_template_id'] ?? 'minimal';
if ($previewCvVariant && !empty($previewCvVariant['pdf_preferences'])) {
    $decodedVariantPrefs = json_decode($previewCvVariant['pdf_preferences'], true);
    if (is_array($decodedVariantPrefs) && !empty($decodedVariantPrefs['preferred_template_id'])) {
        $resolvedTemplateId = $decodedVariantPrefs['preferred_template_id'];
    }
}
$rawSectionsOnlineForPreview = null;
if ($previewCvVariant && !empty($previewCvVariant['pdf_preferences'])) {
    $decodedVariantSectionsOnline = json_decode($previewCvVariant['pdf_preferences'], true);
    $rawSectionsOnlineForPreview = is_array($decodedVariantSectionsOnline) ? ($decodedVariantSectionsOnline['sections_online'] ?? null) : null;
}
if ($rawSectionsOnlineForPreview === null && !empty($profile['sections_online'])) {
    $decodedProfileSectionsOnline = json_decode($profile['sections_online'], true);
    $rawSectionsOnlineForPreview = is_array($decodedProfileSectionsOnline) ? $decodedProfileSectionsOnline : null;
}
$previewSectionKeys = ['profile', 'summary', 'work', 'education', 'areasOfExpertise', 'skills', 'projects', 'certifications', 'memberships', 'interests', 'qualificationEquivalence'];
$resolvedSectionsOnline = [];
foreach ($previewSectionKeys as $previewSectionKey) {
    $resolvedSectionsOnline[$previewSectionKey] = isset($rawSectionsOnlineForPreview[$previewSectionKey]) ? (bool) $rawSectionsOnlineForPreview[$previewSectionKey] : true;
}
$resolvedShowResponsibilitiesOnline = getShowResponsibilitiesOnlineForCv($profile, $previewCvVariant);
$resolvedIncludePhoto = $resolvedPdfPrefs['include_photo'];
$resolvedIncludeQr = $resolvedPdfPrefs['include_qr'];
$resolvedShowResponsibilitiesInPdf = $resolvedPdfPrefs['show_responsibilities_in_pdf'];

// Section order and column placement (set via cv.php's Edit Mode drag-and-drop) - same
// profile-level settings regardless of variant, matching cv.php's own behaviour, so the
// preview/PDF layout matches what the owner arranged on their online CV.
$resolvedSectionOrder = null;
if (!empty($profile['section_order'])) {
    $decodedSectionOrder = json_decode($profile['section_order'], true);
    if (is_array($decodedSectionOrder)) {
        $resolvedSectionOrder = $decodedSectionOrder;
    }
}
$resolvedCvPageColumns = [];
if (!empty($profile['cv_page_columns'])) {
    $decodedCvPageColumns = json_decode($profile['cv_page_columns'], true);
    if (is_array($decodedCvPageColumns)) {
        $resolvedCvPageColumns = $decodedCvPageColumns;
    }
}
$resolvedColourPreset = $resolvedPdfPrefs['colour_preset'] ?: 'default';
$resolvedCustomAccentHex = $resolvedPdfPrefs['custom_accent_hex'] ?: '#2563eb';

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
        'variantId' => $variantId ?? null,
    ]); ?>

    <div class="max-w-6xl mx-auto px-4 py-5">
        <?php if (!empty($cvData['variant']['variant_name'])): ?>
        <p class="text-sm text-gray-500 mb-1">Viewing: <?php echo e($cvData['variant']['variant_name']); ?></p>
        <?php endif; ?>
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Preview & Generate PDF</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Section Selection Panel: sticky on wrapper (no overflow); card scrolls when taller than viewport -->
            <div id="generate-pdf" class="lg:col-span-1 lg:sticky lg:top-24 lg:self-start scroll-mt-4">
                <div class="bg-white shadow rounded-lg p-4 lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto">
                    <!-- Primary action: always visible -->
                    <button id="generate-pdf-button" onclick="generatePDF()" class="w-full bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-sm mb-2">
                        Generate PDF
                    </button>
                    <button type="button" id="update-preview-button" class="w-full bg-gray-100 text-gray-800 px-6 py-2 rounded-md border border-gray-300 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium mb-3">
                        Update Preview
                    </button>
                    <?php if (!planPdfEnabled($subscriptionContext)): ?>
                        <p class="mb-3 text-sm text-gray-500">
                            PDF downloads are available on Pro plans.
                            <a href="/subscription.php" class="text-blue-600 hover:text-blue-800 underline">Upgrade now</a>.
                        </p>
                    <?php endif; ?>

                    <div class="border border-gray-300 bg-gray-50/70 p-2">
                        <nav class="space-y-1.5">
                            <a href="/content-editor.php#appearance<?php echo $variantId ? '&variant_id=' . rawurlencode($variantId) : ''; ?>"
                               class="section-nav-item flex items-center gap-2 px-3 py-2.5 border border-l-4 border-gray-300 border-l-gray-300 bg-white shadow-sm text-sm font-medium text-gray-700 transition-all hover:border-gray-400 hover:bg-gray-50 hover:shadow">
                                <svg class="w-5 h-5 mr-2 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-4"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17h.01"></path>
                                </svg>
                                <span class="truncate">Appearance</span>
                            </a>
                            <a href="/content-editor.php#visibility<?php echo $variantId ? '&variant_id=' . rawurlencode($variantId) : ''; ?>"
                               class="section-nav-item flex items-center gap-2 px-3 py-2.5 border border-l-4 border-gray-300 border-l-gray-300 bg-white shadow-sm text-sm font-medium text-gray-700 transition-all hover:border-gray-400 hover:bg-gray-50 hover:shadow">
                                <svg class="w-5 h-5 mr-2 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span class="truncate">Visibility</span>
                            </a>
                        </nav>
                    </div>

                </div>
            </div>

            <!-- Preview Panel -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg p-4">
                    <div id="cv-preview" class="border border-gray-200 bg-white min-h-[400px]">
                        <!-- Preview will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

        <script type="module">
        import { DEFAULT_TEMPLATE_ID, getTemplateMeta, getPreviewRenderer } from '/templates/index.js?v=<?php echo time(); ?>';

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
        const profileSectionsOnline = <?php
            $raw = $profile['sections_online'] ?? null;
            echo $raw ? json_encode(is_string($raw) ? json_decode($raw, true) : $raw, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) : 'null';
        ?>;
        let cvData = <?php echo json_encode($cvDataDecoded, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        let profile = <?php echo json_encode($profileDecoded, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const dateFormat = <?php echo json_encode($dateFormat, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const cvUrl = <?php echo json_encode($cvUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        const resolvedTemplateId = <?php echo json_encode($resolvedTemplateId, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        let selectedTemplate = resolvedTemplateId || SubscriptionContext?.defaultTemplateId || DEFAULT_TEMPLATE_ID;

        // "Sections for PDF" / "Sections for Online CV" panels, plus PDF Style (template select),
        // Photo & QR Code, PDF Footer, and Customise Colours were all removed from this sidebar
        // (they were disabled read-only mirrors of Appearance/Visibility, or in PDF Footer's case
        // not a setting at all). getSections()/getSectionsOnline()/getCustomization() and the PDF/
        // preview builders below read these resolved values directly instead of removed elements.
        const resolvedPdfSections = <?php echo json_encode($resolvedPdfPrefs['sections'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const resolvedOnlineSections = <?php echo json_encode($resolvedSectionsOnline, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const resolvedShowResponsibilitiesInPdf = <?php echo json_encode($resolvedShowResponsibilitiesInPdf, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const resolvedShowResponsibilitiesOnline = <?php echo json_encode($resolvedShowResponsibilitiesOnline, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const resolvedIncludePhoto = <?php echo json_encode($resolvedIncludePhoto, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const resolvedIncludeQr = <?php echo json_encode($resolvedIncludeQr, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const resolvedColourPreset = <?php echo json_encode($resolvedColourPreset, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const resolvedCustomAccentHex = <?php echo json_encode($resolvedCustomAccentHex, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const resolvedSectionOrder = <?php echo json_encode($resolvedSectionOrder, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const resolvedCvPageColumns = <?php echo json_encode($resolvedCvPageColumns, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

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
                profile: resolvedPdfSections.profile ?? true,
                summary: resolvedPdfSections.summary ?? true,
                work: resolvedPdfSections.work ?? true,
                education: resolvedPdfSections.education ?? true,
                areasOfExpertise: resolvedPdfSections.areasOfExpertise ?? true,
                skills: resolvedPdfSections.skills ?? true,
                projects: resolvedPdfSections.projects ?? true,
                certifications: resolvedPdfSections.certifications ?? true,
                memberships: resolvedPdfSections.memberships ?? true,
                interests: resolvedPdfSections.interests ?? true,
                qualificationEquivalence: resolvedPdfSections.qualificationEquivalence ?? true
            };
        }

        function getSectionsOnline() {
            return {
                profile: resolvedOnlineSections.profile ?? true,
                summary: resolvedOnlineSections.summary ?? true,
                work: resolvedOnlineSections.work ?? true,
                education: resolvedOnlineSections.education ?? true,
                areasOfExpertise: resolvedOnlineSections.areasOfExpertise ?? true,
                skills: resolvedOnlineSections.skills ?? true,
                projects: resolvedOnlineSections.projects ?? true,
                certifications: resolvedOnlineSections.certifications ?? true,
                memberships: resolvedOnlineSections.memberships ?? true,
                interests: resolvedOnlineSections.interests ?? true,
                qualificationEquivalence: resolvedOnlineSections.qualificationEquivalence ?? true
            };
        }

        function getSelectedTemplate() {
            const candidate = selectedTemplate || DEFAULT_TEMPLATE_ID;
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
            const preset = resolvedColourPreset || 'default';
            if (preset === 'default') return {};
            if (preset === 'custom') {
                const hex = resolvedCustomAccentHex || '#2563eb';
                const valid = /^#[0-9A-Fa-f]{6}$/.test(hex) ? hex : '#2563eb';
                return { colors: { accent: valid, divider: valid, link: valid, header: valid } };
            }
            const colors = COLOUR_PRESETS[preset];
            return colors ? { colors } : {};
        }

        function buildPdfFilename(profile, cvData) {
            const fullName = (profile.full_name || '').trim();
            const parts = fullName ? fullName.split(/\s+/) : [];
            const firstName = parts[0] ? parts[0].replace(/[^a-zA-Z0-9\-]/g, '') : '';
            const lastName = parts.length > 1 ? parts[parts.length - 1].replace(/[^a-zA-Z0-9\-]/g, '') : '';
            const safeFirst = firstName || 'First';
            const safeLast = lastName || 'Last';
            const variantName = cvData?.variant?.variant_name;
            if (variantName && String(variantName).trim()) {
                const safeVariant = String(variantName).trim().replace(/\s+/g, '-').replace(/[^a-zA-Z0-9\-]/g, '');
                return `${safeVariant}-${safeFirst}-${safeLast}-CV.pdf`;
            }
            return `${safeFirst}-${safeLast}-CV.pdf`;
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
                const sections = {
                    profile: sectionsObj.profile,
                    professionalSummary: sectionsObj.summary,
                    summary: sectionsObj.summary,
                    workExperience: sectionsObj.work,
                    work: sectionsObj.work,
                    education: sectionsObj.education,
                    areasOfExpertise: sectionsObj.areasOfExpertise,
                    skills: sectionsObj.skills,
                    projects: sectionsObj.projects,
                    certifications: sectionsObj.certifications,
                    qualificationEquivalence: sectionsObj.qualificationEquivalence,
                    memberships: sectionsObj.memberships,
                    interests: sectionsObj.interests
                };

                // Get include photo and QR code settings
                const includePhoto = resolvedIncludePhoto ?? true;
                const includeQr = resolvedIncludeQr ?? true;

                // Get selected template
                const selectedTemplate = getSelectedTemplate();

                // Prepare config (include colour customization for Pro users, free plan branding)
                const customization = getCustomization();
                const showResponsibilitiesInPdf = resolvedShowResponsibilitiesInPdf ?? true;
                const config = {
                    sections: sections,
                    includePhoto: includePhoto,
                    includeQRCode: includeQr,
                    show_responsibilities_in_pdf: showResponsibilitiesInPdf,
                    showFreePlanBranding: !SubscriptionContext?.isPaid,
                    siteUrl: siteUrl || window.location.origin,
                    sectionOrder: resolvedSectionOrder,
                    cvPageColumns: resolvedCvPageColumns
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
                const filename = buildPdfFilename(profile, cvData);

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
                const includePhoto = resolvedIncludePhoto ?? true;
                const includeQr = resolvedIncludeQr ?? true;
                const includeResponsibilitiesInPdf = resolvedShowResponsibilitiesInPdf ?? true;

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
                    includeResponsibilitiesInPdf,
                    cvUrl,
                    template: templateMeta,
                    sectionOrder: resolvedSectionOrder,
                    cvPageColumns: resolvedCvPageColumns
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

        document.addEventListener('DOMContentLoaded', async () => {

            const pdfButton = document.getElementById('generate-pdf-button');
            if (pdfButton && !SubscriptionContext?.pdfEnabled) {
                pdfButton.disabled = true;
                pdfButton.classList.add('opacity-60', 'cursor-not-allowed');
                pdfButton.textContent = 'Upgrade to download PDF';
            }

            // Update Preview button – full reload rather than a partial JS refetch, since
            // content, Appearance, and Visibility settings are all resolved server-side once
            // at page load. A partial refetch only ever picked up content changes (new work
            // experience, edited skills) and silently missed colour/template/section changes
            // made elsewhere - a reload guarantees everything shown here is current.
            const updatePreviewBtn = document.getElementById('update-preview-button');
            if (updatePreviewBtn) {
                updatePreviewBtn.addEventListener('click', () => {
                    updatePreviewBtn.disabled = true;
                    updatePreviewBtn.textContent = 'Updating…';
                    window.location.reload();
                });
            }

            renderPreview();
        });

        // Skill Selection Functions
        // Which skills to include in the PDF is now edited on the Visibility page ("Select
        // Skills"), not here. This page still needs to load the saved selection so the live
        // preview and PDF export filter skills correctly - just no UI to change it.
        let currentSkillSelection = [];

        async function loadSkillSelectionForTemplate(templateId) {
            if (!templateId || !cvData?.skills || cvData.skills.length === 0) {
                currentSkillSelection = [];
                return;
            }
            try {
                const response = await fetch(`/api/get-template-skill-selection.php?template_id=${encodeURIComponent(templateId)}`);
                const data = await response.json();
                currentSkillSelection = data.success ? (data.selected_skill_ids || []) : [];
                renderPreview();
            } catch (error) {
                console.error('Error loading skill selection:', error);
                currentSkillSelection = [];
                renderPreview();
            }
        }

        // Initial load
        if (resolvedPdfSections.skills) {
            setTimeout(() => loadSkillSelectionForTemplate(selectedTemplate), 100);
        }

        window.addEventListener('load', renderPreview);
        window.generatePDF = generatePDF;
    </script>

    <?php partial('footer'); ?>
</body>
</html>