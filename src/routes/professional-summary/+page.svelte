<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import { page } from '$app/stores';
	import { updateSectionStatus } from '$lib/cv-sections';
	import SectionNavigation from '$lib/components/SectionNavigation.svelte';
	import FormSection from '$lib/components/FormSection.svelte';
	import FormField from '$lib/components/FormField.svelte';
	import { session } from '$lib/stores/authStore';
	import { supabase } from '$lib/supabase';
	import { getCsrfTokenFromDocument } from '$lib/security/clientCsrf';

	interface ProfessionalSummary {
		id: string;
		description: string | null;
		strengths: Array<{
			id: string;
			strength: string;
			sort_order: number;
		}>;
	}

	let { data, form } = $props<{
		data: { professionalSummary: ProfessionalSummary | null };
		form?: any;
	}>();

	let summaryContent = $state(data.professionalSummary?.description ?? '');
	let strengths = $state<Array<{ id: string; strength: string; sort_order: number }>>(
		data.professionalSummary?.strengths ?? []
	);
	let loading = $state(false);
	let error = $state<string | null>(null);
	let success = $state<string | null>(null);

	// Check authentication on client side
	$effect(() => {
		if (!$session) {
			console.log('No session found, redirecting to login');
			goto('/');
		}
	});

	// Load data on mount
	onMount(async () => {
		console.log('Server-side data:', data.professionalSummary);

		if ($session?.user?.id) {
			console.log('Loading professional summary for user:', $session.user.id);
			console.log('Session token:', $session.access_token ? 'present' : 'missing');

			try {
				const response = await fetch('/api/professional-summary', {
					method: 'GET',
					headers: {
						Authorization: `Bearer ${$session.access_token}`,
						'Content-Type': 'application/json'
					}
				});

				console.log('Load response status:', response.status);

				if (response.ok) {
					const result = await response.json();
					console.log('Load result:', result);
					if (result.success && result.professionalSummary) {
						console.log('Raw professional summary data:', result.professionalSummary);
						console.log('Summary content from API:', result.professionalSummary.description);
						summaryContent = result.professionalSummary.description || '';
						strengths = result.professionalSummary.strengths || [];
						console.log('Loaded professional summary data:', {
							summaryContent: summaryContent,
							strengthsCount: strengths.length
						});
						console.log('Summary content variable after assignment:', summaryContent);
					} else {
						console.log('No professional summary data found');
					}
				} else {
					console.error('Failed to load professional summary:', response.status);
				}
			} catch (err) {
				console.error('Error loading professional summary:', err);
			}
		}
	});

	// Add a new strength
	function addStrength() {
		strengths = [...strengths, { id: '', strength: '', sort_order: strengths.length }];
	}

	// Remove a strength
	function removeStrength(index: number) {
		strengths = strengths.filter((_, i) => i !== index);
		// Update sort order
		strengths = strengths.map((strength, i) => ({ ...strength, sort_order: i }));
	}

	// Move strength up
	function moveStrengthUp(index: number) {
		if (index > 0) {
			const newStrengths = [...strengths];
			[newStrengths[index - 1], newStrengths[index]] = [
				newStrengths[index],
				newStrengths[index - 1]
			];
			strengths = newStrengths.map((strength, i) => ({ ...strength, sort_order: i }));
		}
	}

	// Move strength down
	function moveStrengthDown(index: number) {
		if (index < strengths.length - 1) {
			const newStrengths = [...strengths];
			[newStrengths[index], newStrengths[index + 1]] = [
				newStrengths[index + 1],
				newStrengths[index]
			];
			strengths = newStrengths.map((strength, i) => ({ ...strength, sort_order: i }));
		}
	}

	// Handle form submission using API endpoint
	async function handleSubmit(event: Event) {
		event.preventDefault();
		loading = true;
		error = null;
		success = null;

		console.log('Submitting professional summary for user:', $session?.user?.id);
		console.log('Session token:', $session?.access_token ? 'present' : 'missing');

		// Get CSRF token
		const csrfToken = getCsrfTokenFromDocument();
		console.log('CSRF token:', csrfToken ? 'present' : 'missing');

		try {
			const response = await fetch('/api/professional-summary', {
				method: 'POST',
				headers: {
					Authorization: `Bearer ${$session?.access_token}`,
					'Content-Type': 'application/json',
					'X-CSRF-Token': csrfToken || ''
				},
				body: JSON.stringify({
					description: summaryContent,
					strengths: strengths.filter((s) => s.strength.trim() !== '')
				})
			});

			console.log('Submit response status:', response.status);

			if (response.ok) {
				const result = await response.json();
				console.log('Submit result:', result);
				if (result.success) {
					success = 'Professional summary saved successfully!';
					console.log('Success message set:', success);
					// Update section status
					updateSectionStatus();

					// Also refresh the CV store data to update preview-cv page
					import('$lib/stores/cvDataStore')
						.then(async (module) => {
							const { cvStore } = module;
							// Force refresh the CV store data
							if ($session?.user?.id) {
								// Get the current username from the profile
								const { data: profileData } = await supabase
									.from('profiles')
									.select('username')
									.eq('id', $session.user.id)
									.single();

								if (profileData?.username) {
									await cvStore.loadByUsername(profileData.username);
								}
							}
						})
						.catch((err) => {
							console.warn('Could not refresh CV store data:', err);
						});

					// Clear success message after 3 seconds
					setTimeout(() => {
						success = null;
					}, 3000);
				} else {
					error = result.error || 'Failed to save professional summary';
				}
			} else {
				const result = await response.json();
				error = result.error || `Failed to save professional summary (${response.status})`;
			}
		} catch (err) {
			console.error('Error saving professional summary:', err);
			error = 'An unexpected error occurred while saving your professional summary';
		} finally {
			loading = false;
		}
	}

	// No need for client-side redirect since server-side loading handles auth
