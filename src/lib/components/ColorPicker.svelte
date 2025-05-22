<script lang="ts">
	export let value: string = '#000000';
	export let label: string = 'Color';
	export let id: string = `color-${Math.random().toString(36).substring(2, 9)}`;

	// Validate hex color
	function isValidHexColor(color: string): boolean {
		return /^#[0-9A-F]{6}$/i.test(color);
	}

	// Handle color change with validation
	function handleChange(event: Event) {
		const input = event.target as HTMLInputElement;
		if (isValidHexColor(input.value)) {
			value = input.value;
		}
	}
</script>

<div>
	<label class="block text-sm font-medium text-gray-700" for={id}>{label}</label>
	<div class="mt-1 flex items-center gap-3">
		<input
			type="color"
			{id}
			name={id}
			bind:value
			on:input={handleChange}
			class="h-10 w-10 cursor-pointer rounded border border-gray-300 p-0"
		/>
		<span class="font-mono text-sm">{value}</span>
	</div>
</div>
