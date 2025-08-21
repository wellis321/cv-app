<script lang="ts">
	import { browser } from '$app/environment';
	import { goto } from '$app/navigation';
	import { supabase } from '$lib/supabase';
	import { page } from '$app/stores';
	import { onMount } from 'svelte';
	// @ts-ignore - The Temporal polyfill doesn't have proper TypeScript definitions
	import { Temporal } from '@js-temporal/polyfill';
	import { session as authSession } from '$lib/stores/authStore';
	import BreadcrumbNavigation from '$lib/components/BreadcrumbNavigation.svelte';
	import { uploadFile, deleteFile, getPathFromUrl } from '$lib/fileUpload';
	import {
		PROJECT_IMAGES_BUCKET,
		DEFAULT_PROJECT_IMAGE,
		getProxiedPhotoUrl
	} from '$lib/photoUtils';
	import { formatDescription } from '$lib/utils/textFormatting';

	interface PageData {
		projects: Project[];
		error?: string;
		form?: {
			error: string;
		};
	}

	const { data } = $props<{ data: PageData }>();

	// Interface definition for the Project type
	interface Project {
		id: string;
		profile_id: string | null;
		title?: string | null;
		name?: string | null; // For backward compatibility if the field was renamed
		description?: string | null;
		start_date?: string | null;
		end_date?: string | null;
		url?: string | null;
		image_url?: string | null;
		created_at?: string;
		updated_at?: string;
	}

	// Form state
	let title = $state('');
	let description = $state('');
	let startDate = $state('');
	let endDate = $state('');
	let url = $state('');
	let imageUrl = $state<string | null>(null);
	let previewImageUrl = $state<string | null>(null);

	// File upload state
	let imageInputEl = $state<HTMLInputElement | null>(null);
	let uploadingImage = $state(false);
	let imageError = $state<string | null>(null);

	// File validation constants
	const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
	const ALLOWED_FILE_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

	// UI state
	let projects = $state<Project[]>(data.projects || []);
	let error = $state<string | undefined>(data.error);
	let success = $state<string>('');
	let loading = $state(false);
	let loadingProjects = $state(false);
	let showAddForm = $state(false);
	let isEditing = $state(false);
	let editingProject = $state<Project | null>(null);
	let deleteConfirmId = $state<string | null>(null);

	// Session from store
	const session = $authSession;

	console.log('Projects page - session:', session ? 'Present' : 'None');
	console.log('Projects page - data:', data);
	console.log('Projects page - projects:', projects);

	// Format dates for display
	function formatDate(dateString: string | null | undefined): string {
		if (!dateString) return '';
		try {
			const date = new Date(dateString);
			return date.toLocaleDateString();
		} catch (err) {
			console.error('Error formatting date:', err);
			return dateString || '';
		}
	}

	// Toggle add form visibility
	function toggleAddForm() {
		showAddForm = !showAddForm;
		if (!showAddForm) {
			// Reset form when canceling
			resetForm();
		}
	}

	// Edit a project
	function editProject(project: Project) {
		isEditing = true;
		editingProject = project;
		title = project.title || '';
		description = project.description || '';
		startDate = project.start_date || '';
		endDate = project.end_date || '';
		url = project.url || '';
		imageUrl = project.image_url || null;
		previewImageUrl = project.image_url
			? getProxiedPhotoUrl(project.image_url, PROJECT_IMAGES_BUCKET)
			: null;
		showAddForm = true;

		// Scroll to form
		setTimeout(() => {
			document.getElementById('projectForm')?.scrollIntoView({ behavior: 'smooth' });
		}, 100);
	}

	// Cancel editing
	function cancelEdit() {
		isEditing = false;
		editingProject = null;
		resetForm();
	}

	// Reset form fields
	function resetForm() {
		title = '';
		description = '';
		startDate = '';
		endDate = '';
		url = '';
		imageUrl = null;
		previewImageUrl = null;
		if (imageInputEl) imageInputEl.value = '';
		imageError = null;
		showAddForm = false;
	}

	async function handleSubmit(event: Event): Promise<void> {
		event.preventDefault();

		if (!session) {
			error = 'You need to be logged in to save a project.';
			return;
		}

		// Basic validation
		if (!title.trim()) {
			error = 'Project title is required.';
			return;
		}

		loading = true;
		error = undefined;
		success = '';

		try {
			// Ensure we have a valid auth token by checking session
			const { data: sessionData } = await supabase.auth.getSession();

			if (!sessionData.session) {
				// Re-authenticate if no session
				error = 'Your session has expired. Please refresh the page and try again.';
				loading = false;
				return;
			}

			let result;

			if (isEditing && editingProject) {
				// Update existing project
				result = await supabase
					.from('projects')
					.update({
						title,
						description,
						start_date: startDate || null,
						end_date: endDate || null,
						url: url || null,
						image_url: imageUrl || null
					})
					.eq('id', editingProject.id)
					.select();
			} else {
				// Insert new project
				result = await supabase
					.from('projects')
					.insert({
						profile_id: sessionData.session.user.id,
						title,
						description,
						start_date: startDate || null,
						end_date: endDate || null,
						url: url || null,
						image_url: imageUrl || null
					})
					.select();
			}

			const { data: projectData, error: submitError } = result;

			if (submitError) {
				console.error('Error submitting project:', submitError);
				error = submitError.message || 'Failed to save project';
				success = '';
			} else {
				console.log('Project saved successfully:', projectData);

				// Force reload of projects
				await loadProjectsFromClient();
				success = isEditing ? 'Project updated successfully!' : 'Project added successfully!';
				resetForm();
				isEditing = false;
				editingProject = null;
			}
		} catch (err) {
			console.error('Unexpected error submitting project:', err);
			error = 'An unexpected error occurred. Please try again.';
		} finally {
			loading = false;
		}
	}

	// Handle deleting a project
	async function deleteProject(id: string) {
		if (!session) {
			error = 'You need to be logged in to delete a project.';
			return;
		}

		if (id !== deleteConfirmId) {
			// First click, show confirmation
			deleteConfirmId = id;
			return;
		}

		// Second click, actually delete
		deleteConfirmId = null;
		loading = true;
		error = undefined;

		try {
			const { data: sessionData } = await supabase.auth.getSession();

			if (!sessionData.session) {
				error = 'Your session has expired. Please refresh the page and try again.';
				loading = false;
				return;
			}

			// Find the project to get its image URL for cleanup
			const projectToDelete = projects.find((p) => p.id === id);

			// Delete project from database
			const { error: deleteError } = await supabase.from('projects').delete().eq('id', id);

			if (deleteError) {
				console.error('Error deleting project:', deleteError);
				error = deleteError.message || 'Failed to delete project';
				return;
			}

			// If project had an image, delete it from storage
			if (projectToDelete?.image_url) {
				const path = getPathFromUrl(projectToDelete.image_url, PROJECT_IMAGES_BUCKET);
				if (path) {
					await deleteFile(PROJECT_IMAGES_BUCKET, path);
				}
			}

			// Update local state by removing the deleted project
			projects = projects.filter((project) => project.id !== id);
			success = 'Project deleted successfully!';

			// Clear success message after 3 seconds
			setTimeout(() => {
				success = '';
			}, 3000);
		} catch (err) {
			console.error('Unexpected error deleting project:', err);
			error = 'An unexpected error occurred while deleting the project.';
		} finally {
			loading = false;
		}
	}

	// Handle image upload
	async function handleImageUpload(event: Event): Promise<void> {
		// Clear previous errors
		imageError = null;

		const input = event.target as HTMLInputElement;
		if (!input.files || input.files.length === 0) {
			return;
		}

		const file = input.files[0];

		// Validate file size
		if (file.size > MAX_FILE_SIZE) {
			imageError = `File is too large. Maximum size is ${MAX_FILE_SIZE / (1024 * 1024)}MB.`;
			input.value = '';
			return;
		}

		// Validate file type
		if (!ALLOWED_FILE_TYPES.includes(file.type)) {
			imageError = 'Only JPEG, PNG, WebP, and GIF images are allowed.';
			input.value = '';
			return;
		}

		// Show preview
		previewImageUrl = URL.createObjectURL(file);

		uploadingImage = true;

		try {
			if (!session) {
				imageError = 'You need to be logged in to upload an image.';
				uploadingImage = false;
				return;
			}

			// If there's already an image and we're editing, delete it
			if (imageUrl && isEditing) {
				const path = getPathFromUrl(imageUrl, PROJECT_IMAGES_BUCKET);
				if (path) {
					await deleteFile(PROJECT_IMAGES_BUCKET, path);
				}
			}

			// Upload the new image
			const result = await uploadFile(session.user.id, file, PROJECT_IMAGES_BUCKET);

			if (!result.success) {
				imageError = result.error || 'Failed to upload image';
				return;
			}

			// Save the image URL
			imageUrl = result.url || null;
			console.log('Image uploaded successfully:', imageUrl);
		} catch (err) {
			console.error('Error uploading image:', err);
			imageError = 'Failed to upload image. Please try again.';
		} finally {
			uploadingImage = false;
		}
	}

	// Remove the image
	function removeImage(): void {
		previewImageUrl = null;
		imageUrl = null;
		if (imageInputEl) imageInputEl.value = '';
	}

	// Load projects from the client side (used for reload)
	async function loadProjectsFromClient() {
		loadingProjects = true;
		error = undefined;

		try {
			// Verify session first
			const { data: sessionData } = await supabase.auth.getSession();

			if (!sessionData.session) {
				console.log('No valid session for client-side load');
				loadingProjects = false;
				return;
			}

			console.log('Loading projects for user:', sessionData.session.user.id);

			// Fetch projects
			const { data: projectsData, error: projectsError } = await supabase
				.from('projects')
				.select('*')
				.eq('profile_id', sessionData.session.user.id)
				.order('start_date', { ascending: false });

			if (projectsError) {
				console.error('Error fetching projects from client:', projectsError);
				error = projectsError.message;
				return;
			}

			console.log('Client-side load successful:', projectsData?.length, 'projects');
			projects = projectsData || [];
		} catch (err) {
			console.error('Unexpected error loading projects from client:', err);
			error = 'Failed to load projects. Please try refreshing the page.';
		} finally {
			loadingProjects = false;
		}
	}

	// Check for success message in URL params and data loading
	onMount(async () => {
		if (browser) {
			// Check URL params for success message
			if ($page.url.searchParams.has('success')) {
				const successType = $page.url.searchParams.get('success');
				if (successType === 'create') {
					success = 'Project added successfully!';
				} else if (successType === 'update') {
					success = 'Project updated successfully!';
				} else if (successType === 'delete') {
					success = 'Project deleted successfully!';
				}

				// Clear URL params
				const url = new URL(window.location.href);
				url.searchParams.delete('success');
				history.replaceState({}, document.title, url.toString());

				// Clear success message after 3 seconds
				setTimeout(() => {
					success = '';
				}, 3000);
			}

			// If data was loaded properly on the server, we'll have projects
			// Otherwise, try to load them directly from client
			if (session && (!projects || projects.length === 0)) {
				console.log('No projects loaded from server, trying client-side fetch');
				await loadProjectsFromClient();
			}
		}
	});
