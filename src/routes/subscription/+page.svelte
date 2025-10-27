<script lang="ts">
	import { onMount } from 'svelte';
	import {
		subscriptionPlans,
		currentSubscription,
		loadUserSubscription
	} from '$lib/stores/subscriptionStore';
	import { goto } from '$app/navigation';
	import { session } from '$lib/stores/authStore';

	let currentPlanName = $state('Free');
	let features = $state(['All CV sections', 'PDF Export', 'Online CV Sharing', 'All Templates']);
	let trialDaysRemaining = $derived.by(() => {
		if (!$currentSubscription.trialEndsAt) return null;
		const end = new Date($currentSubscription.trialEndsAt);
		const now = new Date();
		const diff = end.getTime() - now.getTime();
		const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
		return days > 0 ? days : 0;
	});
	let isInTrial = $derived(
		$currentSubscription.isTrial && trialDaysRemaining && trialDaysRemaining > 0
	);

	onMount(async () => {
		await loadUserSubscription();
		if ($currentSubscription.plan) {
			currentPlanName = $currentSubscription.plan.name;
		}
	});

	function goToHome() {
		goto('/');
	}

	function goToDashboard() {
		goto('/dashboard');
	}

	async function handlePayment() {
		if (!$session) {
			goto('/');
			return;
		}

		// Create payment intent
		try {
			const response = await fetch('/api/stripe/create-payment-intent', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				}
			});

			const { clientSecret, paymentIntentId } = await response.json();

			// Load Stripe
			const stripe = (await import('@stripe/stripe-js')).loadStripe(
				import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY
			);

			const stripeInstance = await stripe;

			if (stripeInstance && clientSecret) {
				const { error } = await stripeInstance.confirmPayment({
					clientSecret,
					confirmParams: {
						return_url: `${window.location.origin}/subscription?payment=success`
					}
				});

				if (error) {
					console.error('Payment error:', error);
					alert('Payment failed. Please try again.');
				}
			}
		} catch (err) {
			console.error('Error processing payment:', err);
			alert('Failed to process payment. Please try again.');
		}
	}
</script>

<svelte:head>
	<title>Subscription | Simple CV Builder</title>
</svelte:head>

