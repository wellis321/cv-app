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
</script>

<svelte:head>
	<title>Free Access | CV Builder</title>
</svelte:head>

<div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
	<!-- Hero Section -->
	<div class="text-center">
		<div class="mx-auto max-w-3xl">
			<h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
				🎉 Free Access During Development
			</h1>
			<p class="mx-auto mt-5 max-w-xl text-xl text-gray-500">
				Sign up now and get full access to all CV features while we're building. No credit card
				required!
			</p>
		</div>
	</div>

	<!-- Main Card -->
	<div class="mt-12 overflow-hidden rounded-lg bg-white shadow-lg">
		<div class="px-6 py-10 sm:p-12">
			<div class="text-center">
				<!-- Current Access Status -->
				<div class="mb-8">
					<div
						class="inline-flex items-center rounded-full bg-green-50 px-4 py-2 text-sm font-medium text-green-800"
					>
						<svg class="mr-2 -ml-1 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
							<path
								fill-rule="evenodd"
								d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
								clip-rule="evenodd"
							/>
						</svg>
						Your Account: {currentPlanName} (All Features Enabled)
					</div>
				</div>

				<h2 class="text-3xl font-bold text-gray-900">What You Get Free</h2>
				<p class="mt-2 text-gray-600">
					Complete access to all premium features during our development phase
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

	<!-- Pricing Information -->
	<div class="mt-8 overflow-hidden rounded-lg border border-blue-200 bg-blue-50">
		<div class="px-6 py-6 sm:p-8">
			<h3 class="mb-4 text-xl font-bold text-gray-900">Future Pricing</h3>
			<p class="mb-2 text-gray-700">
				While we're developing, enjoy free access to everything. When we officially launch, we plan
				to offer:
			</p>
			<div class="mt-4 space-y-2">
				<div class="flex justify-between">
					<span class="font-medium text-gray-900">Premium Annual Plan:</span>
					<span class="font-bold text-blue-600">£19.99/year</span>
				</div>
				<p class="text-sm text-gray-600">
					No monthly subscriptions - just one simple payment for a full year of premium features.
				</p>
			</div>
		</div>
	</div>

	<!-- Call to Action -->
	<div class="mt-12 text-center">
		{#if $session}
			<div class="space-y-4">
				<p class="text-lg text-gray-700">You're all set! Start building your CV.</p>
				<button
					onclick={goToDashboard}
					class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
				>
					Go to Dashboard
				</button>
			</div>
		{:else}
			<div class="space-y-4">
				<p class="text-lg text-gray-700">Ready to create your professional CV?</p>
				<div class="flex justify-center gap-4">
					<button
						onclick={goToHome}
						class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
					>
						Sign Up Free
					</button>
				</div>
				<p class="text-sm text-gray-500">No credit card required • Full access • Cancel anytime</p>
			</div>
		{/if}
	</div>

	<!-- Terms Note -->
	<div class="mt-8 rounded-md border border-yellow-200 bg-yellow-50 p-4">
		<div class="flex">
			<div class="flex-shrink-0">
				<svg
					class="h-5 w-5 text-yellow-400"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 20 20"
					fill="currentColor"
				>
					<path
						fill-rule="evenodd"
						d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
						clip-rule="evenodd"
					/>
				</svg>
			</div>
			<div class="ml-3">
				<p class="text-sm text-yellow-800">
					<strong>Note:</strong> During development, all users have free access to all features. This
					is a development environment, and no payments are processed. When we launch formally, premium
					features will require a subscription.
				</p>
			</div>
		</div>
	</div>
</div>
