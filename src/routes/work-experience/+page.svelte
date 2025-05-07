<script lang="ts">
	export let data;
	let companyName = $state('');
	let position = $state('');
	let startDate = $state('');
	let endDate = $state('');
	let description = $state('');
	let workExperiences = data.workExperiences || [];
	let error = data.error;
</script>

<div class="mx-auto mb-8 max-w-xl">
	<h2 class="mb-4 text-xl font-bold">Your Work Experience</h2>
	{#if error}
		<div class="text-red-600 italic">{error}</div>
	{/if}
	{#if workExperiences.length === 0}
		<div class="text-gray-500 italic">No work experience added yet.</div>
	{:else}
		<ul class="space-y-4">
			{#each workExperiences as exp}
				<li class="rounded border bg-white p-4 shadow">
					<div class="flex items-center justify-between">
						<div>
							<div class="font-semibold">{exp.position} at {exp.company_name}</div>
							<div class="text-sm text-gray-500">
								{exp.start_date} - {exp.end_date || 'Present'}
							</div>
						</div>
						<!-- Edit/Delete buttons can go here -->
					</div>
					{#if exp.description}
						<div class="mt-2 text-gray-700">{exp.description}</div>
					{/if}
				</li>
			{/each}
		</ul>
	{/if}
</div>

<form method="POST" class="mx-auto max-w-xl space-y-6 rounded bg-white p-8 shadow">
	<h2 class="mb-4 text-2xl font-bold">Add Work Experience</h2>
	<div>
		<label class="mb-1 block text-sm font-medium text-gray-700" for="companyName"
			>Company Name</label
		>
		<input
			id="companyName"
			name="companyName"
			type="text"
			bind:value={companyName}
			class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
			required
		/>
	</div>
	<div>
		<label class="mb-1 block text-sm font-medium text-gray-700" for="position">Position</label>
		<input
			id="position"
			name="position"
			type="text"
			bind:value={position}
			class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
			required
		/>
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
				required
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
		<label class="mb-1 block text-sm font-medium text-gray-700" for="description">Description</label
		>
		<textarea
			id="description"
			name="description"
			bind:value={description}
			class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
		></textarea>
	</div>
	<button
		type="submit"
		class="w-full rounded bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700"
		>Save Experience</button
	>
</form>
