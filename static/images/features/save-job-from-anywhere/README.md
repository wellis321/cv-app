# Images for Save Job From Anywhere feature page

Replace the placeholders with your own images for the feature page (`/save-job-from-anywhere.php`).

**Current placeholders:**

| File | Where it appears |
|------|------------------|
| `hero-bg-placeholder.svg` | Hero section **background** (behind the gradient overlay). Replace with your hero image (e.g. job listing or extension in use). |
| `temporary-placeholder.svg` | Step 1, Step 2, and Step 3 images in “How it works”. Replace with screenshots. |

**To use your own images:**

- **Hero background:** Replace `hero-bg-placeholder.svg` with your image (e.g. `hero-bg.jpg`) and update the `background-image` URL in `save-job-from-anywhere.php` (hero section), or use the same filename `hero-bg-placeholder.svg` for a different image type by swapping the file.
- **Step screenshots:** Add `step1-get-token.png`, `step2-extension-options.png`, `step3-save-job.png` (or similar) and update the three step image `src` attributes in `save-job-from-anywhere.php` to point to them.
