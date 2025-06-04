<script lang="ts">
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';
	import { session } from '$lib/stores/authStore';
	import { goto } from '$app/navigation';
	import { isAdminUser } from '$lib/adminConfig';

	// State variables
	let migrationStatus = $state('');
	let isLoading = $state(false);
	let error = $state<string | null>(null);
	let success = $state(false);
	let instructions = $state<string[]>([]);

	onMount(() => {
		// Check if user is authorized
		if (!browser || !$session?.user) {
			goto('/');
			return;
		}

		// Check if user is admin using the centralized function
		const isAdmin = isAdminUser($session.user.email);

		if (!isAdmin) {
			error = 'You do not have permission to apply migrations.';
		}
	});

	async function getInstructions() {
		if (!browser || !$session?.user) return;

		try {
			isLoading = true;
			error = null;
			success = false;
			migrationStatus = 'Fetching migration instructions...';

			const response = await fetch('/admin/analytics/apply-migration', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				}
			});

			const data = await response.json();

			if (response.ok) {
				success = true;
				migrationStatus = data.message;
				instructions = data.instructions || [];
			} else {
				error = data.error || 'Failed to get migration instructions';
				success = false;
			}
		} catch (err) {
			console.error('Error getting migration instructions:', err);
			error = 'Failed to get migration instructions. Please check the console for more details.';
			success = false;
		} finally {
			isLoading = false;
		}
	}
</script>

<svelte:head>
	<title>Apply Analytics Migration</title>
	<meta name="description" content="Apply the analytics tables migration" />
</svelte:head>

<div class="container mx-auto max-w-4xl px-4 py-8">
	<div class="mb-6">
		<h1 class="text-2xl font-bold">Apply Analytics Migration</h1>
		<p class="mt-2 text-gray-600">This page helps you set up the analytics system tables.</p>
	</div>

	{#if error}
		<div class="mb-6 rounded bg-red-100 p-4 text-red-800">
			<p>{error}</p>
		</div>
	{/if}

	{#if success}
		<div class="mb-6 rounded bg-green-100 p-4 text-green-800">
			<p>Migration instructions prepared successfully.</p>
		</div>
	{/if}

	<div class="rounded-lg bg-white p-6 shadow">
		<h2 class="mb-4 text-lg font-semibold">Analytics Migration</h2>
		<p class="mb-4">
			This migration will create the necessary tables and views to track page visits on your CV
			application.
		</p>

		<div class="mb-6 rounded bg-gray-50 p-4">
			<h3 class="mb-2 font-medium">How to apply the migration:</h3>

			{#if instructions.length > 0}
				<ol class="ml-6 list-decimal space-y-2">
					{#each instructions as instruction}
						<li>{instruction}</li>
					{/each}
				</ol>
			{:else}
				<ol class="ml-6 list-decimal space-y-2">
					<li>
						Log in to your <a
							href="https://app.supabase.com"
							target="_blank"
							rel="noreferrer"
							class="text-indigo-600 hover:underline">Supabase dashboard</a
						>
					</li>
					<li>Select your project</li>
					<li>Go to the SQL Editor</li>
					<li>Create a new query</li>
					<li>
						Paste the migration SQL content (available in your project at <code
							class="rounded bg-gray-100 px-1 py-0.5"
							>src/lib/migrations/20240530_create_page_analytics.sql</code
						>)
					</li>
					<li>Run the query</li>
				</ol>
			{/if}
		</div>

		<div class="mt-4">
			<button
				on:click={getInstructions}
				disabled={isLoading}
				class="rounded bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
			>
				{isLoading ? 'Processing...' : 'Get Migration Instructions'}
			</button>

			{#if migrationStatus}
				<p class="mt-4 text-gray-700">{migrationStatus}</p>
			{/if}
		</div>
	</div>

	{#if success}
		<div class="mt-6">
			<a
				href="/admin/analytics"
				class="inline-block rounded bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700"
			>
				Go to Analytics Dashboard
			</a>
		</div>
	{/if}
</div>
