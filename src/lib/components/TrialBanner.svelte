<script lang="ts">
	import { currentSubscription } from '$lib/stores/subscriptionStore';
	import { browser } from '$app/environment';
	import { goto } from '$app/navigation';

	let daysRemaining = $derived.by(() => {
		const sub = $currentSubscription;
		if (!sub.isTrial || !sub.trialEndsAt) return 0;

		const end = new Date(sub.trialEndsAt);
		const now = new Date();
		const diff = end.getTime() - now.getTime();
		const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
		return days > 0 ? days : 0;
	});

	let showBanner = $state(true);

	function dismissBanner() {
		showBanner = false;
	}

	function goToPayment() {
		if (browser) {
			goto('/subscription');
		}
	}
</script>

{#if showBanner && $currentSubscription.isTrial && daysRemaining > 0}
	<div
		class="border-b border-amber-200 bg-gradient-to-r from-amber-50 to-blue-50 py-3 shadow-sm"
		role="banner"
	>
		<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
			<div class="flex items-center justify-between">
				<div class="flex items-center gap-3">
					<div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100">
						<svg class="h-5 w-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
							<path
								fill-rule="evenodd"
								d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
								clip-rule="evenodd"
							/>
						</svg>
					</div>
					<div>
						<p class="text-sm font-medium text-gray-900">
							Free trial: {daysRemaining}
							{daysRemaining === 1 ? 'day' : 'days'} remaining
						</p>
						<p class="text-xs text-gray-600">
							{@html daysRemaining > 3
								? 'Enjoy full access to all features during your free trial'
								: daysRemaining > 1
									? `Only ${daysRemaining} days left! Upgrade now to keep your data forever`
									: 'Your trial ends tomorrow! Upgrade now to keep access to all your CV data'}
						</p>
					</div>
				</div>
				<div class="flex items-center gap-3">
					<button
						type="button"
						onclick={goToPayment}
						class="inline-flex items-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
					>
						Upgrade for £9.99/year
					</button>
					<button
						type="button"
						onclick={dismissBanner}
						class="text-gray-400 hover:text-gray-500 focus:outline-none"
					>
						<span class="sr-only">Dismiss</span>
						<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
							<path
								fill-rule="evenodd"
								d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
								clip-rule="evenodd"
							/>
						</svg>
					</button>
				</div>
			</div>
		</div>
	</div>
{/if}
