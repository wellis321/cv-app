<script lang="ts">
	import { getAvailableTemplates } from '$lib/utils/subscriptionUtils';
	import { session } from '$lib/stores/authStore';
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';

	// Template data
	let templates = $state<string[]>([]);
	let loading = $state(true);
	let showUpgradePrompt = $state(false);

	// Template descriptions
	const templateDescriptions = {
		basic: {
			title: 'Basic',
			description:
				'A clean, straightforward design that focuses on content clarity and readability.',
			features: [
				'Simple layout',
				'Professional appearance',
				'Classic typography',
				'Content-focused'
			],
			bestFor: ['Entry-level professionals', 'Academic applications', 'Minimalists'],
			colors: ['#000000', '#444444', '#888888']
		},
		professional: {
			title: 'Professional',
			description:
				'A traditional business-oriented template with a modern blue accent color scheme.',
			features: [
				'Blue accent colors',
				'Clean section dividers',
				'Structured layout',
				'Business-appropriate'
			],
			bestFor: ['Corporate professionals', 'Management roles', 'Business consultants'],
			colors: ['#2c3e50', '#3498db', '#34495e']
		},
		modern: {
			title: 'Modern',
			description:
				'A contemporary design with a side panel layout for visual interest and structure.',
			features: [
				'Side panel layout',
				'Modern indigo color scheme',
				'Balanced white space',
				'Visual hierarchy'
			],
			bestFor: ['Designers', 'Marketing professionals', 'Creative technologists'],
			colors: ['#1a237e', '#3f51b5', '#5c6bc0', '#e8eaf6']
		},
		executive: {
			title: 'Executive',
			description: 'A sophisticated, corporate-focused design with elegant structural elements.',
			features: [
				'Strong header line',
				'Understated color palette',
				'Refined typography',
				'Professional borders'
			],
			bestFor: ['Senior executives', 'Directors', 'C-suite professionals'],
			colors: ['#000000', '#263238', '#37474f', '#78909c']
		},
		creative: {
			title: 'Creative',
			description:
				'A vibrant and distinctive template with a bold header that stands out from the crowd.',
			features: [
				'Purple accent colors',
				'Bold header area',
				'Distinctive typography',
				'Visual impact'
			],
			bestFor: [
				'Creative professionals',
				'Artists',
				'Entertainment industry',
				'Marketing specialists'
			],
			colors: ['#6200ea', '#651fff', '#7c4dff', '#b388ff']
		},
		minimal: {
			title: 'Minimal',
			description:
				'A clean, reduced design that emphasizes simplicity and content over decorative elements.',
			features: [
				'Generous white space',
				'Subtle typography',
				'Clean layout',
				'No unnecessary elements'
			],
			bestFor: ['UX/UI designers', 'Architects', 'Minimalist professionals', 'Tech industry'],
			colors: ['#212121', '#424242', '#616161', '#757575']
		}
	};

	// Get default template preview data
	const previewData = {
		name: 'Jane Smith',
		title: 'Senior Software Engineer',
		email: 'jane.smith@example.com',
		phone: '+44 123 456 7890',
		location: 'London, UK',
		experience: [
			{
				position: 'Senior Software Engineer',
				company: 'Tech Innovations Ltd',
				duration: '2018 - Present'
			},
			{
				position: 'Software Developer',
				company: 'Digital Solutions',
				duration: '2015 - 2018'
			}
		],
		education: [
			{
				degree: 'MSc Computer Science',
				institution: 'University of Technology',
				duration: '2013 - 2015'
			}
		],
		skills: ['JavaScript', 'TypeScript', 'React', 'Node.js', 'Svelte', 'SQL', 'GraphQL']
	};

	onMount(() => {
		// Get all available templates for current subscription
		templates = getAvailableTemplates();

		// Check if we need to show the upgrade prompt
		showUpgradePrompt = templates.length === 1;

		// Add a full array for showcase purposes (will be filtered in UI)
		const allTemplates = ['basic', 'professional', 'modern', 'executive', 'creative', 'minimal'];

		// Combine current templates with all templates for display
		// We'll mark unavailable ones in the UI
		templates = [...new Set([...templates, ...allTemplates])];

		loading = false;
	});

	// Check if a template is available for the current user
	function isTemplateAvailable(template: string): boolean {
		return getAvailableTemplates().includes(template);
	}

	// Get color swatches for a template
	function getColorSwatches(template: string) {
		return templateDescriptions[template as keyof typeof templateDescriptions]?.colors || [];
	}
</script>

<svelte:head>
	<title>CV Templates | Customize Your CV</title>
</svelte:head>

