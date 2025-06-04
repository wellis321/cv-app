<script lang="ts">
	import { onMount } from 'svelte';
	import { page } from '$app/stores';
	import { browser } from '$app/environment';
	import { trackPageView } from '$lib/analytics/analyticsService';

	// Track visits when the route changes
	$: if (browser && $page.url.pathname) {
		trackPageView();
	}

	// Also track on initial page load
	onMount(() => {
		if (browser) {
			trackPageView();
		}
	});
</script>

<!-- This component doesn't render anything, it just tracks analytics -->
