<script lang="ts">
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';
	import {
		getDashboardStats,
		getPopularPages,
		getActiveUsers,
		type DailyStats,
		type PageViewStats
	} from '$lib/analytics/analyticsService';
	import { session } from '$lib/stores/authStore';
	import { goto } from '$app/navigation';
	import { isAdminUser } from '$lib/adminConfig';

	// Data variables
	let dashboardStats = $state<DailyStats[]>([]);
	let popularPages = $state<PageViewStats[]>([]);
	let activeUsers = $state<number>(0);
	let loading = $state(true);
	let dateRange = $state<number>(30);
	let error = $state<string | null>(null);

	// Check if user is admin
	let isAdmin = $state(false);

	// Automatically update active users count every minute
	let activeUsersInterval: ReturnType<typeof setInterval> | undefined;

	onMount(() => {
		if (!browser) return;

		// Check if user is authorized to view analytics
		if (!$session?.user) {
			goto('/');
			return;
		}

		// Use the centralized admin check
		isAdmin = isAdminUser($session.user.email);

		if (!isAdmin) {
			error = 'You do not have permission to view this page.';
			loading = false;
			return;
		}

		// Define an async function to load data
		async function initializeData() {
			try {
				// Load initial data
				await loadData();
				loading = false;

				// Start polling for active users
				activeUsersInterval = setInterval(async () => {
					activeUsers = await getActiveUsers();
				}, 60000); // Update every minute
			} catch (err) {
				console.error('Error loading analytics data:', err);
				error = 'An error occurred while loading analytics data.';
				loading = false;
			}
		}

		// Call the function but don't await it in onMount
		initializeData();

		// Return a cleanup function
		return () => {
			if (activeUsersInterval) {
				clearInterval(activeUsersInterval);
			}
		};
	});

	// Load data based on current date range
	async function loadData() {
		const [statsData, pagesData, usersCount] = await Promise.all([
			getDashboardStats(dateRange),
			getPopularPages(10, dateRange),
			getActiveUsers()
		]);

		dashboardStats = statsData;
		popularPages = pagesData;
		activeUsers = usersCount;
	}

	// Format date for display
	function formatDate(dateStr: string): string {
		const date = new Date(dateStr);
		return new Intl.DateTimeFormat('en-GB', {
			day: 'numeric',
			month: 'short',
			year: 'numeric'
		}).format(date);
	}

	// Update data when date range changes
	$effect(() => {
		if (!loading && dateRange) {
			loadData();
		}
	});
</script>

<svelte:head>
	<title>Analytics Dashboard</title>
	<meta name="description" content="Website visitor analytics for CV Builder" />
</svelte:head>

