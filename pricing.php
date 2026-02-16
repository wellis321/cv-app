<?php
/**
 * Public pricing page – individual plans and organisation CTA.
 * No auth required. Reachable at /pricing (routed in index.php).
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Free CV Builder UK | Pricing & Plans | Simple CV Builder';
$metaDescription = 'Free CV builder UK and job tracker. Start free or choose 1 week, 1 month, or 3 months—all with a 7-day free trial. Cancel anytime.';
$canonicalUrl = APP_URL . '/pricing';

?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle,
        'metaDescription' => $metaDescription,
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content" role="main">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Free CV Builder UK – Pricing & Plans</h1>
                <p class="mt-2 text-lg text-gray-600">
                    Free CV builder UK plans to fit your job search. Start free, or choose 1 week, 1 month, or 3 months—all with a 7-day free trial.
                </p>
            </div>
            <?php partial('home-pricing', ['pricingUseRegisterModal' => false]); ?>
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
</body>
</html>
