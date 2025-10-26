<script lang="ts">
	import { onMount } from 'svelte';

	onMount(() => {
		// Scroll to top when page loads
		window.scrollTo({ top: 0, behavior: 'instant' });
	});
</script>

<svelte:head>
	<title>Privacy Policy - CV Builder</title>
	<meta
		name="description"
		content="Privacy Policy for CV Builder - Learn how we collect, use, and protect your personal information."
	/>
</svelte:head>

<div class="min-h-screen bg-white py-12">
	<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
		<div class="mb-8">
			<h1 class="text-3xl font-bold text-gray-900">Privacy Policy</h1>
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
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Introduction</h2>
				<p class="mb-4 text-gray-700">
					CV Builder ("we", "our", or "us") is committed to protecting your privacy. This Privacy
					Policy explains how we collect, use, disclose, and safeguard your information when you use
					our CV building service.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Information We Collect</h2>
				<div class="space-y-4">
					<h3 class="text-xl font-semibold text-gray-800">Personal Information</h3>
					<p class="text-gray-700">
						When you create an account with CV Builder, we collect information that you provide
						directly, including:
					</p>
					<ul class="list-disc space-y-2 pl-6 text-gray-700">
						<li>Name, email address, and phone number</li>
						<li>Professional information (work experience, education, skills)</li>
						<li>Profile photo (if you choose to upload one)</li>
						<li>Biography and additional CV details</li>
					</ul>

					<h3 class="mt-6 text-xl font-semibold text-gray-800">
						Automatically Collected Information
					</h3>
					<p class="text-gray-700">
						When you visit our website, we automatically collect certain information about your
						device, including:
					</p>
					<ul class="list-disc space-y-2 pl-6 text-gray-700">
						<li>Device information (browser type, device type, operating system)</li>
						<li>Usage data (pages visited, time spent on site)</li>
						<li>IP address and general location information</li>
						<li>Referrer information</li>
					</ul>
				</div>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">How We Use Your Information</h2>
				<p class="mb-4 text-gray-700">We use the information we collect to:</p>
				<ul class="list-disc space-y-2 pl-6 text-gray-700">
					<li>Create and maintain your CV profile</li>
					<li>Provide, operate, and improve our CV builder service</li>
					<li>Generate PDF downloads of your CV</li>
					<li>Share your CV via online links</li>
					<li>Analyze usage patterns to improve our service</li>
					<li>Send important service notifications</li>
					<li>Respond to your inquiries and provide customer support</li>
				</ul>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Cookies and Tracking Technologies</h2>
				<p class="mb-4 text-gray-700">
					We use cookies and similar tracking technologies to track activity on our website and hold
					certain information. Cookies are files with a small amount of data that may include an
					anonymous unique identifier.
				</p>
				<p class="mb-4 text-gray-700">We use cookies for:</p>
				<ul class="list-disc space-y-2 pl-6 text-gray-700">
					<li><strong>Essential Cookies:</strong> Required for authentication and security</li>
					<li>
						<strong>Analytics Cookies:</strong> Help us understand how visitors interact with our website
					</li>
					<li><strong>Functional Cookies:</strong> Remember your preferences and settings</li>
				</ul>
				<p class="mt-4 text-gray-700">
					You can instruct your browser to refuse all cookies or to indicate when a cookie is being
					sent. However, if you do not accept cookies, you may not be able to use some portions of
					our service.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Data Storage and Security</h2>
				<p class="mb-4 text-gray-700">
					Your data is stored securely in Supabase, a PostgreSQL-based database service. We
					implement appropriate technical and organizational measures to protect your personal
					information against unauthorized access, alteration, disclosure, or destruction.
				</p>
				<p class="text-gray-700">
					While we strive to use commercially acceptable means to protect your personal information,
					no method of transmission over the Internet or method of electronic storage is 100%
					secure.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Your Rights</h2>
				<p class="mb-4 text-gray-700">
					Under applicable data protection laws, you have the right to:
				</p>
				<ul class="list-disc space-y-2 pl-6 text-gray-700">
					<li><strong>Access:</strong> Request copies of your personal data</li>
					<li>
						<strong>Rectification:</strong> Request correction of inaccurate or incomplete data
					</li>
					<li><strong>Erasure:</strong> Request deletion of your personal data</li>
					<li>
						<strong>Restriction:</strong> Request restriction of processing of your personal data
					</li>
					<li>
						<strong>Data Portability:</strong> Request transfer of your data to another service
					</li>
					<li><strong>Objection:</strong> Object to processing of your personal data</li>
				</ul>
				<p class="mt-4 text-gray-700">
					To exercise these rights, please contact us using the information provided in the Contact
					section below.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Data Retention</h2>
				<p class="text-gray-700">
					We retain your personal information for as long as your account is active or as needed to
					provide you services. If you delete your account, we will delete or anonymize your
					personal information within 30 days, except where we are required to retain it for legal
					or regulatory purposes.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Children's Privacy</h2>
				<p class="text-gray-700">
					Our service is not intended for individuals under the age of 16. We do not knowingly
					collect personal information from children under 16. If we become aware that we have
					collected personal information from a child under 16, we will take steps to delete such
					information.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">International Data Transfers</h2>
				<p class="text-gray-700">
					Your information may be transferred to and maintained on computers located outside of your
					state, province, country, or other governmental jurisdiction where data protection laws
					may differ from those in your jurisdiction.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Changes to This Privacy Policy</h2>
				<p class="text-gray-700">
					We may update our Privacy Policy from time to time. We will notify you of any changes by
					posting the new Privacy Policy on this page and updating the "Last updated" date. You are
					advised to review this Privacy Policy periodically for any changes.
				</p>
			</section>

			<section class="mb-8">
				<h2 class="mb-4 text-2xl font-semibold text-gray-900">Contact Us</h2>
				<p class="mb-2 text-gray-700">
					If you have any questions about this Privacy Policy, please contact us:
				</p>
				<div class="text-gray-700">
					<p>Email: wellis321@msn.com</p>
					<p class="mt-2">
						By using this service, you acknowledge that you have read and understood this Privacy
						Policy.
					</p>
				</div>
			</section>
		</div>

		<div class="mt-12 border-t border-gray-200 pt-8">
			<a href="/" class="font-medium text-blue-600 hover:text-blue-800">← Back to Home</a>
		</div>
	</div>
</div>
