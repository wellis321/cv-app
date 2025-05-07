<script lang="ts">
	import { browser } from '$app/environment';
	import { goto } from '$app/navigation';
	import { supabase } from '$lib/supabase';
	import { session as authSession } from '$lib/stores/authStore';
	import { enhance } from '$app/forms';
	import type { SubmitFunction } from '@sveltejs/kit';

	interface PageData {
		user: {
			id: string;
			email: string;
		};
		stats: {
			education: {
				total: number;
				needsUpdate: number;
			};
			projects: {
				total: number;
				needsUpdate: number;
			};
		};
		form?: {
			error?: string;
		};
	}

	const { data } = $props<{ data: PageData }>();
	const session = $authSession;

	// UI state
	let loading = $state(false);
	let fixingAll = $state(false);
	let fixingEducation = $state(false);
	let fixingProjects = $state(false);
	let educationSuccess = $state('');
	let projectsSuccess = $state('');
	let error = $state('');
	let educationStats = $state({
		total: data.stats.education.total,
		needsUpdate: data.stats.education.needsUpdate,
		updated: 0
	});
	let projectStats = $state({
		total: data.stats.projects.total,
		needsUpdate: data.stats.projects.needsUpdate,
		updated: 0
	});

	// Form submit handlers with enhance
	const submitFixAll: SubmitFunction = () => {
		fixingAll = true;
		error = '';
		educationSuccess = '';
		projectsSuccess = '';

		return async ({ result, update }) => {
			await update();

			if (result.type === 'success') {
				const resultData = result.data as any;
				if (resultData?.success) {
					if (resultData?.education?.stats) {
						educationStats = resultData.education.stats;
						educationSuccess = `Updated ${resultData.education.stats.updated} of ${resultData.education.stats.needsUpdate} education records that needed fixing.`;
					}

					if (resultData?.projects?.stats) {
						projectStats = resultData.projects.stats;
						projectsSuccess = `Updated ${resultData.projects.stats.updated} of ${resultData.projects.stats.needsUpdate} project records that needed fixing.`;
					}
				} else if (resultData?.error) {
					error = resultData.error;
				}
			}

			fixingAll = false;
		};
	};

	const submitFixEducation: SubmitFunction = () => {
		fixingEducation = true;
		error = '';
		educationSuccess = '';

		return async ({ result, update }) => {
			await update();

			if (result.type === 'success') {
				const resultData = result.data as any;
				if (resultData?.success && resultData?.stats) {
					educationStats = resultData.stats;
					educationSuccess = `Updated ${resultData.stats.updated} of ${resultData.stats.needsUpdate} education records that needed fixing.`;
				} else if (resultData?.error) {
					error = resultData.error;
				}
			}

			fixingEducation = false;
		};
	};

	const submitFixProjects: SubmitFunction = () => {
		fixingProjects = true;
		error = '';
		projectsSuccess = '';

		return async ({ result, update }) => {
			await update();

			if (result.type === 'success') {
				const resultData = result.data as any;
				if (resultData?.success && resultData?.stats) {
					projectStats = resultData.stats;
					projectsSuccess = `Updated ${resultData.stats.updated} of ${resultData.stats.needsUpdate} project records that needed fixing.`;
				} else if (resultData?.error) {
					error = resultData.error;
				}
			}

			fixingProjects = false;
		};
	};

	// Clear success messages after 5 seconds
	$effect(() => {
		if (educationSuccess) {
			const timeout = setTimeout(() => {
				educationSuccess = '';
			}, 5000);
			return () => clearTimeout(timeout);
		}
	});

	$effect(() => {
		if (projectsSuccess) {
			const timeout = setTimeout(() => {
				projectsSuccess = '';
			}, 5000);
			return () => clearTimeout(timeout);
		}
	});
</script>

