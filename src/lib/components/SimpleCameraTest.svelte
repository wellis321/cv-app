<script lang="ts">
	import { onMount, onDestroy } from 'svelte';

	// State
	let videoElement: HTMLVideoElement;
	let status = $state('Not started');
	let error = $state<string | null>(null);
	let stream: MediaStream | null = null;

	// Initialize camera on mount
	onMount(() => {
		console.log('SimpleCameraTest mounted');
		setTimeout(() => {
			initCamera();
		}, 100);

		// Clean up on component destruction
		return () => {
			if (stream) {
				stream.getTracks().forEach((track) => track.stop());
			}
		};
	});

	async function initCamera() {
		try {
			status = 'Initializing camera...';
			error = null;

			if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
				throw new Error('Camera API not supported in this browser');
			}

			// Simple camera constraints
			const constraints = {
				audio: false,
				video: true
			};

			// Get camera stream
			stream = await navigator.mediaDevices.getUserMedia(constraints);

			// Connect stream to video element
			if (videoElement) {
				videoElement.srcObject = stream;
				status = 'Camera active';
			} else {
				throw new Error('Video element not found');
			}
		} catch (err) {
			console.error('Camera error:', err);
			status = 'Error';
			error = err instanceof Error ? err.message : 'Unknown error accessing camera';
		}
	}

	function restartCamera() {
		// Stop existing stream
		if (stream) {
			stream.getTracks().forEach((track) => track.stop());
			stream = null;
		}

		// Reinitialize
		initCamera();
	}
</script>

<div class="space-y-4">
	<div class="overflow-hidden rounded bg-black" style="height: 240px">
		<video bind:this={videoElement} autoplay playsinline muted class="h-full w-full object-contain"
		></video>
	</div>

	<div class="flex items-center justify-between">
		<div>
			<span class="text-sm font-medium">Status: </span>
			<span class={error ? 'text-red-500' : 'text-green-500'}>{status}</span>
			{#if error}
				<p class="mt-1 text-sm text-red-500">{error}</p>
			{/if}
		</div>

		<button class="rounded bg-blue-500 px-3 py-1 text-sm text-white" onclick={restartCamera}>
			Restart Camera
		</button>
	</div>
</div>
