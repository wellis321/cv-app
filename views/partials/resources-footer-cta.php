<section class="bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid gap-10 lg:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-blue-50 via-white to-white p-10 shadow-sm">
                <h2 class="text-2xl font-bold text-gray-900">Want deeper support?</h2>
                <p class="mt-4 text-gray-600">
                    Our CV Builder Pro plans include priority email support, premium templates, and mindset guides to help you perform throughout your search.
                    Upgrade whenever you need extra momentum.
                </p>
                <a href="/subscription.php" class="mt-6 inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
                    Compare plans
                </a>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-10 shadow-sm">
                <h2 class="text-2xl font-bold text-gray-900">Stay in the loop</h2>
                <p class="mt-4 text-gray-600">
                    Get updates on new features, CV tips, and occasional promotions.
                    No spam—unsubscribe anytime.
                </p>
                <form id="newsletter-signup-form" class="mt-6 flex flex-col gap-3 sm:flex-row" method="post" action="/api/newsletter-signup.php">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                    <input type="hidden" name="source" value="blog">
                    <input type="email" name="email" placeholder="you@example.com" required class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Email address">
                    <button type="submit" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-gray-700 whitespace-nowrap">
                        Join mailing list
                    </button>
                </form>
                <p id="newsletter-message" class="mt-3 text-sm hidden" role="status"></p>
                <script>
                (function() {
                    var form = document.getElementById('newsletter-signup-form');
                    var messageEl = document.getElementById('newsletter-message');
                    if (!form || !messageEl) return;
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        var btn = form.querySelector('button[type="submit"]');
                        var originalText = btn.textContent;
                        btn.disabled = true;
                        btn.textContent = 'Joining…';
                        messageEl.classList.add('hidden');
                        fetch(form.action, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams(new FormData(form))
                        })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            messageEl.textContent = data.message || data.error || 'Done.';
                            messageEl.classList.remove('hidden');
                            messageEl.classList.toggle('text-green-600', data.success);
                            messageEl.classList.toggle('text-red-600', !data.success);
                            if (data.success) form.reset();
                        })
                        .catch(function() {
                            messageEl.textContent = 'Something went wrong. Please try again.';
                            messageEl.classList.remove('hidden');
                            messageEl.classList.add('text-red-600');
                        })
                        .finally(function() {
                            btn.disabled = false;
                            btn.textContent = originalText;
                        });
                    });
                })();
                </script>
            </div>
        </div>
    </div>
</section>
