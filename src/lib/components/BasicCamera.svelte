<script lang="ts">
	import { onMount, onDestroy } from 'svelte';

	// Props
	const { facingMode = 'user' } = $props<{ facingMode?: 'user' | 'environment' }>();

	// State
	let videoElement: HTMLVideoElement;
	let mediaStream: MediaStream | null = null;
	let status = $state('Not started');
	let error = $state<string | null>(null);
	let currentFacingMode = $state<'user' | 'environment'>(facingMode);

	// Initialize camera on mount
	onMount(() => {
		console.log('BasicCamera mounted');
		setTimeout(() => {
			startCamera();
		}, 100);

		return () => {
			stopCamera();
		};
	});

	// Handle facing mode changes
	$effect(() => {
		if (mediaStream) {
			// Restart camera if facing mode changes
			startCamera();
		}
	});

	async function startCamera() {
		try {
			// Stop any existing stream first
			stopCamera();

			status = 'Initializing camera...';
			error = null;

			if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
				throw new Error('Camera API not supported in this browser');
			}

			// Camera constraints
			const constraints = {
				audio: false,
				video: {
					facingMode: currentFacingMode
				}
			};

			// Get camera stream
			mediaStream = await navigator.mediaDevices.getUserMedia(constraints);

			// Connect stream to video element
			if (videoElement) {
				videoElement.srcObject = mediaStream;
				status = 'Camera active';

				// Explicitly play the video
				try {
					await videoElement.play();
					console.log('Video playback started');
				} catch (playErr) {
					console.error('Error playing video:', playErr);
					// Try again after a moment
					setTimeout(async () => {
						try {
							await videoElement.play();
							console.log('Video playback started on second attempt');
						} catch (err) {
							console.error('Error on second play attempt:', err);
						}
					}, 500);
				}
			} else {
				throw new Error('Video element not found');
			}
		} catch (err) {
			console.error('Camera error:', err);
			status = 'Error';
			error = err instanceof Error ? err.message : 'Unknown error accessing camera';
		}
	}

	function stopCamera() {
		// Stop existing stream
		if (mediaStream) {
			mediaStream.getTracks().forEach((track) => track.stop());
			mediaStream = null;
		}

		// Clear video source
		if (videoElement) {
			videoElement.srcObject = null;
		}
	}

	function switchCamera() {
		currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
		startCamera();
	}
</script>

<div class="space-y-4">
	<div class="overflow-hidden rounded border-2 border-gray-300 bg-black" style="height: 240px">
		<video bind:this={videoElement} autoplay playsinline muted class="h-full w-full object-cover"
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

		<div class="flex gap-2">
			<button class="rounded bg-blue-500 px-3 py-1 text-sm text-white" onclick={startCamera}>
				Restart Camera
			</button>

			<button class="rounded bg-gray-200 px-3 py-1 text-sm text-gray-800" onclick={switchCamera}>
				Switch Camera ({currentFacingMode})
			</button>
		</div>
	</div>
</div>
