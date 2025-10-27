<script lang="ts">
	import { onMount } from 'svelte';
	import { page } from '$app/stores';
	import { browser } from '$app/environment';
	import { CV_SECTIONS, sectionStatus, updateSectionStatus } from '$lib/cv-sections';
	import { session as authSession } from '$lib/stores/authStore';
	import { getMaxSections, canExportPdf } from '$lib/utils/subscriptionUtils';
	import { currentSubscription, loadUserSubscription } from '$lib/stores/subscriptionStore';
	import { goto } from '$app/navigation';

	const session = $authSession;
	let errorMessage = $state('');

	// Subscription information
	let maxSections = $state(-1); // -1 means unlimited
	let currentSectionCount = $state(0);
	let isLimited = $state(false);

	// Effect to update section status when session changes
	$effect(() => {
		if (browser && $authSession) {
			updateSectionStatus();
		}
	});

	// Trial information
	let trialDaysRemaining = $derived.by(() => {
		if (!$currentSubscription.trialEndsAt) return null;
		const end = new Date($currentSubscription.trialEndsAt);
		const now = new Date();
		const diff = end.getTime() - now.getTime();
		const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
		return days > 0 ? days : 0;
	});

	// Handle error query parameter and initialize section status
	onMount(async () => {
		if (browser) {
			// Load subscription info
			if (session) {
				await loadUserSubscription();
				await updateSectionStatus();
			}
		}
	});

	// Check subscription limits whenever sections change
	$effect(() => {
		// Get max sections from subscription
		maxSections = getMaxSections();

		// Count total sections with at least one item (excluding profile)
		// Use the section statuses which are already loaded
		currentSectionCount = Object.entries(sectionStatus).filter(
			([sectionId, status]) =>
				// Only count sections with items (status > 0) and exclude profile section
				sectionId !== 'profile' && status > 0
		).length;

		// Check if user is approaching their limit
		isLimited = maxSections !== -1 && currentSectionCount >= maxSections;
	});

	// Navigate to subscription page
	function goToSubscription() {
		goto('/subscription');
	}

	// Get status indicator based on section completion
	function getStatusIndicator(sectionId: string) {
		const status = $sectionStatus[sectionId];
		if (!status || !status.isComplete) {
			return {
				icon: '○',
				text: 'Not started',
				className: 'text-gray-300'
			};
		}

		return {
			icon: '●',
			text: `${status.count} item${status.count !== 1 ? 's' : ''}`,
			className: 'text-green-500'
		};
	}
</script>

