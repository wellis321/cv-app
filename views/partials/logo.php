<?php
/**
 * Logo partial - WebP with fallback, responsive sizes
 * $logoClass: Tailwind classes for size (e.g. h-10, h-12, h-6)
 * $logoAlt: alt text (default: Simple CV Builder)
 */
$logoClass = $logoClass ?? 'h-10 md:h-10 lg:h-12 w-auto flex-shrink-0';
$logoAlt = $logoAlt ?? 'Simple CV Builder';
?>
<picture>
    <source srcset="/static/images/logo/black-logo-96.webp" type="image/webp">
    <img src="/static/images/logo/black-logo-96.jpg" alt="<?php echo e($logoAlt); ?>" class="<?php echo e($logoClass); ?>" width="96" height="96">
</picture>
