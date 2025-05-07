<script lang="ts">
	let { data } = $props();
	let institution = $state('');
	let degree = $state('');
	let fieldOfStudy = $state('');
	let startDate = $state('');
	let endDate = $state('');
	let educationList = data.educationList || [];
	let error = data.error;
</script>

<div class="mx-auto mb-8 max-w-xl">
	<h2 class="mb-4 text-xl font-bold">Your Education</h2>
	{#if error}
		<div class="text-red-600 italic">{error}</div>
	{/if}
	{#if educationList.length === 0}
		<div class="text-gray-500 italic">No education added yet.</div>
	{:else}
		<ul class="space-y-4">
			{#each educationList as edu}
				<li class="rounded border bg-white p-4 shadow">
					<div class="font-semibold">{edu.degree} at {edu.institution}</div>
					<div class="text-sm text-gray-500">{edu.start_date} - {edu.end_date || 'Present'}</div>
					{#if edu.field_of_study}
						<div class="mt-2 text-gray-700">Field: {edu.field_of_study}</div>
					{/if}
				</li>
			{/each}
		</ul>
	{/if}
</div>

<form method="POST" class="mx-auto max-w-xl space-y-6 rounded bg-white p-8 shadow">
	<h2 class="mb-4 text-2xl font-bold">Add Education</h2>
	<div>
		<label class="mb-1 block text-sm font-medium text-gray-700" for="institution">Institution</label
		>
		<input
			id="institution"
			name="institution"
			type="text"
			bind:value={institution}
			class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
			required
		/>
	</div>
	<div>
		<label class="mb-1 block text-sm font-medium text-gray-700" for="degree">Degree</label>
		<input
			id="degree"
			name="degree"
			type="text"
			bind:value={degree}
			class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
			required
		/>
	</div>
	<div>
		<label class="mb-1 block text-sm font-medium text-gray-700" for="fieldOfStudy"
			>Field of Study</label
		>
		<input
			id="fieldOfStudy"
			name="fieldOfStudy"
			type="text"
			bind:value={fieldOfStudy}
			class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
	<button
		type="submit"
		class="w-full rounded bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700"
		>Save Education</button
	>
</form>