<div class="mx-auto max-w-xl p-6">
	<h1 class="mb-6 text-2xl font-bold">Fix Data Display Issues</h1>

	{#if !session}
		<div class="mb-4 rounded bg-yellow-100 p-4">
			<p class="font-medium">You need to be logged in to fix your data.</p>
			<button
				onclick={() => goto('/')}
				class="mt-2 rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700"
			>
				Go to Login
			</button>
		</div>
	{:else}
		{#if error}
			<div class="mb-4 rounded bg-red-100 p-4 text-red-700">
				<p class="font-medium">{error}</p>
			</div>
		{/if}

		{#if educationSuccess}
			<div class="mb-4 rounded bg-green-100 p-4 text-green-700">
				<p class="font-medium">Education: {educationSuccess}</p>
			</div>
		{/if}

		{#if projectsSuccess}
			<div class="mb-4 rounded bg-green-100 p-4 text-green-700">
				<p class="font-medium">Projects: {projectsSuccess}</p>
			</div>
		{/if}

		<div class="mb-6 rounded border bg-gray-50 p-4">
			<h2 class="mb-2 text-lg font-semibold">Data Status</h2>

			<div class="mb-3 grid grid-cols-2 gap-4">
				<div class="rounded border bg-white p-3">
					<h3 class="font-medium text-gray-700">Education</h3>
					<p class="mt-1 text-sm text-gray-600">
						<span class="rounded-sm bg-blue-50 px-1 py-0.5 text-blue-800"
							>{educationStats.total}</span
						> total records
					</p>
					<p class="mt-1 text-sm text-gray-600">
						<span class="rounded-sm bg-yellow-50 px-1 py-0.5 text-yellow-800"
							>{educationStats.needsUpdate}</span
						> need fixing
					</p>
					{#if educationStats.updated > 0}
						<p class="mt-1 text-sm text-green-600">
							<span class="rounded-sm bg-green-50 px-1 py-0.5 text-green-800"
								>{educationStats.updated}</span
							> updated
						</p>
					{/if}
				</div>
				<div class="rounded border bg-white p-3">
					<h3 class="font-medium text-gray-700">Projects</h3>
					<p class="mt-1 text-sm text-gray-600">
						<span class="rounded-sm bg-blue-50 px-1 py-0.5 text-blue-800">{projectStats.total}</span
						> total records
					</p>
					<p class="mt-1 text-sm text-gray-600">
						<span class="rounded-sm bg-yellow-50 px-1 py-0.5 text-yellow-800"
							>{projectStats.needsUpdate}</span
						> need fixing
					</p>
					{#if projectStats.updated > 0}
						<p class="mt-1 text-sm text-green-600">
							<span class="rounded-sm bg-green-50 px-1 py-0.5 text-green-800"
								>{projectStats.updated}</span
							> updated
						</p>
					{/if}
				</div>
			</div>

			{#if educationStats.needsUpdate > 0 || projectStats.needsUpdate > 0}
				<div class="mt-2 rounded-sm bg-yellow-100 p-2 text-sm text-yellow-800">
					<p class="font-medium">Display issues detected!</p>
					<p class="mt-1">Please click "Fix All Display Issues" to resolve them.</p>
				</div>
			{:else if educationStats.updated > 0 || projectStats.updated > 0}
				<div class="mt-2 rounded-sm bg-green-100 p-2 text-sm text-green-800">
					<p class="font-medium">Issues have been fixed!</p>
					<p class="mt-1">Your data is now displaying correctly.</p>
				</div>
			{:else}
				<div class="mt-2 rounded-sm bg-green-100 p-2 text-sm text-green-800">
					<p class="font-medium">All data looks good!</p>
					<p class="mt-1">No display issues detected.</p>
				</div>
			{/if}
		</div>

		<div class="mb-6 rounded border bg-white p-4">
			<h2 class="mb-2 text-lg font-semibold">Data Fix Tool</h2>
			<p class="mb-4 text-gray-700">
				This utility will fix display issues with your education and projects data by updating your
				database records to ensure all fields are synchronized.
			</p>

			<form method="POST" action="?/fixAll" use:enhance={submitFixAll}>
				<button
					type="submit"
					disabled={fixingAll || fixingEducation || fixingProjects}
					class="flex w-full items-center justify-center rounded bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none disabled:opacity-50"
				>
					{#if fixingAll}
						<svg
							class="mr-2 -ml-1 h-4 w-4 animate-spin text-white"
							xmlns="http://www.w3.org/2000/svg"
							fill="none"
							viewBox="0 0 24 24"
						>
							<circle
								class="opacity-25"
								cx="12"
								cy="12"
								r="10"
								stroke="currentColor"
								stroke-width="4"
							></circle>
							<path
								class="opacity-75"
								fill="currentColor"
								d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
							></path>
						</svg>
						Fixing All Issues...
					{:else}
						Fix All Display Issues
					{/if}
				</button>
			</form>
		</div>

		<div class="grid grid-cols-2 gap-4">
			<div class="rounded border bg-white p-4">
				<h3 class="mb-2 font-semibold">Fix Education Data</h3>
				<p class="mb-2 text-sm text-gray-600">
					This will update education records to ensure both qualification and degree fields are
					synchronized.
				</p>
				<form method="POST" action="?/fixEducation" use:enhance={submitFixEducation}>
					<button
						type="submit"
						disabled={fixingAll || fixingEducation || fixingProjects}
						class="mt-2 flex w-full items-center justify-center rounded bg-gray-200 px-3 py-1.5 text-sm font-medium text-gray-800 hover:bg-gray-300 focus:ring-2 focus:ring-gray-400 focus:outline-none disabled:opacity-50"
					>
						{#if fixingEducation}
							<svg
								class="mr-1 -ml-1 h-3 w-3 animate-spin text-gray-700"
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
							>
								<circle
									class="opacity-25"
									cx="12"
									cy="12"
									r="10"
									stroke="currentColor"
									stroke-width="4"
								></circle>
								<path
									class="opacity-75"
									fill="currentColor"
									d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
								></path>
							</svg>
							Fixing...
						{:else}
							Fix Education Only
						{/if}
					</button>
				</form>
			</div>

			<div class="rounded border bg-white p-4">
				<h3 class="mb-2 font-semibold">Fix Projects Data</h3>
				<p class="mb-2 text-sm text-gray-600">
					This will update project records to ensure both title and name fields are synchronized.
				</p>
				<form method="POST" action="?/fixProjects" use:enhance={submitFixProjects}>
					<button
						type="submit"
						disabled={fixingAll || fixingEducation || fixingProjects}
						class="mt-2 flex w-full items-center justify-center rounded bg-gray-200 px-3 py-1.5 text-sm font-medium text-gray-800 hover:bg-gray-300 focus:ring-2 focus:ring-gray-400 focus:outline-none disabled:opacity-50"
					>
						{#if fixingProjects}
							<svg
								class="mr-1 -ml-1 h-3 w-3 animate-spin text-gray-700"
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
							>
								<circle
									class="opacity-25"
									cx="12"
									cy="12"
									r="10"
									stroke="currentColor"
									stroke-width="4"
								></circle>
								<path
									class="opacity-75"
									fill="currentColor"
									d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
								></path>
							</svg>
							Fixing...
						{:else}
							Fix Projects Only
						{/if}
					</button>
				</form>
			</div>
		</div>
	{/if}

	<div class="mt-6 flex gap-4">
		<a href="/education" class="text-indigo-600 hover:text-indigo-800 hover:underline">
			← Back to Education
		</a>
		<a href="/projects" class="text-indigo-600 hover:text-indigo-800 hover:underline">
			← Back to Projects
		</a>
	</div>
</div>