<div class="container mx-auto max-w-6xl px-4 py-8">
	<div class="mb-8">
		<h1 class="mb-2 text-3xl font-bold">CV Templates</h1>
		<p class="text-gray-600">
			Choose from our range of professionally designed CV templates to showcase your skills and
			experience.
		</p>
	</div>

	{#if loading}
		<div class="flex justify-center py-12">
			<div
				class="border-primary-500 h-12 w-12 animate-spin rounded-full border-t-2 border-b-2"
			></div>
		</div>
	{:else}
		<div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
			{#each templates as template}
				{@const templateInfo = templateDescriptions[template as keyof typeof templateDescriptions]}
				{@const available = isTemplateAvailable(template)}

				<div
					class="overflow-hidden rounded-lg border bg-white shadow-md {available
						? 'border-gray-200'
						: 'border-gray-200 opacity-70'}"
				>
					<!-- Template Preview -->
					<div class="relative">
						<!-- Template header bar -->
						{#if template === 'creative'}
							<div class="absolute top-0 right-0 left-0 h-12 bg-[#b388ff]"></div>
						{:else if template === 'executive'}
							<div class="absolute top-4 right-4 left-4 h-1 bg-[#263238]"></div>
						{:else if template === 'modern'}
							<div class="absolute top-0 bottom-0 left-0 w-20 bg-[#e8eaf6]"></div>
						{/if}

						<div class="relative px-6 pt-6 pb-4">
							<!-- Template Content -->
							<div class="mb-6 {template === 'modern' ? 'ml-16' : ''}">
								<h2
									class="{template === 'basic'
										? 'text-black'
										: template === 'professional'
											? 'text-[#2c3e50]'
											: template === 'modern'
												? 'text-[#1a237e]'
												: template === 'executive'
													? 'text-black'
													: template === 'creative'
														? 'text-[#6200ea]'
														: template === 'minimal'
															? 'text-[#212121]'
															: ''}
											text-center text-xl font-bold"
								>
									{previewData.name}
								</h2>
								<p class="mt-1 text-center text-sm text-gray-600">
									{previewData.title}
								</p>
								<div class="mt-2 flex justify-center space-x-3 text-xs text-gray-500">
									<span>{previewData.email}</span>
									<span>{previewData.phone}</span>
								</div>
							</div>

							<!-- Divider -->
							{#if template === 'professional'}
								<div class="mb-4 h-0.5 bg-[#3498db]"></div>
							{:else if template !== 'executive' && template !== 'creative'}
								<div class="mb-4 h-px bg-gray-200"></div>
							{/if}

							<!-- Experience Section -->
							<div class={template === 'modern' ? 'ml-16' : ''}>
								<h3
									class="{template === 'basic'
										? 'text-black'
										: template === 'professional'
											? 'text-[#3498db]'
											: template === 'modern'
												? 'text-[#3f51b5]'
												: template === 'executive'
													? 'text-[#263238]'
													: template === 'creative'
														? 'text-[#651fff]'
														: template === 'minimal'
															? 'text-[#424242]'
															: ''}
											mb-2 text-sm font-bold uppercase"
								>
									Experience
								</h3>

								{#each previewData.experience.slice(0, 2) as job}
									<div class="mb-3">
										<div class="flex items-start justify-between">
											<span
												class="{template === 'basic'
													? 'text-black'
													: template === 'professional'
														? 'text-[#2c3e50]'
														: template === 'modern'
															? 'text-[#303f9f]'
															: template === 'executive'
																? 'text-black'
																: template === 'creative'
																	? 'text-[#7c4dff]'
																	: template === 'minimal'
																		? 'text-[#212121]'
																		: ''}
														text-sm font-medium"
											>
												{job.position}
											</span>
											<span
												class="{template === 'basic'
													? 'text-gray-500'
													: template === 'professional'
														? 'text-[#34495e]'
														: template === 'modern'
															? 'text-[#5c6bc0]'
															: template === 'executive'
																? 'text-[#546e7a]'
																: template === 'creative'
																	? 'text-[#9575cd]'
																	: template === 'minimal'
																		? 'text-[#757575]'
																		: ''}
														text-xs"
											>
												{job.duration}
											</span>
										</div>
										<p
											class="{template === 'basic'
												? 'text-gray-600'
												: template === 'professional'
													? 'text-[#34495e]'
													: template === 'modern'
														? 'text-[#424242]'
														: template === 'executive'
															? 'text-[#37474f]'
															: template === 'creative'
																? 'text-[#512da8]'
																: template === 'minimal'
																	? 'text-[#616161]'
																	: ''}
													text-xs"
										>
											{job.company}
										</p>
									</div>
								{/each}
							</div>
						</div>
					</div>

					<!-- Template Info -->
					<div class="border-t border-gray-200 bg-gray-50 p-6">
						<div class="mb-3 flex items-center justify-between">
							<h3 class="text-lg font-semibold">{templateInfo.title}</h3>
							{#if !available}
								<span class="rounded bg-gray-200 px-2 py-1 text-xs text-gray-700">Premium</span>
							{:else}
								<span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">Available</span>
							{/if}
						</div>

						<p class="mb-4 text-sm text-gray-600">{templateInfo.description}</p>

						<!-- Color swatches -->
						<div class="mb-4">
							<p class="mb-2 text-xs text-gray-500">Color Palette</p>
							<div class="flex space-x-2">
								{#each getColorSwatches(template) as color}
									<div
										class="h-6 w-6 rounded-full"
										style="background-color: {color}"
										title={color}
									></div>
								{/each}
							</div>
						</div>

						<!-- Best for -->
						<div class="mb-4">
							<p class="mb-1 text-xs text-gray-500">Best for:</p>
							<div class="flex flex-wrap gap-1">
								{#each templateInfo.bestFor as industry}
									<span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-800"
										>{industry}</span
									>
								{/each}
							</div>
						</div>

						{#if !available}
							<button
								class="bg-primary-500 hover:bg-primary-600 mt-2 w-full rounded px-4 py-2 text-white transition duration-200"
								on:click={() => goto('/subscription?required=templates')}
							>
								Upgrade to Unlock
							</button>
						{:else}
							<a
								href="/preview-cv?template={template}"
								class="bg-primary-500 hover:bg-primary-600 mt-2 block w-full rounded px-4 py-2 text-center text-white transition duration-200"
							>
								Use Template
							</a>
						{/if}
					</div>
				</div>
			{/each}
		</div>
	{/if}
</div>

<style>
	/* Add any additional styling here */
</style>
