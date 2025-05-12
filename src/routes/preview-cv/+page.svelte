<script lang="ts">
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';
	import { session } from '$lib/stores/authStore';
	import { supabase } from '$lib/supabase';
	import SectionNavigation from '$lib/components/SectionNavigation.svelte';
	import { generateCvPdf, formatDate, type CvData } from '$lib/pdfGenerator';

	// CV data
	let profile = $state<any>(null);
	let workExperiences = $state<any[]>([]);
	let projects = $state<any[]>([]);
	let skills = $state<any[]>([]);
	let education = $state<any[]>([]);
	let certifications = $state<any[]>([]);
	let memberships = $state<any[]>([]);
	let interests = $state<any[]>([]);
	let qualificationEquivalence = $state<any[]>([]);
	let error = $state<string | null>(null);
	let loading = $state(true);
	let generatingPdf = $state(false);

	// Shareable URL
	let shareableUrl = $state<string | null>(null);

	// Interface for skill objects
	interface Skill {
		name: string;
		level?: string;
		category?: string;
	}

	// Load all CV data
	onMount(async () => {
		if (!browser) return;

		if (!$session || !$session.user) {
			error = 'Please log in to view your CV';
			loading = false;
			return;
		}

		try {
			const userId = $session.user.id;

			// Load profile data
			const { data: profileData, error: profileError } = await supabase
				.from('profiles')
				.select('*')
				.eq('id', userId)
				.single();

			if (profileError) {
				console.error('Error loading profile:', profileError);
				error = 'Failed to load profile data';
				loading = false;
				return;
			}

			profile = profileData;

			// Load work experiences
			const { data: workData, error: workError } = await supabase
				.from('work_experience')
				.select('*')
				.eq('profile_id', userId)
				.order('start_date', { ascending: false });

			if (workError) {
				console.error('Error loading work experiences:', workError);
			} else {
				workExperiences = workData || [];
			}

			// Load projects
			const { data: projectsData, error: projectsError } = await supabase
				.from('projects')
				.select('*')
				.eq('profile_id', userId)
				.order('start_date', { ascending: false });

			if (projectsError) {
				console.error('Error loading projects:', projectsError);
			} else {
				projects = projectsData || [];
			}

			// Load skills
			const { data: skillsData, error: skillsError } = await supabase
				.from('skills')
				.select('*')
				.eq('profile_id', userId);

			if (skillsError) {
				console.error('Error loading skills:', skillsError);
			} else {
				skills = skillsData || [];
			}

			// Load education (if we have an education table)
			try {
				const { data: educationData, error: educationError } = await supabase
					.from('education')
					.select('*')
					.eq('profile_id', userId)
					.order('start_date', { ascending: false });

				if (!educationError) {
					education = educationData || [];
				}
			} catch (err) {
				console.log('Education table might not exist yet', err);
			}

			// Load certifications
			try {
				const { data: certData, error: certError } = await supabase
					.from('certifications')
					.select('*')
					.eq('profile_id', userId)
					.order('date_issued', { ascending: false });

				if (!certError) {
					certifications = certData || [];
				}
			} catch (err) {
				console.log('Certifications table might not exist yet', err);
			}

			// Load memberships
			try {
				const { data: membershipData, error: membershipError } = await supabase
					.from('professional_memberships')
					.select('*')
					.eq('profile_id', userId)
					.order('start_date', { ascending: false });

				if (!membershipError) {
					memberships = membershipData || [];
				}
			} catch (err) {
				console.log('Professional memberships table might not exist yet', err);
			}

			// Load interests
			try {
				const { data: interestsData, error: interestsError } = await supabase
					.from('interests')
					.select('*')
					.eq('profile_id', userId);

				if (!interestsError) {
					interests = interestsData || [];
				}
			} catch (err) {
				console.log('Interests table might not exist yet', err);
			}

			// Load qualification equivalence
			try {
				const { data: qualificationData, error: qualificationError } = await supabase
					.from('professional_qualification_equivalence')
					.select('*')
					.eq('profile_id', userId);

				if (!qualificationError) {
					qualificationEquivalence = qualificationData || [];
				}
			} catch (err) {
				console.log('Professional qualification equivalence table might not exist yet', err);
			}

			// Generate shareable URL
			shareableUrl = `${window.location.origin}/cv/${userId}`;

			// Clear errors
			error = null;
		} catch (err) {
			console.error('Error loading CV data:', err);
			error = 'An unexpected error occurred while loading your CV data';
		} finally {
			loading = false;
		}
	});

	// Generate PDF
	async function generatePdf() {
		if (!browser || !profile) return;

		generatingPdf = true;

		try {
			// Prepare CV data for the PDF generator
			const cvData: CvData = {
				profile,
				workExperiences,
				projects,
				skills,
				education,
				certifications,
				memberships,
				interests,
				qualificationEquivalence
			};

			// Generate and download the PDF
			await generateCvPdf(cvData);
		} catch (err: unknown) {
			console.error('Error generating PDF:', err);
			const errorMessage = err instanceof Error ? err.message : 'Unknown error';
			error = `Failed to generate PDF: ${errorMessage}`;
		} finally {
			generatingPdf = false;
		}
	}

	// Copy shareable URL to clipboard
	function copyToClipboard() {
		if (!browser || !shareableUrl) return;

		navigator.clipboard
			.writeText(shareableUrl)
			.then(() => {
				alert('URL copied to clipboard!');
			})
			.catch((err) => {
				console.error('Could not copy URL:', err);
				alert('Failed to copy URL to clipboard.');
			});
	}

	// Group skills by category
	$effect(() => {
		if (skills.length > 0) {
			// Group skills by category
			const skillsByCategory = skills.reduce<Record<string, Skill[]>>((acc, skill) => {
				const category = skill.category || 'Other';
				if (!acc[category]) {
					acc[category] = [];
				}
				acc[category].push(skill);
				return acc;
			}, {});

			// Sort skills in each category
			Object.keys(skillsByCategory).forEach((category) => {
				skillsByCategory[category].sort((a: Skill, b: Skill) => a.name.localeCompare(b.name));
			});

			// Update skills (for reactivity)
			const categorized = [];
			Object.keys(skillsByCategory)
				.sort()
				.forEach((category) => {
					categorized.push({
						category,
						skills: skillsByCategory[category]
					});
				});
		}
	});