</script>

<div class="mx-auto max-w-4xl space-y-6">
	<BreadcrumbNavigation />

	<h1 class="text-2xl font-bold">Projects</h1>
	<p class="text-gray-700">
		Add key projects you've worked on to showcase your achievements and skills.
	</p>

	<div class="mx-auto max-w-xl">
		<div class="mb-4 flex items-center justify-between">
			<h2 class="text-2xl font-bold">Your Projects</h2>
			<button
				onclick={toggleAddForm}
				class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
			>
				{showAddForm ? 'Cancel' : 'Add Project'}
			</button>
		</div>

		{#if error}
			<div class="mb-4 rounded bg-red-100 p-4 text-red-700">{error}</div>
		{/if}

		{#if success}
			<div class="mb-4 rounded bg-green-100 p-4 text-green-700">{success}</div>
		{/if}

		<!-- Add/Edit form -->
		{#if showAddForm && session}
			<div id="projectForm" class="mb-8 rounded bg-white p-6 shadow">
				<h3 class="mb-4 text-xl font-semibold">
					{isEditing ? 'Edit Project' : 'Add New Project'}
				</h3>

				<form
					onsubmit={handleSubmit}
					method="POST"
					action={isEditing ? '?/update' : '?/create'}
					class="space-y-4"
				>
					{#if data.form?.error}
						<div class="mb-4 rounded bg-red-100 p-4 text-red-700">{data.form.error}</div>
					{/if}

					{#if isEditing && editingProject}
						<input type="hidden" name="id" value={editingProject.id} />
					{/if}

					<div>
						<label class="mb-1 block text-sm font-medium text-gray-700" for="title">Title</label>
						<input
							id="title"
							name="title"
							type="text"
							bind:value={title}
							class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
							required
						/>
					</div>
					<div>
						<label class="mb-1 block text-sm font-medium text-gray-700" for="description"
							>Description</label
						>
						<textarea
							id="description"
							name="description"
							bind:value={description}
							class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
						></textarea>
					</div>

					<!-- Project Image Upload -->
					<div>
						<label class="mb-1 block text-sm font-medium text-gray-700"
							>Project Image <span class="text-xs text-gray-500">(Optional)</span></label
						>

						<div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-center">
							<!-- Image Preview -->
							<div
								class="relative h-36 w-48 overflow-hidden rounded border border-gray-300 bg-gray-50"
							>
								{#if previewImageUrl}
									<img
										src={previewImageUrl}
										alt="Project preview"
										class="h-full w-full object-cover"
										onerror={() => {
											console.error('Error loading project image preview');
											previewImageUrl = DEFAULT_PROJECT_IMAGE;
										}}
									/>
									<button
										type="button"
										onclick={removeImage}
										class="absolute top-1 right-1 rounded-full bg-red-500 p-1 text-white hover:bg-red-600 focus:ring-2 focus:ring-red-400 focus:outline-none"
										title="Remove image"
									>
										<svg
											xmlns="http://www.w3.org/2000/svg"
											class="h-4 w-4"
											viewBox="0 0 20 20"
											fill="currentColor"
										>
											<path
												fill-rule="evenodd"
												d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
												clip-rule="evenodd"
											/>
										</svg>
									</button>
								{:else}
									<div class="flex h-full w-full items-center justify-center">
										<svg
											xmlns="http://www.w3.org/2000/svg"
											class="h-12 w-12 text-gray-300"
											fill="none"
											viewBox="0 0 24 24"
											stroke="currentColor"
										>
											<path
												stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
											/>
										</svg>
									</div>
								{/if}
							</div>

							<!-- Upload Controls -->
							<div class="flex flex-col gap-2">
								{#if uploadingImage}
									<div class="flex items-center gap-2 text-sm text-gray-600">
										<div
											class="h-4 w-4 animate-spin rounded-full border-2 border-gray-300 border-t-indigo-600"
										></div>
										Uploading image...
									</div>
								{:else}
									<label
										for="project-image"
										class="inline-flex cursor-pointer items-center rounded-md bg-indigo-500 px-3 py-2 text-sm text-white hover:bg-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
									>
										<svg
											xmlns="http://www.w3.org/2000/svg"
											class="mr-2 h-4 w-4"
											fill="none"
											viewBox="0 0 24 24"
											stroke="currentColor"
										>
											<path
												stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"
											/>
										</svg>
										Upload Image
									</label>
									<input
										type="file"
										id="project-image"
										accept="image/jpeg,image/png,image/webp,image/gif"
										class="hidden"
										bind:this={imageInputEl}
										onchange={handleImageUpload}
									/>
									<p class="text-xs text-gray-500">Max size: 5MB. Formats: JPEG, PNG, WebP, GIF</p>
								{/if}

								{#if imageError}
									<p class="text-sm text-red-600">{imageError}</p>
								{/if}
							</div>
						</div>
					</div>

					<div class="flex gap-4">
						<div class="flex-1">
							<label class="mb-1 block text-sm font-medium text-gray-700" for="startDate"
								>Start Date</label
							>
							<input
								id="startDate"
								name="startDate"
								type="date"
								bind:value={startDate}
								class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
							/>
						</div>
						<div class="flex-1">
							<label class="mb-1 block text-sm font-medium text-gray-700" for="endDate"
								>End Date <span class="text-xs text-gray-500">(Leave blank if ongoing)</span></label
							>
							<input
								id="endDate"
								name="endDate"
								type="date"
								bind:value={endDate}
								class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
							/>
						</div>
					</div>
					<div>
						<label class="mb-1 block text-sm font-medium text-gray-700" for="url">Project URL</label
						>
						<input
							id="url"
							name="url"
							type="url"
							bind:value={url}
							class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
						/>
					</div>
					<div class="flex gap-2">
						<button
							type="submit"
							disabled={loading}
							class="flex-1 rounded bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
						>
							{loading ? 'Saving...' : isEditing ? 'Update Project' : 'Save Project'}
						</button>
						{#if isEditing}
							<button
								type="button"
								onclick={cancelEdit}
								class="rounded bg-gray-300 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-400 focus:ring-2 focus:ring-gray-500 focus:outline-none"
							>
								Cancel
							</button>
						{/if}
					</div>
				</form>
			</div>
		{/if}

		<!-- Projects list -->
		<div class="space-y-4">
			{#if loadingProjects}
				<div class="flex items-center justify-center py-6">
					<div
						class="h-6 w-6 animate-spin rounded-full border-2 border-gray-300 border-t-indigo-600"
					></div>
					<span class="ml-2 text-gray-600">Loading projects...</span>
				</div>
			{:else if projects.length === 0}
				<div class="rounded bg-gray-50 p-8 text-center text-gray-500">
					<p>You don't have any projects yet.</p>
					{#if !showAddForm}
						<button
							onclick={() => (showAddForm = true)}
							class="mt-4 rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
						>
							Add Your First Project
						</button>
					{/if}
				</div>
			{:else}
				{#each projects as project (project.id)}
					<div class="relative rounded-lg border border-gray-200 bg-white shadow-sm">
						<div class="flex flex-col overflow-hidden">
							<!-- Project Title and Dates -->
							<div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
								<div class="flex flex-wrap items-start justify-between gap-2">
									<h3 class="font-semibold text-gray-800">{project.title}</h3>
									{#if project.start_date}
										<p class="text-sm text-gray-500">
											{formatDate(project.start_date)} - {project.end_date
												? formatDate(project.end_date)
												: 'Present'}
										</p>
									{/if}
								</div>
							</div>

							<!-- Project Image - Now above the description -->
							{#if project.image_url}
								<div class="w-full overflow-hidden bg-white">
									<img
										src={getProxiedPhotoUrl(project.image_url, PROJECT_IMAGES_BUCKET)}
										alt={project.title || 'Project'}
										class="mx-auto h-auto max-h-96 w-full bg-white object-contain py-4"
										onerror={(e) => {
											const img = e.target as HTMLImageElement;
											img.src = DEFAULT_PROJECT_IMAGE;
										}}
									/>
								</div>
							{/if}

							<!-- Project Details -->
							<div class="p-6">
								{#if project.description}
									{#each formatDescription(project.description) as paragraph}
										<p class="text-gray-600">{paragraph}</p>
									{/each}
								{/if}

								{#if project.url}
									<div class="mt-4">
										<a
											href={project.url}
											target="_blank"
											rel="noopener noreferrer"
											class="inline-flex items-center text-indigo-600 hover:text-indigo-800"
										>
											<span>View Project</span>
											<svg
												xmlns="http://www.w3.org/2000/svg"
												class="ml-1 h-4 w-4"
												viewBox="0 0 20 20"
												fill="currentColor"
											>
												<path
													d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"
												/>
												<path
													d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"
												/>
											</svg>
										</a>
									</div>
								{/if}

								<div class="mt-4 flex space-x-2">
									<button
										onclick={() => editProject(project)}
										class="rounded bg-indigo-100 px-3 py-1 text-sm text-indigo-700 hover:bg-indigo-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
									>
										Edit
									</button>
									<button
										onclick={() => deleteProject(project.id)}
										class={`rounded px-3 py-1 text-sm ${
											deleteConfirmId === project.id
												? 'bg-red-500 text-white hover:bg-red-600'
												: 'bg-red-100 text-red-700 hover:bg-red-200'
										} focus:ring-2 focus:ring-red-500 focus:outline-none`}
									>
										{deleteConfirmId === project.id ? 'Confirm Delete' : 'Delete'}
									</button>
								</div>
							</div>
						</div>
					</div>
				{/each}
			{/if}
		</div>
	</div>
</div>
