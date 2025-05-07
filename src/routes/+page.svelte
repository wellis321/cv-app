<script lang="ts">
	const sections = [
		{
			title: 'Profile',
			href: '/profile',
			description: 'Your personal information and contact details'
		},
		{
			title: 'Work Experience',
			href: '/work-experience',
			description: 'Your professional work history'
		},
		{ title: 'Projects', href: '/projects', description: 'Notable projects and achievements' },
		{ title: 'Education', href: '/education', description: 'Your academic background' },
		{ title: 'Skills', href: '/skills', description: 'Your technical and soft skills' },
		{
			title: 'Certifications',
			href: '/certifications',
			description: 'Professional certifications and qualifications'
		},
		{
			title: 'Professional Memberships',
			href: '/memberships',
			description: 'Professional organisations and memberships'
		},
		{ title: 'Interests', href: '/interests', description: 'Your hobbies and interests' }
	];

	console.log('ALL ENV:', import.meta.env);
</script>

<div class="py-12">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
		<div class="text-center">
			<h1 class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl">
				Build Your Professional CV
			</h1>
			<p
				class="mx-auto mt-3 max-w-md text-base text-gray-500 sm:text-lg md:mt-5 md:max-w-3xl md:text-xl"
			>
				Create a comprehensive CV that showcases your professional journey. Get started by filling
				out each section below.
			</p>
		</div>

		<div class="mx-auto mt-12 grid max-w-lg gap-5 lg:max-w-none lg:grid-cols-3">
			{#each sections as section}
				<div class="flex flex-col overflow-hidden rounded-lg shadow-lg">
					<div class="flex flex-1 flex-col justify-between bg-white p-6">
						<div class="flex-1">
							<a href={section.href} class="mt-2 block">
								<p class="text-xl font-semibold text-gray-900">{section.title}</p>
								<p class="mt-3 text-base text-gray-500">{section.description}</p>
							</a>
						</div>
					</div>
				</div>
			{/each}
		</div>
	</div>
</div>
