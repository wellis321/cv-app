<?php
/**
 * Inline CTA for blog/resource article pages.
 * Use midway through long articles to keep readers engaged.
 *
 * Optional: pass $heading and $subtext to customize the message.
 * Default: "Put this into practice" / "Build or refresh your CV with our free tool."
 */
$heading = $heading ?? 'Put this into practice';
$subtext = $subtext ?? 'Build or refresh your CV with our free tool.';
?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="rounded-2xl border-2 border-emerald-300 bg-white p-6 flex flex-col sm:flex-row gap-6 items-center justify-between shadow-md">
        <p class="text-slate-800 text-base"><strong><?php echo e($heading); ?></strong> <?php echo e($subtext); ?></p>
        <div class="flex flex-col sm:flex-row gap-3 shrink-0">
            <a href="/#pricing" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-6 py-3 text-base font-bold text-white shadow-lg hover:bg-slate-800 whitespace-nowrap">Create your CV free →</a>
            <a href="/pricing" class="inline-flex items-center justify-center rounded-lg border-2 border-slate-900 px-6 py-3 text-base font-semibold text-slate-900 hover:bg-slate-50 whitespace-nowrap">Compare plans</a>
        </div>
    </div>
</div>
