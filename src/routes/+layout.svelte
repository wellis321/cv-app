<script lang="ts">
	import '../app.css';
	import AuthForm from '$lib/auth-form.svelte';
	import { goto } from '$app/navigation';
	import { onMount } from 'svelte';
	import { session, initializeSession, setupAuthListener, logout } from '$lib/stores/authStore';
	import { browser } from '$app/environment';

	// Setup auth on mount
	onMount(() => {
		console.log('Layout mounted, initializing session...');

		if (browser) {
			// Force refresh page if coming from login/signup
			const fromAuth = sessionStorage.getItem('just_authenticated');
			if (fromAuth) {
				console.log('Detected authentication redirect, clearing flag');
				sessionStorage.removeItem('just_authenticated');
			}

			// Initialize session
			initializeSession()
				.then(() => {
					console.log('Session initialized in layout:', $session ? 'Present' : 'None');
				})
				.catch((err) => {
					console.error('Error initializing session in layout:', err);
				});

			// Set up auth listener and return cleanup function
			const unsubscribe = setupAuthListener();
			return unsubscribe;
		}
	});

	// Handle sign out
	async function signOut() {
		console.log('Signing out...');
		try {
			await logout();
			// Force page reload after logout to clear any cached state
			if (browser) {
				window.location.href = '/';
			} else {
				goto('/');
			}
		} catch (err) {
			console.error('Error during sign out:', err);
		}
	}
</script>

<div class="min-h-screen bg-gray-50">
	<header class="bg-white shadow">
		<nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
			<div class="flex h-16 justify-between">
				<div class="flex">
					<div class="flex flex-shrink-0 items-center">
						<a href="/" class="text-xl font-bold text-gray-900">CV Builder</a>
					</div>
				</div>
				<div class="flex items-center gap-4">
					{#if $session}
						<button onclick={signOut} class="text-gray-600 hover:text-gray-900">Sign Out</button>
						<a href="/profile" class="text-gray-600 hover:text-gray-900">Profile</a>
					{/if}
				</div>
			</div>
		</nav>
	</header>

	<main class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
		{#if !$session}
			<AuthForm />
		{:else}
			<slot></slot>
		{/if}
	</main>
</div>
