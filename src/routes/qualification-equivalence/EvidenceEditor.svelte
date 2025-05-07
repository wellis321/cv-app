<script lang="ts">
	import { onMount, createEventDispatcher } from 'svelte';
	import {
		type SupportingEvidence,
		addEvidence,
		updateEvidence,
		deleteEvidence,
		reorderEvidence
	} from './qualificationUtils';
	import { supabase } from '../../lib/supabase';

	// Event dispatcher to communicate with parent
	const dispatch = createEventDispatcher();

	// Props
	let { qualificationId = '', readOnly = false } = $props<{
		qualificationId: string;
		readOnly?: boolean;
	}>();

	// State
	let evidenceItems = $state<SupportingEvidence[]>([]);
	let loading = $state(true);
	let error = $state<string | null>(null);

	// Form state
	let newEvidenceContent = $state('');
	let editingEvidenceId = $state<string | null>(null);
	let editEvidenceContent = $state('');

	// Function to notify parent that we're editing
	function setEditingEvidence(editing: boolean) {
		dispatch('editingEvidence', { editing });
	}

	// Load evidence on mount
	onMount(async () => {
		await loadEvidence();
	});

	async function loadEvidence() {
		if (!qualificationId) {
			console.error('Missing qualificationId in EvidenceEditor');
			error = 'Missing qualification ID';
			loading = false;
			return;
		}

		loading = true;
		error = null;

		try {
			// Fetch evidence directly from the database
			const { data: evidenceData, error: fetchError } = await supabase
				.from('supporting_evidence')
				.select('*')
				.eq('qualification_equivalence_id', qualificationId)
				.order('sort_order', { ascending: true });

			if (fetchError) {
				console.error('Error fetching evidence:', fetchError);
				error = 'Failed to load evidence. Please try again.';
			} else {
				evidenceItems = evidenceData || [];
			}
		} catch (err) {
			console.error('Unexpected error loading evidence:', err);
			error = 'An unexpected error occurred. Please try again.';
		} finally {
			loading = false;
		}
	}

	// Add a new evidence item
	async function handleAddEvidence() {
		if (!newEvidenceContent.trim()) return;

		// Notify parent we're editing
		setEditingEvidence(true);

		try {
			const result = await addEvidence(qualificationId, newEvidenceContent);

			if (result) {
				// Reset the input field
				newEvidenceContent = '';

				// Add the new evidence to the list
				evidenceItems = [...evidenceItems, result];

				// Focus back on the input field after a short delay
				setTimeout(() => {
					const inputElement = document.getElementById('new-evidence-input') as HTMLInputElement;
					if (inputElement) {
						inputElement.focus();
					}
				}, 100);
			} else {
				error = 'Failed to add evidence. Please try again.';
			}
		} catch (err) {
			console.error('Error adding evidence:', err);
			error = 'Failed to add evidence. Please try again.';
		}
	}

	// Start editing an evidence item
	function startEditEvidence(evidence: SupportingEvidence) {
		editingEvidenceId = evidence.id;
		editEvidenceContent = evidence.content;
	}

	// Cancel editing
	function cancelEditEvidence() {
		editingEvidenceId = null;
		editEvidenceContent = '';
	}

	// Save evidence edit
	async function saveEditEvidence(evidenceId: string) {
		if (!editEvidenceContent.trim()) return;

		// Notify parent we're editing
		setEditingEvidence(true);

		try {
			const success = await updateEvidence(evidenceId, editEvidenceContent);

			if (success) {
				// Update the item in the list
				evidenceItems = evidenceItems.map((item) =>
					item.id === evidenceId ? { ...item, content: editEvidenceContent } : item
				);

				// Reset the edit state
				editingEvidenceId = null;
				editEvidenceContent = '';
			} else {
				error = 'Failed to update evidence. Please try again.';
			}
		} catch (err) {
			console.error('Error updating evidence:', err);
			error = 'Failed to update evidence. Please try again.';
		}
	}

	// Delete an evidence item
	async function handleDeleteEvidence(evidenceId: string) {
		if (!confirm('Are you sure you want to delete this evidence item?')) {
			return;
		}

		// Notify parent we're editing
		setEditingEvidence(true);

		try {
			const success = await deleteEvidence(evidenceId);

			if (success) {
				// Remove the item from the list
				evidenceItems = evidenceItems.filter((item) => item.id !== evidenceId);
			} else {
				error = 'Failed to delete evidence. Please try again.';
			}
		} catch (err) {
			console.error('Error deleting evidence:', err);
			error = 'Failed to delete evidence. Please try again.';
		}
	}

	// Format evidence as text
	export function getFormattedText(): string {
		if (evidenceItems.length === 0) return '';

		let text = 'Supporting Evidence:\n';

		evidenceItems.forEach((evidence, index) => {
			text += `• ${evidence.content}\n`;
		});

		return text;
	}

	// Function to update the evidence items from parent
	export function updateEvidenceItems(items: SupportingEvidence[]) {
		evidenceItems = items;
		loading = false;
	}
</script>

{#if loading}
	<div class="py-4 text-center text-gray-500">
		<p>Loading evidence...</p>
	</div>
{:else if error}
	<div class="py-4 text-red-500">
		<p>{error}</p>
		<button class="mt-2 text-sm text-indigo-600 underline" onclick={() => loadEvidence()}>
			Try again
		</button>
	</div>
{:else}
	<div class="mt-4">
		<h3 class="mb-4 text-lg font-medium text-gray-700">Supporting Evidence</h3>

		{#if evidenceItems.length === 0}
			<p class="mb-4 text-gray-500 italic">No supporting evidence added yet.</p>
		{:else}
			<ul class="list-disc space-y-2 pl-6">
				{#each evidenceItems as evidence (evidence.id)}
					<li>
						{#if editingEvidenceId === evidence.id}
							<div class="mt-1 flex items-center">
								<input
									type="text"
									bind:value={editEvidenceContent}
									class="mr-2 flex-1 rounded-md border-gray-300"
									placeholder="Evidence content"
								/>
								<button
									onclick={() => saveEditEvidence(evidence.id)}
									class="mr-1 rounded bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-700"
									disabled={!editEvidenceContent.trim()}
								>
									Save
								</button>
								<button
									onclick={cancelEditEvidence}
									class="rounded bg-gray-200 px-2 py-1 text-xs text-gray-700 hover:bg-gray-300"
								>
									Cancel
								</button>
							</div>
						{:else}
							<div class="group flex items-start">
								<span class="flex-1">{evidence.content}</span>
								{#if !readOnly}
									<div class="ml-2 hidden space-x-2 group-hover:flex">
										<button
											onclick={() => startEditEvidence(evidence)}
											class="text-xs text-indigo-600 hover:text-indigo-800"
										>
											Edit
										</button>
										<button
											onclick={() => handleDeleteEvidence(evidence.id)}
											class="text-xs text-red-600 hover:text-red-800"
										>
											Delete
										</button>
									</div>
								{/if}
							</div>
						{/if}
					</li>
				{/each}
			</ul>
		{/if}

		{#if !readOnly}
			<div class="mt-4">
				<div class="flex items-center">
					<input
						id="new-evidence-input"
						type="text"
						bind:value={newEvidenceContent}
						class="flex-1 rounded-md border-gray-300"
						placeholder="Add supporting evidence"
					/>
					<button
						onclick={handleAddEvidence}
						class="ml-2 rounded bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700 disabled:opacity-50"
						disabled={!newEvidenceContent.trim()}
					>
						Add
					</button>
				</div>
			</div>
		{/if}
	</div>
{/if}
