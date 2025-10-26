<script lang="ts">
	import { onMount } from 'svelte';

	onMount(() => {
		// Scroll to top when page loads
		window.scrollTo({ top: 0, behavior: 'instant' });
	});
</script>

<svelte:head>
	<title>Terms of Service - CV Builder</title>
	<meta
		name="description"
		content="Terms of Service for CV Builder - Read our terms and conditions of use."
	/>
</svelte:head>

<div class="min-h-screen bg-white py-12">
	<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
		<div class="mb-8">
			<h1 class="text-3xl font-bold text-gray-900">Terms of Service</h1>
			<p class="mt-2 text-sm text-gray-600">
				Last updated: {new Date().toLocaleDateString('en-GB', {
					day: 'numeric',
					month: 'long',
					year: 'numeric'
				})}
			</p>
		</div>

		<div class="prose max-w-none">
			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Acceptance of Terms</h2>
				<p class="text-gray-700">
					By accessing and using CV Builder, you accept and agree to be bound by the terms and
					provision of this agreement. If you do not agree to abide by the above, please do not use
					this service.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Use License</h2>
				<p class="mb-4 text-gray-700">
					Permission is granted to temporarily use CV Builder for personal CV creation. This is the
					grant of a license, not a transfer of title, and under this license you may not:
				</p>
				<ul class="list-disc space-y-2 pl-6 text-gray-700">
					<li>modify or copy the materials</li>
					<li>
						use the materials for any commercial purpose, or for any public display (commercial or
						non-commercial)
					</li>
					<li>attempt to decompile or reverse engineer any software contained on CV Builder</li>
					<li>remove any copyright or other proprietary notations from the materials</li>
					<li>
						transfer the materials to another person or "mirror" the materials on any other server
					</li>
				</ul>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">User Account</h2>
				<p class="mb-4 text-gray-700">
					When you create an account with us, you must provide information that is accurate,
					complete, and current at all times. You are responsible for safeguarding the password and
					for all activities that occur under your account.
				</p>
				<p class="text-gray-700">
					You agree not to disclose your password to any third party. You must notify us immediately
					upon becoming aware of any breach of security or unauthorized use of your account.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Content</h2>
				<p class="mb-4 text-gray-700">
					You retain all ownership rights to the content you create using CV Builder. You grant us a
					license to use, store, and display your content solely for the purpose of providing and
					improving our service.
				</p>
				<p class="mb-4 text-gray-700">
					You are solely responsible for the content you create and share. You agree not to upload,
					post, or transmit any content that:
				</p>
				<ul class="list-disc space-y-2 pl-6 text-gray-700">
					<li>
						is illegal, harmful, threatening, abusive, harassing, defamatory, vulgar, obscene, or
						otherwise objectionable
					</li>
					<li>
						infringes upon any patent, trademark, trade secret, copyright, or other proprietary
						rights of any party
					</li>
					<li>
						contains software viruses or any other computer code designed to interrupt, destroy, or
						limit the functionality of any computer software or hardware
					</li>
					<li>
						impersonates any person or entity, or falsely states or misrepresents your affiliation
						with a person or entity
					</li>
				</ul>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Disclaimers</h2>
				<p class="mb-4 text-gray-700">
					The materials on CV Builder are provided on an 'as is' basis. CV Builder makes no
					warranties, expressed or implied, and hereby disclaims and negates all other warranties
					including without limitation, implied warranties or conditions of merchantability, fitness
					for a particular purpose, or non-infringement of intellectual property or other violation
					of rights.
				</p>
				<p class="text-gray-700">
					Further, CV Builder does not warrant or make any representations concerning the accuracy,
					likely results, or reliability of the use of the materials on its website or otherwise
					relating to such materials or on any sites linked to this site.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Limitations</h2>
				<p class="text-gray-700">
					In no event shall CV Builder or its suppliers be liable for any damages (including,
					without limitation, damages for loss of data or profit, or due to business interruption)
					arising out of the use or inability to use the materials on CV Builder, even if CV Builder
					or a CV Builder authorized representative has been notified orally or in writing of the
					possibility of such damage.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Accuracy of Materials</h2>
				<p class="text-gray-700">
					The materials appearing on CV Builder could include technical, typographical, or
					photographic errors. CV Builder does not warrant that any of the materials on its website
					are accurate, complete, or current. CV Builder may make changes to the materials contained
					on its website at any time without notice.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Links</h2>
				<p class="text-gray-700">
					CV Builder has not reviewed all of the sites linked to our website and is not responsible
					for the contents of any such linked site. The inclusion of any link does not imply
					endorsement by CV Builder of the site. Use of any such linked website is at the user's own
					risk.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Modifications</h2>
				<p class="text-gray-700">
					CV Builder may revise these terms of service for its website at any time without notice.
					By using this website you are agreeing to be bound by the then current version of these
					terms of service.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Termination</h2>
				<p class="text-gray-700">
					We may terminate or suspend your account and bar access to the Service immediately,
					without prior notice or liability, under our sole discretion, for any reason whatsoever
					and without limitation, including but not limited to a breach of the Terms.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Governing Law</h2>
				<p class="text-gray-700">
					These terms and conditions are governed by and construed in accordance with the laws and
					you irrevocably submit to the exclusive jurisdiction of the courts in that location.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Contact Information</h2>
				<p class="mb-2 text-gray-700">
					If you have any questions about these Terms of Service, please contact us:
				</p>
				<div class="text-gray-700">
					<p>Email: wellis321@msn.com</p>
				</div>
			</section>
		</div>

		<div class="mt-12 border-t border-gray-200 pt-8">
			<a href="/" class="font-medium text-blue-600 hover:text-blue-800">← Back to Home</a>
		</div>
	</div>
</div>