<div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
	<!-- Hero Section -->
	<div class="text-center">
		<div class="mx-auto max-w-3xl">
			{#if isInTrial}
				<h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
					🆓 Free Trial Active
				</h1>
				<p class="mx-auto mt-5 max-w-xl text-xl text-gray-500">
					You have <strong>{trialDaysRemaining} {trialDaysRemaining === 1 ? 'day' : 'days'}</strong>
					left in your free trial
				</p>
			{:else if $currentSubscription.hasPaid}
				<h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
					✅ Full Access
				</h1>
				<p class="mx-auto mt-5 max-w-xl text-xl text-gray-500">
					You have full access to all features. Thank you for your support!
				</p>
			{:else}
				<h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
					📝 Upgrade Now
				</h1>
				<p class="mx-auto mt-5 max-w-xl text-xl text-gray-500">
					Your trial has ended. Upgrade to keep access to all your CV data and features.
				</p>
			{/if}
		</div>
	</div>

	<!-- Main Card -->
	<div class="mt-12 overflow-hidden rounded-lg bg-white shadow-lg">
		<div class="px-6 py-10 sm:p-12">
			<div class="text-center">
				<!-- Current Access Status -->
				<div class="mb-8">
					<div
						class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium {isInTrial
							? 'bg-amber-50 text-amber-800'
							: $currentSubscription.hasPaid
								? 'bg-green-50 text-green-800'
								: 'bg-red-50 text-red-800'}"
					>
						{#if isInTrial}
							<svg
								class="mr-2 -ml-1 h-5 w-5 text-amber-500"
								fill="currentColor"
								viewBox="0 0 20 20"
							>
								<path
									fill-rule="evenodd"
									d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
									clip-rule="evenodd"
								/>
							</svg>
							Trial: {trialDaysRemaining}
							{trialDaysRemaining === 1 ? 'day' : 'days'} remaining
						{:else if $currentSubscription.hasPaid}
							<svg
								class="mr-2 -ml-1 h-5 w-5 text-green-500"
								fill="currentColor"
								viewBox="0 0 20 20"
							>
								<path
									fill-rule="evenodd"
									d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
									clip-rule="evenodd"
								/>
							</svg>
							Full Access: All Features Enabled
						{:else}
							<svg class="mr-2 -ml-1 h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
								<path
									fill-rule="evenodd"
									d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
									clip-rule="evenodd"
								/>
							</svg>
							Trial Expired: Upgrade to Keep Access
						{/if}
					</div>
				</div>

				{#if !$currentSubscription.hasPaid}
					<!-- Pricing -->
					<div class="mb-8">
						<div
							class="inline-block rounded-2xl border-4 border-blue-600 bg-gradient-to-br from-blue-50 to-blue-100 p-8"
						>
							<div class="text-center">
								<div class="text-5xl font-bold text-blue-900">£9.99</div>
								<div class="mt-2 text-sm font-medium text-blue-700">per year</div>
								<div class="mt-4 text-sm text-gray-600">
									One-time payment • Full access forever • No recurring fees
								</div>
							</div>
						</div>
					</div>

					<!-- CTA Button -->
					<div class="mb-8">
						<button
							type="button"
							onclick={handlePayment}
							class="inline-flex items-center rounded-md bg-blue-600 px-8 py-4 text-lg font-semibold text-white shadow-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
						>
							<svg
								class="mr-2 h-6 w-6"
								fill="none"
								stroke="currentColor"
								viewBox="0 0 24 24"
								xmlns="http://www.w3.org/2000/svg"
							>
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
								/>
							</svg>
							{#if isInTrial}
								Upgrade Now (£9.99/year)
							{:else}
								Pay Now to Keep Access (£9.99/year)
							{/if}
						</button>
					</div>
				{/if}

				<h2 class="text-3xl font-bold text-gray-900">What You Get</h2>
				<p class="mt-2 text-gray-600">
					{#if isInTrial}
						Enjoy all premium features during your free trial
					{:else if $currentSubscription.hasPaid}
						You have full access to all premium features
					{:else}
						Keep access to all these features forever
					{/if}
				</p>

				<!-- Features List -->
				<div class="mt-8 grid gap-6 sm:grid-cols-2">
					{#each features as feature}
						<div class="flex items-start">
							<div class="flex-shrink-0">
								<svg
									class="h-6 w-6 text-green-500"
									xmlns="http://www.w3.org/2000/svg"
									fill="none"
									viewBox="0 0 24 24"
									stroke="currentColor"
								>
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
										d="M5 13l4 4L19 7"
									/>
								</svg>
							</div>
							<p class="ml-3 text-base text-gray-700">{feature}</p>
						</div>
					{/each}
				</div>
			</div>
		</div>
	</div>

	<!-- Why Upgrade / Support Section -->
	{#if !$currentSubscription.hasPaid}
		<div class="mt-8 overflow-hidden rounded-lg bg-gray-50 px-6 py-8 shadow">
			<h3 class="mb-4 text-center text-xl font-semibold text-gray-900">
				{#if isInTrial}
					Why Upgrade Now?
				{:else}
					Don't Lose Your Data!
				{/if}
			</h3>
			<ul class="mx-auto max-w-2xl space-y-3 text-gray-700">
				{#if isInTrial}
					<li class="flex items-start">
						<span class="mr-2 text-blue-600">✓</span>
						<span>Upgrade now and secure your access before your trial ends</span>
					</li>
					<li class="flex items-start">
						<span class="mr-2 text-blue-600">✓</span>
						<span>No interruptions - keep all your CV data forever</span>
					</li>
				{:else}
					<li class="flex items-start">
						<span class="mr-2 text-red-600">⚠</span>
						<span>Your trial has expired. Pay now to regain access to all your CVs</span>
					</li>
					<li class="flex items-start">
						<span class="mr-2 text-red-600">⚠</span>
						<span>Your data is still safe - upgrade within 30 days to keep everything</span>
					</li>
				{/if}
				<li class="flex items-start">
					<span class="mr-2 text-blue-600">✓</span>
					<span>One-time payment of £9.99 for full access for life</span>
				</li>
			</ul>
		</div>
	{/if}

	<!-- Action Buttons -->
	<div class="mt-8 flex justify-center gap-4">
		{#if $currentSubscription.hasPaid}
			<button
				type="button"
				onclick={goToDashboard}
				class="inline-flex items-center rounded-md border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50"
			>
				Go to Dashboard
			</button>
		{:else}
			<button
				type="button"
				onclick={goToHome}
				class="inline-flex items-center rounded-md border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50"
			>
				Back to Home
			</button>
		{/if}
	</div>

	<!-- Support Section -->
	<div class="mt-12 text-center text-sm text-gray-500">
		<p>
			Need help? Contact us at
			<a href="mailto:wellis321@msn.com" class="font-medium text-blue-600 hover:text-blue-800">
				wellis321@msn.com
			</a>
		</p>
	</div>
</div>