<div class="container mx-auto px-4 py-8">
	<div class="mb-6">
		<h1 class="text-3xl font-bold">Analytics Dashboard</h1>
		<p class="mt-2 text-gray-600">View site visitor statistics and usage data</p>
	</div>

	{#if error}
		<div class="mb-6 rounded-md bg-red-50 p-4 text-red-800">
			<p>{error}</p>
		</div>
	{:else if loading}
		<div class="flex h-64 items-center justify-center">
			<div
				class="h-12 w-12 animate-spin rounded-full border-t-2 border-b-2 border-indigo-500"
			></div>
		</div>
	{:else}
		<!-- Date range selector -->
		<div class="mb-6 flex items-center space-x-4">
			<label for="date-range" class="font-medium text-gray-700">Time period:</label>
			<select
				id="date-range"
				bind:value={dateRange}
				class="rounded-md border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
			>
				<option value={7}>Last 7 days</option>
				<option value={30}>Last 30 days</option>
				<option value={90}>Last 90 days</option>
			</select>
		</div>

		<!-- Summary stats cards -->
		<div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
			<!-- Total page views -->
			<div class="rounded-lg bg-white p-6 shadow">
				<h3 class="text-sm font-medium text-gray-500 uppercase">Total Page Views</h3>
				<p class="mt-2 text-3xl font-bold text-gray-900">
					{dashboardStats.reduce((sum, day) => sum + day.totalPageviews, 0).toLocaleString()}
				</p>
			</div>

			<!-- Unique visitors -->
			<div class="rounded-lg bg-white p-6 shadow">
				<h3 class="text-sm font-medium text-gray-500 uppercase">Unique Visitors</h3>
				<p class="mt-2 text-3xl font-bold text-gray-900">
					{dashboardStats.reduce((sum, day) => sum + day.uniqueUsers, 0).toLocaleString()}
				</p>
			</div>

			<!-- Average pages per session -->
			<div class="rounded-lg bg-white p-6 shadow">
				<h3 class="text-sm font-medium text-gray-500 uppercase">Pages per Session</h3>
				{#if dashboardStats.length > 0}
					{@const totalSessions = dashboardStats.reduce((sum, day) => sum + day.uniqueSessions, 0)}
					{@const totalViews = dashboardStats.reduce((sum, day) => sum + day.totalPageviews, 0)}
					{@const pagesPerSession =
						totalSessions > 0 ? (totalViews / totalSessions).toFixed(1) : '0'}
					<p class="mt-2 text-3xl font-bold text-gray-900">{pagesPerSession}</p>
				{:else}
					<p class="mt-2 text-3xl font-bold text-gray-900">0</p>
				{/if}
			</div>

			<!-- Active users -->
			<div class="rounded-lg bg-white p-6 shadow">
				<h3 class="text-sm font-medium text-gray-500 uppercase">Currently Active</h3>
				<p class="mt-2 text-3xl font-bold text-gray-900">{activeUsers}</p>
				<p class="mt-1 text-xs text-gray-500">Users active in the last 5 minutes</p>
			</div>
		</div>

		<!-- Daily traffic chart -->
		<div class="mb-8 rounded-lg bg-white p-6 shadow">
			<h2 class="mb-4 text-lg font-medium text-gray-900">Daily Traffic</h2>
			<div class="h-64 overflow-x-auto">
				<div class="min-w-full">
					{#if dashboardStats.length > 0}
						<div class="flex h-52 items-end space-x-2">
							{#each [...dashboardStats].reverse() as day}
								{@const maxViews = Math.max(...dashboardStats.map((d) => d.totalPageviews))}
								{@const barHeight = maxViews > 0 ? (day.totalPageviews / maxViews) * 100 : 0}
								<div class="group flex flex-col items-center">
									<div class="relative">
										<div
											class="w-12 rounded-t bg-indigo-500 transition-all hover:bg-indigo-600"
											style="height: {barHeight}%"
										></div>
										<!-- Tooltip -->
										<div
											class="absolute bottom-full left-1/2 mb-2 hidden -translate-x-1/2 transform rounded bg-gray-800 px-2 py-1 text-xs text-white group-hover:block"
										>
											<p class="whitespace-nowrap">{day.totalPageviews} views</p>
											<p class="whitespace-nowrap">{day.uniqueUsers} visitors</p>
										</div>
									</div>
									<div class="mt-1 w-12 text-center text-xs text-gray-500">
										{formatDate(day.date).split(' ')[0]}
									</div>
								</div>
							{/each}
						</div>
					{:else}
						<div class="flex h-52 items-center justify-center text-gray-500">No data available</div>
					{/if}
				</div>
			</div>
		</div>

		<!-- Popular pages table -->
		<div class="mb-8 rounded-lg bg-white p-6 shadow">
			<h2 class="mb-4 text-lg font-medium text-gray-900">Most Popular Pages</h2>
			{#if popularPages.length > 0}
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr>
								<th
									scope="col"
									class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
								>
									Page
								</th>
								<th
									scope="col"
									class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
								>
									Views
								</th>
								<th
									scope="col"
									class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
								>
									Unique Visitors
								</th>
								<th
									scope="col"
									class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
								>
									Sessions
								</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200 bg-white">
							{#each popularPages as page}
								<tr class="hover:bg-gray-50">
									<td class="px-6 py-4 whitespace-nowrap">
										<a href={page.path} class="text-indigo-600 hover:text-indigo-900">
											{page.path || '/'}
										</a>
									</td>
									<td class="px-6 py-4 whitespace-nowrap text-gray-900">{page.views}</td>
									<td class="px-6 py-4 whitespace-nowrap text-gray-900">{page.uniqueUsers}</td>
									<td class="px-6 py-4 whitespace-nowrap text-gray-900">{page.uniqueSessions}</td>
								</tr>
							{/each}
						</tbody>
					</table>
				</div>
			{:else}
				<p class="text-gray-500">No page view data available</p>
			{/if}
		</div>

		<!-- User segments -->
		<div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2">
			<!-- Authenticated vs. Anonymous -->
			<div class="rounded-lg bg-white p-6 shadow">
				<h2 class="mb-4 text-lg font-medium text-gray-900">User Authentication</h2>
				{#if dashboardStats.length > 0}
					{@const authViews = dashboardStats.reduce((sum, day) => sum + day.authenticatedViews, 0)}
					{@const anonViews = dashboardStats.reduce((sum, day) => sum + day.anonymousViews, 0)}
					{@const totalViews = authViews + anonViews}
					{@const authPercent = totalViews > 0 ? Math.round((authViews / totalViews) * 100) : 0}
					{@const anonPercent = 100 - authPercent}

					<div class="mb-2 h-4 overflow-hidden rounded-full bg-gray-200">
						<div
							class="h-full bg-indigo-600"
							style="width: {authPercent}%"
							title="Authenticated: {authPercent}%"
						></div>
					</div>

					<div class="flex justify-between text-sm">
						<div>
							<span class="inline-block h-3 w-3 rounded-full bg-indigo-600"></span>
							<span class="ml-1 font-medium">Authenticated: {authViews} ({authPercent}%)</span>
						</div>
						<div>
							<span class="inline-block h-3 w-3 rounded-full bg-gray-300"></span>
							<span class="ml-1 font-medium">Anonymous: {anonViews} ({anonPercent}%)</span>
						</div>
					</div>
				{:else}
					<p class="text-gray-500">No user data available</p>
				{/if}
			</div>

			<!-- Device types -->
			<div class="rounded-lg bg-white p-6 shadow">
				<h2 class="mb-4 text-lg font-medium text-gray-900">Device Types</h2>
				<p class="mb-4 text-sm text-gray-500">
					This section will show device usage statistics once more data is collected.
				</p>
			</div>
		</div>
	{/if}
</div>