</script>

<svelte:head>
	<title>Professional Summary - Simple CV Builder</title>
</svelte:head>

<div class="mx-auto max-w-4xl px-4 py-8">
	<SectionNavigation currentPath="/professional-summary" />

	<div class="mb-8">
		<h1 class="text-3xl font-bold text-gray-900">Professional Summary</h1>
		<p class="mt-2 text-lg text-gray-600">
			Create a compelling professional summary that highlights your key strengths and experience.
		</p>
	</div>

	{#if error || form?.error}
		<div class="mb-6 rounded-md bg-red-50 p-4">
			<div class="flex">
				<div class="flex-shrink-0">
					<svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
						<path
							fill-rule="evenodd"
							d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
							clip-rule="evenodd"
						/>
					</svg>
				</div>
				<div class="ml-3">
					<p class="text-sm font-medium text-red-800">{error || form?.error}</p>
				</div>
			</div>
		</div>
	{/if}

	{#if success}
		<div class="mb-6 rounded-md bg-green-50 p-4">
			<div class="flex">
				<div class="flex-shrink-0">
					<svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
						<path
							fill-rule="evenodd"
							d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
							clip-rule="evenodd"
						/>
					</svg>
				</div>
				<div class="ml-3">
					<p class="text-sm font-medium text-green-800">{success}</p>
				</div>
			</div>
		</div>
	{/if}

	<form onsubmit={handleSubmit} class="space-y-8">
		<FormSection
			title="Professional Summary"
			description="Write a brief overview of your professional background and key strengths."
		>
			<div>
				<textarea
					id="summary-content"
					name="summary-content"
					bind:value={summaryContent}
					rows="6"
					placeholder="Write a compelling professional summary that highlights your experience, skills, and career objectives..."
					class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
					required
				></textarea>
			</div>
		</FormSection>

		<FormSection
			title="Key Strengths"
			description="List your key professional strengths and skills."
		>
			<div class="space-y-4">
				{#each strengths as strength, index}
					<div class="flex items-start space-x-3">
						<div class="flex-1">
							<input
								type="text"
								name="strengths"
								bind:value={strength.strength}
								placeholder="Enter a key strength or skill..."
								class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
							/>
						</div>
						<div class="flex space-x-1">
							<button
								type="button"
								onclick={() => moveStrengthUp(index)}
								disabled={index === 0}
								class="rounded-md p-2 text-gray-400 hover:text-gray-600 disabled:cursor-not-allowed disabled:opacity-50"
								title="Move up"
							>
								<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
										d="M5 15l7-7 7 7"
									/>
								</svg>
							</button>
							<button
								type="button"
								onclick={() => moveStrengthDown(index)}
								disabled={index === strengths.length - 1}
								class="rounded-md p-2 text-gray-400 hover:text-gray-600 disabled:cursor-not-allowed disabled:opacity-50"
								title="Move down"
							>
								<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
										d="M19 9l-7 7-7-7"
									/>
								</svg>
							</button>
							<button
								type="button"
								onclick={() => removeStrength(index)}
								class="rounded-md p-2 text-red-400 hover:text-red-600"
								title="Remove"
							>
								<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
										d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
									/>
								</svg>
							</button>
						</div>
					</div>
				{/each}

				<button
					type="button"
					onclick={addStrength}
					class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
				>
					<svg class="mr-1.5 -ml-0.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M12 6v6m0 0v6m0-6h6m-6 0H6"
						/>
					</svg>
					Add Strength
				</button>
			</div>
		</FormSection>

		<div class="flex justify-end space-x-3">
			<button
				type="button"
				onclick={() => goto('/work-experience')}
				class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
			>
				Skip for now
			</button>
			<button
				type="submit"
				disabled={loading}
				class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
			>
				{loading ? 'Saving...' : 'Save Professional Summary'}
			</button>
		</div>
	</form>
</div>
