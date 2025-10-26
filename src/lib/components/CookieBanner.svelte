<script lang="ts">
	import { browser } from '$app/environment';
	import { onMount } from 'svelte';

	let showBanner = $state(false);

	onMount(() => {
		if (browser) {
			// Check if user has already accepted/rejected cookies
			const consent = localStorage.getItem('cookie_consent');
			if (!consent) {
				showBanner = true;
			}
		}
	});

	function acceptCookies() {
		if (browser) {
			localStorage.setItem('cookie_consent', 'accepted');
			showBanner = false;
		}
	}

	function rejectCookies() {
		if (browser) {
			localStorage.setItem('cookie_consent', 'rejected');
			showBanner = false;
			// Clear analytics session if user rejects
			localStorage.removeItem('analytics_session_id');
		}
	}
</script>

{#if showBanner}
	<div
		class="fixed inset-x-0 bottom-0 z-50 bg-gray-900 px-4 py-4 shadow-lg sm:px-6 lg:px-8"
		role="dialog"
		aria-labelledby="cookie-consent-title"
	>
		<div class="mx-auto max-w-7xl">
			<div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
				<div class="flex-1">
					<h2 id="cookie-consent-title" class="text-lg font-semibold text-white">
						Cookie Preferences
					</h2>
					<p class="mt-2 text-sm text-gray-300">
						We use cookies to enhance your experience, analyze site usage, and assist with our
						marketing efforts. By clicking "Accept", you consent to our use of cookies.
						<a href="/privacy" class="font-medium text-blue-400 underline hover:text-blue-300">
							Learn more
						</a>
					</p>
				</div>
				<div class="flex shrink-0 gap-3">
					<button
						type="button"
						onclick={rejectCookies}
						class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-900 focus:outline-none"
					>
						Reject All
					</button>
					<button
						type="button"
						onclick={acceptCookies}
						class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900 focus:outline-none"
					>
						Accept All
					</button>
				</div>
			</div>
		</div>
	</div>
{/if}
