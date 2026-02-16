<?php
/**
 * Reusable image lightbox for feature pages
 * Include before </body>. Use data-image-lightbox="/path/to/image" on clickable elements.
 */
?>
<!-- Image lightbox -->
<div id="image-lightbox" class="fixed inset-0 z-[60] hidden overflow-y-auto" role="dialog" aria-modal="true" aria-label="Image preview">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/70 transition-opacity" data-close-image-lightbox aria-hidden="true"></div>
        <div class="relative max-w-4xl w-full flex items-center justify-center">
            <button type="button" class="absolute right-2 top-2 z-10 rounded-full bg-white/90 p-2 text-gray-600 hover:bg-white hover:text-gray-900 transition-colors" data-close-image-lightbox aria-label="Close">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <img id="image-lightbox-img" src="" alt="" class="max-h-[90vh] w-auto rounded-lg shadow-2xl object-contain">
        </div>
    </div>
</div>
<script>
(function() {
    var lightbox = document.getElementById('image-lightbox');
    var lightboxImg = document.getElementById('image-lightbox-img');
    if (!lightbox || !lightboxImg) return;
    function openLightbox(src, alt) {
        lightboxImg.src = src;
        lightboxImg.alt = alt || 'Image preview';
        lightbox.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        var closeBtn = lightbox.querySelector('button[data-close-image-lightbox]');
        if (closeBtn) setTimeout(function() { closeBtn.focus(); }, 50);
    }
    function closeLightbox() {
        lightbox.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    document.querySelectorAll('[data-image-lightbox]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            openLightbox(this.getAttribute('data-image-lightbox'), this.getAttribute('aria-label') || 'Image preview');
        });
    });
    document.querySelectorAll('[data-close-image-lightbox]').forEach(function(btn) {
        btn.addEventListener('click', closeLightbox);
    });
    lightbox.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
    });
})();
</script>
