<script lang="ts">
	import CameraCapture from '$lib/components/CameraCapture.svelte';

	// State
	let showCamera = $state(false);
	let capturedPhoto = $state<{ url: string } | null>(null);
	let cameraStatus = $state('Not initialized');
	let showAlert = $state(false);
	let alertMessage = $state('');

	// Handle photo capture
	function handleCapture(blob: Blob, url: string) {
		console.log('Photo captured:', { blob, url });
		capturedPhoto = { url: url };
		showCamera = false;
		cameraStatus = 'Photo captured successfully';
	}

	// Handle errors
	function handleError(error: string) {
		console.error('Camera error:', error);
		cameraStatus = `Error: ${error}`;
		showAlert = true;
		alertMessage = `Camera error: ${error}`;
		showCamera = false;
	}

	// Show camera modal
	function openCamera() {
		capturedPhoto = null;
		cameraStatus = 'Opening camera...';
		showCamera = true;
	}

	// Camera close handler
	function handleClose() {
		showCamera = false;
		cameraStatus = 'Camera closed';
	}

	// Close alert
	function closeAlert() {
		showAlert = false;
	}
</script>

<div class="container mx-auto p-4">
	<h1 class="mb-6 text-2xl font-bold">Camera Test Page</h1>

	{#if showAlert}
		<div class="mb-4 rounded-md bg-red-50 p-4">
			<div class="flex">
				<div class="flex-shrink-0">
					<svg
						class="h-5 w-5 text-red-400"
						viewBox="0 0 20 20"
						fill="currentColor"
						aria-hidden="true"
					>
						<path
							fill-rule="evenodd"
							d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
							clip-rule="evenodd"
						/>
					</svg>
				</div>
				<div class="ml-3">
					<p class="text-sm font-medium text-red-800">{alertMessage}</p>
				</div>
				<div class="ml-auto pl-3">
					<div class="-mx-1.5 -my-1.5">
						<button
							onclick={closeAlert}
							type="button"
							class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100 focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50 focus:outline-none"
						>
							<span class="sr-only">Dismiss</span>
							<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
								<path
									d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"
								/>
							</svg>
						</button>
					</div>
				</div>
			</div>
		</div>
	{/if}

	<div class="mb-8">
		<h2 class="mb-4 text-xl font-semibold">Test Camera</h2>

		<div class="flex flex-col gap-4">
			<div>
				<button
					class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
					onclick={openCamera}
				>
					Open Camera
				</button>
				<p class="mt-2 text-sm text-gray-600">Camera status: {cameraStatus}</p>
			</div>

			{#if capturedPhoto}
				<div class="mt-4">
					<h3 class="mb-2 text-lg font-medium">Captured Photo:</h3>
					<div class="overflow-hidden rounded-lg border border-gray-300">
						<img src={capturedPhoto.url} alt="Captured photo" class="h-64 w-64 object-cover" />
					</div>
				</div>
			{/if}
		</div>
	</div>

	<div class="mb-8">
		<h2 class="mb-4 text-xl font-semibold">Troubleshooting Tips</h2>
		<div class="rounded border border-gray-200 bg-gray-50 p-4">
			<ul class="list-inside list-disc space-y-2">
				<li>Make sure your browser has permission to access the camera</li>
				<li>Try using Chrome or Firefox for the best camera compatibility</li>
				<li>Some browsers only allow camera access on HTTPS sites</li>
				<li>Close other applications that might be using your camera</li>
				<li>Try refreshing the page if the camera isn't working</li>
			</ul>
		</div>
	</div>
</div>

{#if showCamera}
	<CameraCapture oncapture={handleCapture} onerror={handleError} onclose={handleClose} />
{/if}
