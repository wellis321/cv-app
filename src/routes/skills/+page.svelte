<script lang="ts">
	let { data } = $props();
	let name = $state('');
	let level = $state('');
	let category = $state('');
	let skills = data.skills || [];
	let error = data.error;
</script>

<div class="mx-auto mb-8 max-w-xl">
	<h2 class="mb-4 text-xl font-bold">Your Skills</h2>
	{#if error}
		<div class="text-red-600 italic">{error}</div>
	{/if}
	{#if skills.length === 0}
		<div class="text-gray-500 italic">No skills added yet.</div>
	{:else}
		<ul class="space-y-4">
			{#each skills as skill}
				<li class="rounded border bg-white p-4 shadow">
					<div class="font-semibold">{skill.name}</div>
					{#if skill.level}
						<div class="text-sm text-gray-500">Level: {skill.level}</div>
					{/if}
					{#if skill.category}
						<div class="text-sm text-gray-500">Category: {skill.category}</div>
					{/if}
				</li>
			{/each}
		</ul>
	{/if}
</div>

<form method="POST" class="mx-auto max-w-xl space-y-6 rounded bg-white p-8 shadow">
	<h2 class="mb-4 text-2xl font-bold">Skill</h2>
	<div>
		<label class="mb-1 block text-sm font-medium text-gray-700" for="name">Skill Name</label>
		<input
			id="name"
			name="name"
			type="text"
			bind:value={name}
			class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
			required
		/>
	</div>
	<div>
		<label class="mb-1 block text-sm font-medium text-gray-700" for="level">Level</label>
		<input
			id="level"
			name="level"
			type="text"
			bind:value={level}
			class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
		/>
	</div>
	<div>
		<label class="mb-1 block text-sm font-medium text-gray-700" for="category">Category</label>
		<input
			id="category"
			name="category"
			type="text"
			bind:value={category}
			class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
		/>
	</div>
	<button
		type="submit"
		class="w-full rounded bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700"
		>Save Skill</button
	>
</form>
