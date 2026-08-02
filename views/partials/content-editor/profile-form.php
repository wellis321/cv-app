<?php
/**
 * Profile Section Form Partial
 * Identity fields only (name/contact/photo). Username, password, and job-reminder
 * preferences stay on profile.php as "Account" settings - not migrated here.
 */

$profile = db()->fetchOne("SELECT * FROM profiles WHERE id = ?", [$userId]);
$photoUrl = $profile['photo_url'] ?? null;
?>
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Profile</h1>
        <a href="/profile.php" class="text-sm text-gray-600 hover:text-gray-900">Username, password &amp; account settings &rarr;</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Your Details</h2>

        <div class="flex items-start gap-6 mb-6 pb-6 border-b border-gray-200" id="profile-photo-section">
            <div class="flex-shrink-0">
                <?php if (!empty($photoUrl)): ?>
                    <?php
                    $photoResponsiveData = $profile['photo_responsive'] ?? null;
                    $photoImgAttrs = getResponsiveImageAttributes($photoResponsiveData, $photoUrl, 'full');
                    ?>
                    <img id="profile-photo-preview"
                         src="<?php echo e($photoImgAttrs['src']); ?>"
                         <?php if (!empty($photoImgAttrs['srcset'])): ?>
                             srcset="<?php echo e($photoImgAttrs['srcset']); ?>"
                             sizes="<?php echo e($photoImgAttrs['sizes']); ?>"
                         <?php endif; ?>
                         alt="Profile Photo"
                         class="w-24 h-24 rounded-full object-cover border-4 border-gray-200"
                         loading="lazy" width="96" height="96">
                <?php else: ?>
                    <div id="profile-photo-preview" class="w-24 h-24 rounded-full bg-gray-200 border-4 border-gray-200 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex-1">
                <div class="flex gap-3 flex-wrap" id="profile-photo-buttons">
                    <label class="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 inline-flex items-center gap-2">
                        <input type="file" id="profile-photo-capture" accept="image/*" capture="user" class="hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Take Photo</span>
                    </label>
                    <label class="cursor-pointer bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 inline-flex items-center gap-2">
                        <input type="file" id="profile-photo-upload" accept="image/*" class="hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <span>Upload Photo</span>
                    </label>
                    <?php if (!empty($photoUrl)): ?>
                        <button type="button" id="profile-photo-delete" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <span>Delete Photo</span>
                        </button>
                    <?php endif; ?>
                </div>
                <p class="mt-2 text-sm text-gray-500">Upload a professional photo. Supported formats: JPG, PNG, GIF, WebP (max 5MB)</p>
                <div id="profile-photo-status" class="mt-2"></div>
            </div>
        </div>

        <form method="POST" data-section-form data-form-type="update">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="section_id" value="profile">

            <div class="flex flex-wrap gap-6 mb-6">
                <div class="w-full sm:w-64">
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo e($profile['full_name'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="w-full sm:w-44">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo e($profile['phone'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="w-full sm:w-56">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" id="location" name="location" value="<?php echo e($profile['location'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-1">LinkedIn URL</label>
                <input type="url" id="linkedin_url" name="linkedin_url" value="<?php echo e($profile['linkedin_url'] ?? ''); ?>" class="w-full sm:max-w-md px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Strapline</label>
                <textarea id="bio" name="bio" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"><?php echo e($profile['bio'] ?? ''); ?></textarea>
                <p class="mt-1 text-xs text-gray-500">A single line of text that appears on your CV</p>
            </div>

            <div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">Save</button>
            </div>
        </form>
    </div>
</div>