<div class="py-6">
	<!-- Dashboard for logged in users -->
	<div class="mb-10 text-center">
		<h1 class="text-3xl font-bold text-gray-900">Your Simple CV Builder Dashboard</h1>
		<p class="mt-2 text-lg text-gray-600">Complete each section to create your professional CV.</p>
	</div>

	{#if $currentSubscription.isTrial && trialDaysRemaining && trialDaysRemaining > 0}
		<div class="mb-6 rounded-md bg-amber-50 p-4">
			<div class="flex">
				<div class="flex-shrink-0">
					<svg
						class="h-5 w-5 text-amber-400"
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 20 20"
						fill="currentColor"
						aria-hidden="true"
					>
						<path
							fill-rule="evenodd"
							d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
							clip-rule="evenodd"
						/>
					</svg>
				</div>
				<div class="ml-3">
					<h3 class="text-sm font-medium text-amber-800">Free Trial Active</h3>
					<div class="mt-2 text-sm text-amber-700">
						<p>
							🆓 You have <strong
								>{trialDaysRemaining} {trialDaysRemaining === 1 ? 'day' : 'days'}</strong
							> left in your free trial. Full access to all features!
						</p>
					</div>
				</div>
			</div>
		</div>
	{:else if $currentSubscription.hasPaid}
		<div class="mb-6 rounded-md bg-green-50 p-4">
			<div class="flex">
				<div class="flex-shrink-0">
					<svg
						class="h-5 w-5 text-green-400"
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 20 20"
						fill="currentColor"
						aria-hidden="true"
					>
						<path
							fill-rule="evenodd"
							d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
							clip-rule="evenodd"
						/>
					</svg>
				</div>
				<div class="ml-3">
					<h3 class="text-sm font-medium text-green-800">Full Access Active</h3>
					<div class="mt-2 text-sm text-green-700">
						<p>✅ Thank you for your support! You have full access to all features for one year.</p>
					</div>
				</div>
			</div>
		</div>
	{:else if isLimited}
		<div class="mb-6 rounded-md bg-yellow-50 p-4">
			<div class="flex">
				<div class="flex-shrink-0">
					<svg
						class="h-5 w-5 text-yellow-400"
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 20 20"
						fill="currentColor"
						aria-hidden="true"
					>
						<path
							fill-rule="evenodd"
							d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
							clip-rule="evenodd"
						/>
					</svg>
				</div>
				<div class="ml-3">
					<h3 class="text-sm font-medium text-yellow-800">Section limit reached</h3>
					<div class="mt-2 text-sm text-yellow-700">
						<p>
							Your trial has expired and you've used {currentSectionCount} of {maxSections} available
							sections. Pay £9.99/year to regain access to all your CV data and features.
						</p>
					</div>
					<div class="mt-4">
						<div class="-mx-2 -my-1.5 flex">
							<button
								type="button"
								onclick={() => goto('/subscription')}
								class="rounded-md bg-yellow-50 px-2 py-1.5 text-sm font-medium text-yellow-800 hover:bg-yellow-100 focus:ring-2 focus:ring-yellow-600 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:outline-none"
							>
								Pay £9.99/year to Continue
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	{/if}

	<div class="mb-8 rounded-md bg-white p-4 shadow">
		<h2 class="text-lg font-medium text-gray-900">Your Access</h2>
		<div class="mt-2">
			{#if $currentSubscription.hasPaid}
				<p class="text-sm text-gray-600">
					You have <span class="font-medium">Full Access</span> for one year
				</p>
			{:else if $currentSubscription.isTrial && trialDaysRemaining && trialDaysRemaining > 0}
				<p class="text-sm text-gray-600">
					Free trial: <span class="font-medium"
						>{trialDaysRemaining} {trialDaysRemaining === 1 ? 'day' : 'days'}</span
					> remaining
				</p>
			{:else}
				<p class="text-sm text-gray-600">
					<span class="font-medium">Trial expired</span> - Pay £9.99/year to continue
				</p>
			{/if}

			<div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
				<div class="flex items-start">
					<div class="flex-shrink-0">
						{#if maxSections === -1}
							<!-- Checkmark for unlimited -->
							<svg
								class="h-5 w-5 text-green-500"
								xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 20 20"
								fill="currentColor"
							>
								<path
									fill-rule="evenodd"
									d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
									clip-rule="evenodd"
								/>
							</svg>
						{:else}
							<!-- Limited indicator -->
							<svg
								class="h-5 w-5 text-yellow-500"
								xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 20 20"
								fill="currentColor"
							>
								<path
									fill-rule="evenodd"
									d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
									clip-rule="evenodd"
								/>
							</svg>
						{/if}
					</div>
					<div class="ml-3 text-sm">
						<p class="font-medium text-gray-700">
							{maxSections === -1
								? 'Unlimited Sections'
								: `${currentSectionCount}/${maxSections} Sections Used`}
						</p>
					</div>
				</div>

				<div class="flex items-start">
					<div class="flex-shrink-0">
						{#if canExportPdf()}
							<!-- Checkmark for PDF -->
							<svg
								class="h-5 w-5 text-green-500"
								xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 20 20"
								fill="currentColor"
							>
								<path
									fill-rule="evenodd"
									d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
									clip-rule="evenodd"
								/>
							</svg>
						{:else}
							<!-- X for no PDF -->
							<svg
								class="h-5 w-5 text-gray-400"
								xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 20 20"
								fill="currentColor"
							>
								<path
									fill-rule="evenodd"
									d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
									clip-rule="evenodd"
								/>
							</svg>
						{/if}
					</div>
					<div class="ml-3 text-sm">
						<p class="font-medium text-gray-700">PDF Export</p>
					</div>
				</div>

				<div class="flex items-start">
					<div class="flex-shrink-0">
						<!-- Checkmark for templates -->
						<svg
							class="h-5 w-5 text-green-500"
							xmlns="http://www.w3.org/2000/svg"
							viewBox="0 0 20 20"
							fill="currentColor"
						>
							<path
								fill-rule="evenodd"
								d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
								clip-rule="evenodd"
							/>
						</svg>
					</div>
					<div class="ml-3 text-sm">
						<p class="font-medium text-gray-700">
							{$currentSubscription.plan?.features?.templates?.length || 1} Templates
						</p>
					</div>
				</div>
			</div>

			{#if !$currentSubscription.hasPaid}
				<div class="mt-4">
					{#if $currentSubscription.isTrial && trialDaysRemaining && trialDaysRemaining > 0}
						<p class="mb-2 text-xs text-gray-500">
							Trial ends in {trialDaysRemaining}
							{trialDaysRemaining === 1 ? 'day' : 'days'}
						</p>
					{/if}
					<button
						type="button"
						onclick={() => goto('/subscription')}
						class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
					>
						{#if $currentSubscription.isTrial && trialDaysRemaining && trialDaysRemaining > 0}
							Upgrade for £9.99/year
						{:else}
							Pay £9.99/year to Continue
						{/if}
					</button>
				</div>
			{/if}
		</div>
	</div>

	<div class="mx-auto grid max-w-7xl gap-5 sm:grid-cols-2 lg:grid-cols-3">
		{#each CV_SECTIONS as section}
			{@const status = getStatusIndicator(section.id)}
			<a
				href={section.path}
				class="group flex flex-col overflow-hidden rounded-lg shadow-lg transition-all duration-200 hover:bg-gray-50 hover:shadow-xl"
			>
				<div class="flex flex-1 flex-col justify-between bg-white p-6 group-hover:bg-gray-50">
					<div class="flex-1">
						<div class="flex justify-between">
							<p class="text-xl font-semibold text-gray-900 group-hover:text-indigo-600">
								{section.name}
							</p>
							<span class={`text-lg font-bold ${status.className}`} title={status.text}>
								{status.icon}
							</span>
						</div>
						<p class="mt-3 text-base text-gray-500">{section.description}</p>
						<div class="mt-4">
							{#if $sectionStatus[section.id]?.isComplete}
								<span
									class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800"
								>
									{$sectionStatus[section.id]?.count} entr{$sectionStatus[section.id]?.count !== 1
										? 'ies'
										: 'y'} added
								</span>
							{:else}
								<span
									class="inline-flex items-center text-sm font-medium text-indigo-600 group-hover:underline"
								>
									Add information
									<svg
										class="ml-1 h-4 w-4"
										fill="currentColor"
										viewBox="0 0 20 20"
										xmlns="http://www.w3.org/2000/svg"
									>
										<path
											fill-rule="evenodd"
											d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
											clip-rule="evenodd"
										/>
									</svg>
								</span>
							{/if}
						</div>
					</div>
				</div>
			</a>
		{/each}
	</div>
</div>
