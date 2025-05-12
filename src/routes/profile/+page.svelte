<script lang="ts">
	import { onMount, onDestroy } from 'svelte';
	import { goto } from '$app/navigation';
	import { browser } from '$app/environment';
	import { session, updateProfile } from '$lib/stores/authStore';
	import { supabase } from '$lib/supabase';
	import { uploadFile, deleteFile, getPathFromUrl } from '$lib/fileUpload';
	import SectionNavigation from '$lib/components/SectionNavigation.svelte';
	import { updateSectionStatus } from '$lib/cv-sections';

	let { data, form } = $props();
	let fullName = $state(data.profile?.full_name ?? '');
	let email = $state(data.profile?.email ?? '');
	let phone = $state(data.profile?.phone ?? '');
	let location = $state(data.profile?.location ?? '');
	let photoUrl = $state(data.profile?.photo_url ?? '');
	let error = $state<string | null>(null); // Don't initialize with server error
	let success = $state<string | null>(null);
	let loading = $state(false);
	let uploadingPhoto = $state(false);
	let photoError = $state<string | null>(null);
	let initialCheckDone = $state(false);
	let loadingProfile = $state(true); // Add loading state for profile
	let photoInputEl = $state<HTMLInputElement | null>(null);

	// File validation constants
	const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
	const ALLOWED_FILE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

	// Storage bucket name
	const PROFILE_PHOTOS_BUCKET = 'profile_photos';

	// Fix updateProfile return type
	interface ProfileUpdateResult {
		success: boolean;
		profile?: any;
		error?: string;
	}

	// Check authentication on mount and try to initialize data
	onMount(async () => {
		console.log('Profile page mounted');
		console.log('Store session:', $session ? `User ID: ${$session.user.id}` : 'Missing');
		console.log('Data session:', data.session ? `User ID: ${data.session.user.id}` : 'Missing');

		// Check for authentication
		if (!data.session && !$session) {
			console.log('No session found on profile page mount');
			error = 'Not authenticated. Please login first.';
			loadingProfile = false;

			// If in browser, redirect to home
			if (browser) {
				setTimeout(() => {
					goto('/');
				}, 2000);
			}
		} else {
			// We have a session, try to load profile if it wasn't loaded from server
			if ((!data.profile || data.error) && ($session || data.session)) {
				try {
					console.log('Trying to load profile from client');
					const userId = $session?.user.id || data.session?.user.id;

					if (userId) {
						console.log('Fetching profile for user ID:', userId);

						// Create a proper query with explicit headers
						const { data: profileData, error: profileError } = await supabase
							.from('profiles')
							.select('*')
							.eq('id', userId)
							.maybeSingle(); // Use maybeSingle instead of single to avoid 406 errors

						console.log(
							'Profile fetch result:',
							profileData || 'No data',
							profileError || 'No error'
						);

						if (profileError) {
							// Check if it's just a "no rows" error, which is expected for new users
							if (profileError.code === 'PGRST116') {
								console.log('No profile found for user - this is normal for new users');

								// Set email from session if available
								if ($session?.user?.email) {
									email = $session.user.email;
								}
							} else {
								console.error('Error loading profile from client:', profileError);
								error = 'Error loading profile. Please try again.';
							}
						} else if (profileData) {
							console.log('Profile loaded from client:', profileData);
							// Update form fields with profile data
							fullName = profileData.full_name || '';
							email = profileData.email || '';
							phone = profileData.phone || '';
							location = profileData.location || '';
							photoUrl = profileData.photo_url || '';
							// Clear any error
							error = null;
						} else {
							console.log('No profile data found, but no error either');
							// Set email from session if available
							if ($session?.user?.email) {
								email = $session.user.email;
							}
						}
					}
				} catch (err) {
					console.error('Error in client-side profile load:', err);
					error = 'Error loading profile. Please refresh the page.';
				} finally {
					loadingProfile = false;
				}
			} else if (data.profile) {
				// Profile was loaded from server
				// Update local state with profile data
				photoUrl = data.profile.photo_url || '';
				loadingProfile = false;
				error = null; // Clear any errors from the server
			}
		}

		initialCheckDone = true;
	});

	// Handle file upload
	async function handlePhotoUpload(e: Event) {
		if (!$session) {
			photoError = 'You need to be logged in to upload a photo.';
			return;
		}

		const input = e.target as HTMLInputElement;
		const file = input.files?.[0];

		if (!file) {
			photoError = 'No file selected.';
			return;
		}

		// Validate file size
		if (file.size > MAX_FILE_SIZE) {
			photoError = `File is too large. Maximum size is ${MAX_FILE_SIZE / (1024 * 1024)}MB.`;
			// Reset input
			if (photoInputEl) photoInputEl.value = '';
			return;
		}

		// Validate file type
		if (!ALLOWED_FILE_TYPES.includes(file.type)) {
			photoError = 'Only JPEG, PNG, and WebP images are allowed.';
			// Reset input
			if (photoInputEl) photoInputEl.value = '';
			return;
		}

		// Clear any errors
		photoError = null;
		uploadingPhoto = true;

		try {
			// If there's already a photo, delete it
			if (photoUrl) {
				const path = getPathFromUrl(photoUrl, PROFILE_PHOTOS_BUCKET);
				if (path) {
					await deleteFile(PROFILE_PHOTOS_BUCKET, path);
				}
			}

			// Upload the new photo
			const result = await uploadFile($session.user.id, file, PROFILE_PHOTOS_BUCKET);

			if (!result.success) {
				photoError = result.error || 'Failed to upload photo.';
				return;
			}

			// Update the photo URL
			photoUrl = result.url || '';

			// Save the profile with the new photo URL
			const userId = $session.user.id;

			// Prepare profile data
			const profileData = {
				id: userId,
				photo_url: photoUrl
			};

			// Use the updateProfile helper from authStore with proper typing
			const updateResult = (await updateProfile(profileData)) as ProfileUpdateResult;

			if (!updateResult.success) {
				photoError = updateResult.error || 'Failed to update profile with new photo.';
				console.error('Error saving profile photo:', updateResult.error);
			} else {
				success = 'Photo uploaded successfully!';

				// Clear success message after 3 seconds
				setTimeout(() => {
					success = null;
				}, 3000);
			}
		} catch (err) {
			console.error('Error handling photo upload:', err);
			photoError = 'An unexpected error occurred while uploading your photo.';
		} finally {
			uploadingPhoto = false;
			// Reset input
			if (photoInputEl) photoInputEl.value = '';
		}
	}

	// Delete profile photo
	async function deleteProfilePhoto() {
		if (!$session || !photoUrl) {
			return;
		}

		uploadingPhoto = true;
		photoError = null;

		try {
			// Extract path from URL
			const path = getPathFromUrl(photoUrl, PROFILE_PHOTOS_BUCKET);

			if (path) {
				// Delete the file from storage
				const deleteResult = await deleteFile(PROFILE_PHOTOS_BUCKET, path);

				if (!deleteResult.success) {
					photoError = deleteResult.error || 'Failed to delete photo.';
					return;
				}
			}

			// Update profile to remove photo URL
			const profileData = {
				id: $session.user.id,
				photo_url: null
			};

			const updateResult = (await updateProfile(profileData)) as ProfileUpdateResult;

			if (!updateResult.success) {
				photoError = updateResult.error || 'Failed to update profile.';
				return;
			}

			// Clear photo URL
			photoUrl = '';
			success = 'Photo removed successfully!';

			// Clear success message after 3 seconds
			setTimeout(() => {
				success = null;
			}, 3000);
		} catch (err) {
			console.error('Error deleting photo:', err);
			photoError = 'An unexpected error occurred while deleting your photo.';
		} finally {
			uploadingPhoto = false;
		}
	}

	// Subscribe to auth state changes
	$effect(() => {
		if (!initialCheckDone) return;

		// If session changes after initial check, update UI accordingly
		if (!$session) {
			console.log('Session lost during profile page lifecycle');
			error = 'Session lost. Please login again.';
			if (browser) {
				setTimeout(() => {
					goto('/');
				}, 2000);
			}
		} else {
			console.log('Session available during profile page lifecycle');
			// Clear error if it was auth-related
			if (
				error === 'Not authenticated. Please login first.' ||
				error === 'Session lost. Please login again.'
			) {
				error = null;
			}
		}
	});

	async function saveProfile(e: Event) {
		e.preventDefault();
		// Double-check authentication
		if (!$session) {
			error = 'Not authenticated. Please login first.';
			return;
		}

		loading = true;
		error = null;
		success = null;

		try {
			// Use session from store
			const userId = $session.user.id;
			const accessToken = $session.access_token;

			// Ensure we have a user ID and token
			if (!userId || !accessToken) {
				error = 'User ID or token not found. Please log in again.';
				loading = false;
				return;
			}

			// Prepare profile data
			const profileData = {
				id: userId,
				full_name: fullName,
				email,
				phone,
				location,
				photo_url: photoUrl
			};

			console.log('Saving profile data:', profileData);

			// Use the updateProfile helper from authStore with proper typing
			const result = (await updateProfile(profileData)) as ProfileUpdateResult;

			if (!result.success) {
				error = result.error || 'Failed to save profile';
				console.error('Error saving profile:', result.error);
			} else {
				success = 'Profile saved successfully!';
				console.log('Profile saved successfully:', result.profile);

				// Update local state with the returned profile data
				if (result.profile && result.profile.length > 0) {
					const savedProfile = result.profile[0];
					fullName = savedProfile.full_name || fullName;
					email = savedProfile.email || email;
					phone = savedProfile.phone || phone;
					location = savedProfile.location || location;
					photoUrl = savedProfile.photo_url || photoUrl;
				}

				// Update section status to reflect the profile completion
				await updateSectionStatus();
			}
		} catch (err) {
			console.error('Error saving profile:', err);
			error = 'An unexpected error occurred';
		} finally {
			loading = false;
		}
	}
