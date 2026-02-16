<?php
/**
 * Article CTA - shown at end of resource articles
 * Links to create a free CV / register
 */
$ctaTitle = $ctaTitle ?? 'Create your free CV with Simple CV Builder';
$ctaText = $ctaText ?? 'Build your professional CV, track job applications, and use AI to tailor your content—all in one place.';
$ctaButtonText = $ctaButtonText ?? 'Create free CV';
$ctaButtonUrl = $ctaButtonUrl ?? '/index.php#register';
?>
<section class="mt-16 rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-8 shadow-lg shadow-slate-900/5">
    <div class="mx-auto max-w-2xl text-center">
        <h2 class="text-2xl font-semibold text-slate-900"><?php echo e($ctaTitle); ?></h2>
        <p class="mt-4 text-base text-slate-600"><?php echo e($ctaText); ?></p>
        <a href="<?php echo e($ctaButtonUrl); ?>" class="mt-6 inline-flex items-center justify-center rounded-lg bg-slate-900 px-6 py-3 text-base font-semibold text-white shadow hover:bg-slate-800 transition-colors">
            <?php echo e($ctaButtonText); ?> →
        </a>
    </div>
</section>