</script>

<div class="mx-auto max-w-4xl px-4 py-8">
	<div class="mb-6 flex items-center justify-between">
		<h1 class="text-2xl font-bold">CV Preview</h1>
		<div class="flex gap-2">
			<button
				onclick={generatePdf}
				disabled={loading || generatingPdf || !profile}
				class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none disabled:opacity-50"
			>
				{generatingPdf ? 'Generating...' : 'Download PDF'}
			</button>

			{#if shareableUrl}
				<div class="relative ml-2">
					<input
						type="text"
						readonly
						value={shareableUrl}
						class="w-60 rounded border px-3 py-2 text-sm"
					/>
					<button
						onclick={copyToClipboard}
						class="absolute top-1/2 right-2 -translate-y-1/2 rounded text-indigo-600 hover:text-indigo-800"
						title="Copy to clipboard"
						aria-label="Copy shareable URL to clipboard"
					>
						<svg
							xmlns="http://www.w3.org/2000/svg"
							class="h-5 w-5"
							fill="none"
							viewBox="0 0 24 24"
							stroke="currentColor"
						>
							<path
								stroke-linecap="round"
								stroke-linejoin="round"
								stroke-width="2"
								d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
							/>
						</svg>
					</button>
				</div>
			{/if}
		</div>
	</div>

	{#if error}
		<div class="mb-4 rounded bg-red-100 p-4 text-red-700">{error}</div>
	{/if}

	{#if loading}
		<div class="my-8 flex justify-center">
			<div class="text-center">
				<div
					class="mx-auto mb-2 h-10 w-10 animate-spin rounded-full border-t-2 border-b-2 border-indigo-500"
				></div>
				<p class="text-gray-600">Loading your CV data...</p>
			</div>
		</div>
	{:else if !profile}
		<div class="rounded bg-yellow-100 p-4">
			<p class="font-medium">Please complete your profile to preview your CV.</p>
			<a
				href="/profile"
				class="mt-2 inline-block rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700"
			>
				Go to Profile
			</a>
		</div>
	{:else}
		<div id="cv-content" class="bg-white p-8 shadow-lg">
			<!-- Header with personal info -->
			<div class="border-b border-gray-300 pb-6">
				<div class="flex items-start justify-between">
					<div>
						<h1 class="text-3xl font-bold">{profile.full_name || 'Your Name'}</h1>
						<div class="mt-2 text-gray-700">
							{#if profile.location}
								<p>{profile.location}</p>
							{/if}
							{#if profile.email}
								<p>{profile.email}</p>
							{/if}
							{#if profile.phone}
								<p>{profile.phone}</p>
							{/if}
						</div>
					</div>

					{#if profile.photo_url}
						<div class="h-28 w-28 overflow-hidden rounded-full">
							<img
								src={profile.photo_url}
								alt={profile.full_name || 'Profile picture'}
								class="h-full w-full object-cover"
							/>
						</div>
					{/if}
				</div>
			</div>

			<!-- Work Experience -->
			{#if workExperiences.length > 0}
				<div class="mt-8">
					<h2 class="mb-4 text-xl font-bold">Work Experience</h2>
					<div class="space-y-6">
						{#each workExperiences as job}
							<div>
								<div class="flex justify-between">
									<div>
										<h3 class="font-semibold">{job.position}</h3>
										<h4 class="text-gray-700">{job.company_name}</h4>
									</div>
									<div class="text-sm text-gray-600">
										{formatDate(job.start_date)} - {formatDate(job.end_date)}
									</div>
								</div>
								{#if job.description}
									<div class="mt-2 text-gray-700">
										<p class="whitespace-pre-line">{job.description}</p>
									</div>
								{/if}
							</div>
						{/each}
					</div>
				</div>
			{/if}

			<!-- Projects -->
			{#if projects.length > 0}
				<div class="mt-8">
					<h2 class="mb-4 text-xl font-bold">Projects</h2>
					<div class="space-y-6">
						{#each projects as project}
							<div>
								<div class="flex justify-between">
									<div>
										<h3 class="font-semibold">{project.title}</h3>
										{#if project.url}
											<a
												href={project.url}
												target="_blank"
												rel="noopener noreferrer"
												class="text-indigo-600 hover:underline"
											>
												{project.url.replace(/^https?:\/\//, '')}
											</a>
										{/if}
									</div>
									{#if project.start_date}
										<div class="text-sm text-gray-600">
											{formatDate(project.start_date)} - {formatDate(project.end_date)}
										</div>
									{/if}
								</div>
								{#if project.description}
									<div class="mt-2 text-gray-700">
										<p class="whitespace-pre-line">{project.description}</p>
									</div>
								{/if}
							</div>
						{/each}
					</div>
				</div>
			{/if}

			<!-- Skills -->
			{#if skills.length > 0}
				<div class="mt-8">
					<h2 class="mb-4 text-xl font-bold">Skills</h2>
					<div>
						{#each skills as skill}
							<span class="mr-2 mb-2 inline-block rounded-full bg-gray-200 px-3 py-1 text-sm">
								{skill.name}
								{#if skill.level}
									<span class="ml-1 text-gray-600">({skill.level})</span>
								{/if}
							</span>
						{/each}
					</div>
				</div>
			{/if}

			<!-- Education -->
			{#if education.length > 0}
				<div class="mt-8">
					<h2 class="mb-4 text-xl font-bold">Education</h2>
					<div class="space-y-6">
						{#each education as edu}
							<div>
								<div class="flex justify-between">
									<div>
										<h3 class="font-semibold">{edu.degree || edu.course}</h3>
										<h4 class="text-gray-700">{edu.institution}</h4>
									</div>
									<div class="text-sm text-gray-600">
										{formatDate(edu.start_date)} - {formatDate(edu.end_date)}
									</div>
								</div>
								{#if edu.description}
									<div class="mt-2 text-gray-700">
										<p class="whitespace-pre-line">{edu.description}</p>
									</div>
								{/if}
							</div>
						{/each}
					</div>
				</div>
			{/if}

			<!-- Certifications -->
			{#if certifications && certifications.length > 0}
				<div class="mt-8">
					<h2 class="mb-4 text-xl font-bold">Certifications</h2>
					<div class="space-y-6">
						{#each certifications as cert}
							<div>
								<div class="flex justify-between">
									<div>
										<h3 class="font-semibold">{cert.name}</h3>
										{#if cert.issuer}
											<h4 class="text-gray-700">{cert.issuer}</h4>
										{/if}
										{#if cert.url}
											<a
												href={cert.url}
												target="_blank"
												rel="noopener noreferrer"
												class="text-indigo-600 hover:underline"
											>
												{cert.url.replace(/^https?:\/\//, '')}
											</a>
										{/if}
									</div>
									{#if cert.date_issued}
										<div class="text-sm text-gray-600">
											Issued: {formatDate(cert.date_issued)}
											{#if cert.expiry_date}
												<br />Expires: {formatDate(cert.expiry_date)}
											{/if}
										</div>
									{/if}
								</div>
								{#if cert.description}
									<div class="mt-2 text-gray-700">
										<p class="whitespace-pre-line">{cert.description}</p>
									</div>
								{/if}
							</div>
						{/each}
					</div>
				</div>
			{/if}

			<!-- Professional Memberships -->
			{#if memberships && memberships.length > 0}
				<div class="mt-8">
					<h2 class="mb-4 text-xl font-bold">Professional Memberships</h2>
					<div class="space-y-6">
						{#each memberships as membership}
							<div>
								<div class="flex justify-between">
									<div>
										<h3 class="font-semibold">{membership.organisation}</h3>
										{#if membership.role}
											<h4 class="text-gray-700">{membership.role}</h4>
										{/if}
									</div>
									{#if membership.start_date}
										<div class="text-sm text-gray-600">
											{formatDate(membership.start_date)} - {formatDate(membership.end_date)}
										</div>
									{/if}
								</div>
								{#if membership.description}
									<div class="mt-2 text-gray-700">
										<p class="whitespace-pre-line">{membership.description}</p>
									</div>
								{/if}
							</div>
						{/each}
					</div>
				</div>
			{/if}

			<!-- Qualification Equivalence -->
			{#if qualificationEquivalence && qualificationEquivalence.length > 0}
				<div class="mt-8">
					<h2 class="mb-4 text-xl font-bold">Professional Qualification Equivalence</h2>
					<div class="space-y-6">
						{#each qualificationEquivalence as qualification}
							<div>
								<h3 class="font-semibold">{qualification.qualification}</h3>
								<h4 class="text-gray-700">Equivalent to: {qualification.equivalent_to}</h4>
								{#if qualification.description}
									<div class="mt-2 text-gray-700">
										<p class="whitespace-pre-line">{qualification.description}</p>
									</div>
								{/if}
							</div>
						{/each}
					</div>
				</div>
			{/if}

			<!-- Interests -->
			{#if interests && interests.length > 0}
				<div class="mt-8">
					<h2 class="mb-4 text-xl font-bold">Interests & Activities</h2>
					<div class="space-y-6">
						{#each interests as interest}
							<div>
								<h3 class="font-semibold">{interest.name}</h3>
								{#if interest.description}
									<div class="mt-2 text-gray-700">
										<p class="whitespace-pre-line">{interest.description}</p>
									</div>
								{/if}
							</div>
						{/each}
					</div>
				</div>
			{/if}
		</div>
	{/if}

	<div class="mt-6">
		<SectionNavigation />
	</div>
</div>