</script>

<div class="mx-auto max-w-xl space-y-6 rounded bg-white p-8 shadow">
	<h2 class="mb-4 text-2xl font-bold">Profile</h2>
	{#if error}
		<div class="rounded bg-red-100 p-4 text-red-700">{error}</div>
	{/if}
	{#if success}
		<div class="rounded bg-green-100 p-4 text-green-700">{success}</div>
	{/if}
	{#if photoError}
		<div class="rounded bg-red-100 p-4 text-red-700">{photoError}</div>
	{/if}

	{#if (!data.session && !$session) || loading}
		<div class="rounded bg-yellow-100 p-4">
			<p class="font-medium">
				{loading ? 'Loading...' : 'You need to be logged in to edit your profile.'}
			</p>
			{#if !loading}
				<button
					onclick={() => goto('/')}
					class="mt-2 rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700"
				>
					Go to Login
				</button>
			{/if}
		</div>
	{:else if loadingProfile}
		<div class="rounded bg-blue-100 p-4">
			<p class="font-medium">Loading your profile...</p>
		</div>
	{:else}
		<!-- Profile Photo Section -->
		<div class="mb-6 flex flex-col items-center">
			<div class="mb-4 h-32 w-32 overflow-hidden rounded-full bg-gray-200">
				{#if photoUrl}
					<img src={photoUrl} alt="Profile" class="h-full w-full object-cover" />
				{:else}
					<div class="flex h-full w-full items-center justify-center bg-gray-200 text-gray-500">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							class="h-16 w-16"
							fill="none"
							viewBox="0 0 24 24"
							stroke="currentColor"
						>
							<path
								stroke-linecap="round"
								stroke-linejoin="round"
								stroke-width="2"
								d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
							/>
						</svg>
					</div>
				{/if}
			</div>

			<div class="flex gap-2">
				<label
					class="cursor-pointer rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
				>
					{uploadingPhoto ? 'Uploading...' : 'Upload Photo'}
					<input
						type="file"
						accept="image/jpeg,image/png,image/webp"
						class="hidden"
						bind:this={photoInputEl}
						onchange={handlePhotoUpload}
						disabled={uploadingPhoto}
					/>
				</label>

				{#if photoUrl}
					<button
						type="button"
						onclick={deleteProfilePhoto}
						disabled={uploadingPhoto}
						class="rounded-md bg-red-100 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-200 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none"
					>
						{uploadingPhoto ? 'Deleting...' : 'Remove'}
					</button>
				{/if}
			</div>

			<p class="mt-2 text-xs text-gray-500">Max 2MB. JPEG, PNG, or WebP.</p>
		</div>

		<form onsubmit={saveProfile} class="space-y-6">
			<div>
				<label class="mb-1 block text-sm font-medium text-gray-700" for="fullName">Full Name</label>
				<input
					id="fullName"
					name="fullName"
					type="text"
					bind:value={fullName}
					class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
					required
				/>
			</div>
			<div>
				<label class="mb-1 block text-sm font-medium text-gray-700" for="email">Email</label>
				<input
					id="email"
					name="email"
					type="email"
					bind:value={email}
					class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
					required
				/>
			</div>
			<div>
				<label class="mb-1 block text-sm font-medium text-gray-700" for="phone">Phone</label>
				<input
					id="phone"
					name="phone"
					type="tel"
					bind:value={phone}
					class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
				/>
			</div>
			<div>
				<label class="mb-1 block text-sm font-medium text-gray-700" for="location">Location</label>
				<input
					id="location"
					name="location"
					type="text"
					bind:value={location}
					class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
				/>
			</div>
			<div>
				<button
					type="submit"
					disabled={loading}
					class="w-full rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
				>
					{loading ? 'Saving...' : 'Save Profile'}
				</button>
			</div>
		</form>
	{/if}

	<SectionNavigation />
</div>
