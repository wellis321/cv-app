<script lang="ts">
	import { onMount } from 'svelte';

	let addColumnStatus = $state('');
	let updateDataStatus = $state('');
	let isLoading = $state(false);
	let success = $state('');
	let error = $state('');

	async function addQualificationColumn() {
		isLoading = true;
		error = '';
		addColumnStatus = 'Running...';

		try {
			const response = await fetch('/api/add-qualification-column');
			const result = await response.json();

			if (result.success) {
				addColumnStatus = result.message;
				success = 'Column added successfully!';
			} else {
				addColumnStatus = 'Failed: ' + (result.error || 'Unknown error');
				error = result.error || 'Failed to add qualification column';
			}
		} catch (err) {
			console.error('Error adding column:', err);
			addColumnStatus = 'Error: ' + String(err);
			error = String(err);
		} finally {
			isLoading = false;
		}
	}

	async function updateEducationData() {
		isLoading = true;
		error = '';
		updateDataStatus = 'Running...';

		try {
			const response = await fetch('/api/update-education');
			const result = await response.json();

			if (result.success) {
				updateDataStatus = result.message;
				success = 'Data updated successfully!';
			} else {
				updateDataStatus = 'Failed: ' + (result.error || 'Unknown error');
				error = result.error || 'Failed to update education data';
			}
		} catch (err) {
			console.error('Error updating data:', err);
			updateDataStatus = 'Error: ' + String(err);
			error = String(err);
		} finally {
			isLoading = false;
		}
	}

	function runAllSteps() {
		addQualificationColumn().then(() => {
			if (!error) {
				updateEducationData();
			}
		});
	}
</script>

<div class="mx-auto max-w-xl p-6">
	<h1 class="mb-6 text-2xl font-bold">Education Table Migration</h1>

	{#if error}
		<div class="mb-4 rounded bg-red-100 p-4 text-red-700">
			<p class="font-medium">Error: {error}</p>
		</div>
	{/if}

	{#if success}
		<div class="mb-4 rounded bg-green-100 p-4 text-green-700">
			<p class="font-medium">{success}</p>
		</div>
	{/if}

	<div class="mb-6 rounded border p-4">
		<h2 class="mb-2 text-lg font-semibold">Migration Steps</h2>
		<p class="mb-4 text-gray-700">
			This utility will migrate the education table to use "qualification" instead of "degree".
		</p>

		<ol class="mb-4 list-decimal pl-6">
			<li class="mb-2">Add the "qualification" column if it doesn't exist</li>
			<li class="mb-2">Copy data from "degree" column to "qualification" column</li>
		</ol>

		<button
			onclick={runAllSteps}
			disabled={isLoading}
			class="w-full rounded bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none disabled:opacity-50"
		>
			{isLoading ? 'Running...' : 'Run All Migration Steps'}
		</button>
	</div>

	<div class="space-y-4">
		<div class="rounded border p-4">
			<h3 class="mb-2 font-semibold">Step 1: Add Qualification Column</h3>
			<p class="mb-2 text-sm text-gray-600">
				This step adds the "qualification" column to the education table if it doesn't already
				exist.
			</p>
			{#if addColumnStatus}
				<div class="mb-2 text-sm font-medium">
					Status: <span
						class={addColumnStatus.includes('Failed') || addColumnStatus.includes('Error')
							? 'text-red-600'
							: 'text-green-600'}>{addColumnStatus}</span
					>
				</div>
			{/if}
			<button
				onclick={addQualificationColumn}
				disabled={isLoading}
				class="mt-2 w-full rounded bg-gray-200 px-3 py-1 text-sm font-medium text-gray-800 hover:bg-gray-300 focus:ring-2 focus:ring-gray-400 focus:outline-none disabled:opacity-50"
			>
				Run Step 1 Only
			</button>
		</div>

		<div class="rounded border p-4">
			<h3 class="mb-2 font-semibold">Step 2: Update Education Data</h3>
			<p class="mb-2 text-sm text-gray-600">
				This step copies data from "degree" to "qualification" for all your education records.
			</p>
			{#if updateDataStatus}
				<div class="mb-2 text-sm font-medium">
					Status: <span
						class={updateDataStatus.includes('Failed') || updateDataStatus.includes('Error')
							? 'text-red-600'
							: 'text-green-600'}>{updateDataStatus}</span
					>
				</div>
			{/if}
			<button
				onclick={updateEducationData}
				disabled={isLoading}
				class="mt-2 w-full rounded bg-gray-200 px-3 py-1 text-sm font-medium text-gray-800 hover:bg-gray-300 focus:ring-2 focus:ring-gray-400 focus:outline-none disabled:opacity-50"
			>
				Run Step 2 Only
			</button>
		</div>
	</div>

	<div class="mt-6">
		<a href="/education" class="text-indigo-600 hover:text-indigo-800 hover:underline">
			← Back to Education
		</a>
	</div>
</div>
