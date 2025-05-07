<script lang="ts">
	let { data } = $props();
	let title = $state('');
	let description = $state('');
	let startDate = $state('');
	let endDate = $state('');
	let url = $state('');
	let projects = data.projects || [];
	let error = data.error;
</script>

<div class="mx-auto mb-8 max-w-xl">
	<h2 class="mb-4 text-xl font-bold">Your Projects</h2>
	{#if error}
		<div class="text-red-600 italic">{error}</div>
	{/if}
	{#if projects.length === 0}
		<div class="text-gray-500 italic">No projects added yet.</div>
	{:else}
		<ul class="space-y-4">
			{#each projects as project}
				<li class="rounded border bg-white p-4 shadow">
					<div class="font-semibold">{project.title}</div>
					<div class="text-sm text-gray-500">
						{project.start_date} - {project.end_date || 'Present'}
					</div>
					{#if project.description}
						<div class="mt-2 text-gray-700">{project.description}</div>
					{/if}
					{#if project.url}
						<div class="mt-2 text-blue-600 underline">
							<a href={project.url} target="_blank">{project.url}</a>
						</div>
					{/if}
				</li>
			{/each}
		</ul>
	{/if}
</div>

<form method="POST" class="mx-auto max-w-xl space-y-6 rounded bg-white p-8 shadow">
	<h2 class="mb-4 text-2xl font-bold">Add Project</h2>
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
		<label class="mb-1 block text-sm font-medium text-gray-700" for="description">Description</label
		>
		<textarea
			id="description"
			name="description"
			bind:value={description}
			class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
		></textarea>
	</div>
	<div class="flex gap-4">
		<div class="flex-1">
			<label class="mb-1 block text-sm font-medium text-gray-700" for="startDate">Start Date</label>
			<input
				id="startDate"
				name="startDate"
				type="date"
				bind:value={startDate}
				class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
			/>
		</div>
		<div class="flex-1">
			<label class="mb-1 block text-sm font-medium text-gray-700" for="endDate">End Date</label>
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
		<label class="mb-1 block text-sm font-medium text-gray-700" for="url">Project URL</label>
		<input
			id="url"
			name="url"
			type="url"
			bind:value={url}
			class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
		/>
	</div>
	<button
		type="submit"
		class="w-full rounded bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700"
		>Save Project</button
	>
</form>
