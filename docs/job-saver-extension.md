# Save Job Browser Extension – User Guide

This extension lets you save the **current tab's URL and title** as a job in your Simple CV Builder job list **without leaving the page**. One click (popup or context menu) and the job is created in your account.

## How it works

1. **You're browsing a job page** (Indeed, LinkedIn, company career pages, or any job listing).
2. **Click the extension icon** or right‑click the page → **"Save job to Simple CV Builder"**.
3. The extension sends the page URL and title to Simple CV Builder using your **save token**.
4. The job is added to your list immediately. You can add details later in Job Applications or the content editor.

**No new tab, no copy‑paste.** The job appears in your list as soon as you're logged in on the site.

## Step 1: Get Your Save Token

1. **Log in** to Simple CV Builder.
2. Go to **My CV** → **Get save token** (or visit `/save-job-token.php`).
3. Click **Copy token** (or **Regenerate** if you want a new one).
4. **Keep this token safe** — you'll paste it into the extension in Step 3.

> **Security tip:** Treat your save token like a password. If it's ever exposed, regenerate it immediately.

## Step 2: Download and Install the Extension

### For Chrome/Edge/Brave (Chromium-based browsers)

1. **Download the extension:**
   - **Option 1:** Visit [Download Extension](/download-extension.php) on Simple CV Builder to download the extension as a ZIP file.
   - **Option 2:** If you have access to the Simple CV Builder codebase, use the `extension` folder directly.
   - Extract the ZIP file to a location you can remember (e.g., `Downloads/simple-cv-extension`).

2. **Open Chrome Extensions page:**
   - Type `chrome://extensions` in your address bar and press Enter.
   - Or go to **Menu** (three dots) → **Extensions** → **Manage extensions**.

3. **Enable Developer Mode:**
   - Toggle **Developer mode** ON (top right corner of the extensions page).

4. **Load the extension:**
   - Click **Load unpacked**.
   - Navigate to and select the `extension` folder (the one containing `manifest.json`).
   - Click **Select Folder**.

5. **Verify installation:**
   - You should see "Save job to Simple CV Builder" in your extensions list.
   - The extension icon should appear in your browser toolbar.

### For Firefox

1. **Download the extension for Firefox** — use **Download for Firefox** (not the Chrome download). Firefox always loads `manifest.json` from the extension folder and requires `background.scripts`; the Chrome manifest uses `background.service_worker` which Firefox rejects. The Firefox download packages the correct `manifest.json`.

2. **Extract the ZIP** to a folder (e.g. `Downloads/simple-cv-extension-firefox`).

3. **Open Firefox Add-ons page:**
   - Type `about:debugging` in your address bar.
   - Click **This Firefox** in the left sidebar.

4. **Load the extension:**
   - Click **Load Temporary Add-on...**.
   - Navigate to the extracted folder and select `manifest.json`.
   - Click **Open**.

> **Note:** Firefox requires the extension to be reloaded each time you restart Firefox. For a permanent installation, you'll need to package it as a `.xpi` file or publish it to Firefox Add-ons.

## Step 3: Configure the Extension

1. **Open extension options:**
   - **Chrome/Edge:** Click the extension icon in your toolbar → **Options**, or right‑click the extension icon → **Options**.
   - **Firefox:** The options page should open automatically, or click the extension icon → **Options**.

2. **Enter your settings:**
   - **Site URL:** 
     - Click **Production** for `https://simple-cv-builder.com`
     - Or click **Testing** for `https://lightcoral-raccoon-941077.hostingersite.com`
     - Or enter a custom URL (no trailing slash).
   - **Save token:** Paste the token you copied in Step 1.

3. **Save:**
   - Click **Save settings**.
   - You should see a confirmation message.

## Step 4: Use the Extension

### Method 1: Extension Icon
1. Navigate to any job listing page (Indeed, LinkedIn, company careers, etc.).
2. Click the **Simple CV Builder extension icon** in your browser toolbar.
3. Click **Save job** in the popup.
4. The job is added to your list immediately!

### Method 2: Right-Click Menu
1. Navigate to any job listing page.
2. **Right‑click** anywhere on the page.
3. Select **"Save job to Simple CV Builder"** from the context menu.
4. The job is added to your list immediately!

### After Saving
- The job appears in your **Job Applications** list with the page title and URL.
- You can add company name, closing date, notes, and other details later.
- The job is linked to your account via your save token.

## Troubleshooting

### Extension icon not showing
- **Chrome:** Check that the extension is enabled in `chrome://extensions`.
- **Firefox:** Check `about:addons` to ensure the extension is installed and enabled.
- Try refreshing the extensions page or restarting your browser.

### "Save job" button doesn't work
- **Check your token:** Make sure you've pasted your save token correctly in the extension options (no extra spaces).
- **Check Site URL:** Ensure the Site URL matches the Simple CV Builder site you're using (production vs testing).
- **Check you're logged in:** The extension requires you to be logged in to Simple CV Builder in another tab.
- **Regenerate token:** If it still doesn't work, regenerate your save token and update it in the extension options.

### Extension options won't save
- Make sure you've entered both **Site URL** and **Save token**.
- Check that the Site URL doesn't have a trailing slash (`/`).
- Try closing and reopening the options page.

### Job not appearing in my list
- **Check you're logged in:** Make sure you're logged in to Simple CV Builder.
- **Check the Site URL:** Ensure the extension is pointing to the correct Simple CV Builder site.
- **Check your token:** Verify your save token is correct and hasn't been regenerated.
- **Refresh your job list:** Try refreshing the Job Applications page.

### LinkedIn job titles showing as "LinkedIn"
- The extension automatically extracts job titles from LinkedIn job pages.
- If you see "LinkedIn" instead of the job title, the extraction may have failed. You can edit the job title manually in your job list.

## Security & Privacy

- **Your save token** is a long random secret tied to your account. Keep it private.
- **The extension only sends:**
  - Page URL
  - Page title
  - Optional closing date and priority (if you set them)
- **The extension does NOT:**
  - Read other page content
  - Access your browsing history
  - Send data to third parties
  - Store your password or login credentials

## Need Help?

- Visit the [Save Job From Anywhere](/save-job-from-anywhere.php) feature page.
- Check your [save token page](/save-job-token.php) for setup instructions.
- Contact support if you continue to have issues.

---

## Developer Notes

### Backend requirements

- **Migration**: `database/20250206_add_job_saver_token.sql` adds `profiles.job_saver_token`. Run it so tokens can be created and validated.
- **API**:
  - `GET /api/job-saver-token.php` – returns masked token (and ensures one exists).
  - `POST /api/job-saver-token.php` – `action=copy` returns full token; `action=regenerate` creates a new token and returns it.
  - `POST /api/quick-add-job.php` – accepts JSON `{ url, title?, closing_date?, priority? }` and `Authorization: Bearer <job_saver_token>`, creates the job for that user.

### Optional: Custom Icons

To give the extension a custom icon, add `icon16.png` (16×16) and `icon48.png` (48×48) in the `extension` folder and add to `manifest.json`:

```json
"action": {
  "default_popup": "popup.html",
  "default_icon": { "16": "icon16.png", "48": "icon48.png" },
  "default_title": "Save job to Simple CV Builder"
},
"icons": { "16": "icon16.png", "48": "icon48.png" }
```

Without these, Chrome uses the default extension icon.
