# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

B2B CV Builder web application in vanilla PHP. Users create professional CVs with multiple sections (work experience, education, projects, skills, certifications, memberships, interests, professional summary, qualification equivalence), preview them at `/cv/@username`, and export to PDF. Features include Stripe subscriptions, AI-powered CV rewriting/assessment, CV templates, CV variants, cover letters, and job application tracking.

## Development Commands

```bash
# Install PHP dependencies (Twig templating)
composer install

# Run local development server
php -S localhost:8000

# Build Tailwind CSS (run after changing templates/views)
npm run build:css          # Full site CSS
npm run build:css:home     # Homepage-only CSS (smaller, ~38KB vs ~113KB)
npm run build:css:all      # Both (recommended for deploy)

# Install PDF generator dependencies (in scripts/)
cd scripts && npm install

# Generate PDF (test)
node scripts/generate-pdf.js https://example.com ./output.pdf

# Academic template PDF fonts (Liberation Serif - matches Georgia/Times in preview)
./scripts/download-academic-fonts.sh

# Demo/showcase account (noreply@simple-job-tracker.com)
php scripts/create-example-cv.php              # Creates example CV (marketing professional)
php scripts/create-demo-jobs.php               # Adds demo jobs (run after create-example-cv)
php scripts/create-example-cv.php --with-demo-jobs   # Both in one command
# Login: noreply@simple-job-tracker.com / ExampleAccount123!

# Database setup
mysql -u user -p database_name < database/mysql_schema.sql

# Apply migrations (run in chronological order)
mysql -u user -p database_name < database/YYYYMMDD_description.sql
```

## Syncing to the Electron desktop app

The desktop app (simple-cv-builder-desktop) keeps a copy of this web app in its `app/` folder. After making changes here that should appear in the desktop app, run the sync script from this repo:

```bash
# From b2b-cv-app root. Set path to the desktop repo (env or argument):
SIMPLE_CV_DESKTOP_PATH=/path/to/simple-cv-builder-desktop npm run sync-to-desktop
# or:
npm run sync-to-desktop -- /path/to/simple-cv-builder-desktop
```

The script copies `php/`, `views/`, `api/`, `js/`, `static/`, `templates/`, `resources/`, root `*.php`, and `composer.json` into the desktop `app/` folder. It **never overwrites** the desktop’s `app/php/config.php` or `app/php/database.php` (those are desktop-specific for SQLite and DESKTOP_MODE). If you change config or database logic in this repo, merge those changes manually into the desktop versions.

## Architecture

### Request Flow
1. Apache `.htaccess` routes: storage → `api/storage-proxy.php`, CV → `cv.php?username=X`, else → `index.php`
2. All pages require `php/helpers.php` (loads core: config, database, auth, security, storage, utils, cv-data, subscriptions, stripe)
3. Template system via `partial()` function from `views/partials/`
4. API endpoints return JSON

### Core Files (`php/`)
- **config.php**: Environment variables, constants, `.env` loader
- **database.php**: Database singleton with PDO wrapper, access via `db()`
- **auth.php**: Register, login, logout, password reset, email verification
- **security.php**: CSRF tokens, input sanitization, validation, rate limiting
- **helpers.php**: Template rendering (`partial()`, `render()`), flash messages
- **ai-service.php**: AI abstraction (Ollama, OpenAI, Anthropic, Gemini, Grok)
- **cv-templates.php**: CV template management (Twig-based)
- **cv-variants.php**: CV variant creation and management
- **cv-data.php**: Fetch CV data for display/export
- **cover-letters.php**: Cover letter generation and management
- **job-applications.php**: Job application tracking
- **subscriptions.php**: Subscription management and feature gating
- **stripe.php**: Stripe API integration
- **twig-template-service.php**: Twig template rendering service

### Database
- MySQL with PDO prepared statements
- Helper methods: `query()`, `fetchOne()`, `fetchAll()`, `insert()`, `update()`, `delete()`
- Transaction support: `beginTransaction()`, `commit()`, `rollback()`
- Main tables: `profiles` (UUID primary key), `work_experience`, `education`, `projects`, `skills`, `cv_templates`, `cv_variants`, `cover_letters`, `job_applications`
- All related tables use `profile_id` foreign key with CASCADE delete

### Authentication
- Session-based (`$_SESSION['user_id']`)
- Use `requireAuth()` at top of protected pages
- Use `isLoggedIn()` / `getCurrentUser()` / `getUserId()` for user context
- CSRF required for all POST: `csrfToken()` in forms, verify with `verifyCsrfToken()`
- Email verification required before login

### AI Features
AI service supports multiple providers configured per-user or globally:
- **Browser AI**: Default, runs in browser (no API keys needed)
- **Ollama**: Local self-hosted (configurable URL/model)
- **Cloud APIs**: OpenAI, Anthropic, Gemini, Grok (API keys in user settings or .env)

Features: AI CV rewriting for job applications, CV quality assessment, cover letter generation

## Key Patterns

### Page Structure
```php
<?php
require_once __DIR__ . '/php/helpers.php';
requireAuth(); // if login required

if (isPost()) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        setFlash('error', 'Invalid security token');
        redirect('/current-page.php');
    }
    // Process form...
}

$userId = getUserId();
$data = db()->fetchAll("SELECT * FROM table WHERE profile_id = ?", [$userId]);
?>
<!DOCTYPE html>
<html lang="en">
<head><?php partial('head', ['pageTitle' => 'Title']); ?></head>
<body>
    <?php partial('header'); ?>
    <main><!-- content --></main>
    <?php partial('footer'); ?>
</body>
</html>
```

### API Endpoint Structure
```php
<?php
require_once __DIR__ . '/../php/helpers.php';
header('Content-Type: application/json');

if (!isLoggedIn()) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit; }
if (!isPost()) { http_response_code(405); echo json_encode(['error' => 'Method not allowed']); exit; }
if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) { http_response_code(403); echo json_encode(['error' => 'Invalid CSRF token']); exit; }

try {
    $userId = getUserId();
    // Operations...
    echo json_encode(['success' => true, 'data' => $result]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
```

### Database Operations
```php
$user = db()->fetchOne("SELECT * FROM profiles WHERE id = ?", [$userId]);
$items = db()->fetchAll("SELECT * FROM table WHERE profile_id = ? ORDER BY created_at DESC", [$userId]);
$id = db()->insert('table_name', ['id' => generateUuid(), 'profile_id' => $userId, 'field' => $value]);
db()->update('table_name', ['field' => $value], 'id = ?', [$id]);
db()->delete('table_name', 'id = ? AND profile_id = ?', [$id, $userId]);
```

## URL Routing
- Home: `/` or `/index.php`
- Dashboard: `/dashboard.php`
- Public CV: `/cv/@username`
- Profile editing: `/profile.php`
- Section editing: `/work-experience.php`, `/education.php`, etc.
- CV Variants: `/cv-variants.php`, `/cv-variants/rewrite.php`
- Job Applications: `/job-applications.php`
- Cover Letters: `/cover-letters.php`
- CV Quality: `/cv-quality.php`

## Environment Setup
1. Copy and configure `.env` file (see `php/config.php` for required variables)
2. Run `composer install`
3. Import database: `mysql -u user -p database_name < database/mysql_schema.sql`
4. Apply migrations from `database/` in chronological order
5. Create storage: `mkdir -p storage/uploads && chmod 755 storage/uploads`
6. For PDF generation: `cd scripts && npm install`
7. Enable Apache mod_rewrite or configure Nginx equivalent

## Important Notes

- **Security**: Always use `sanitizeInput()`, prepared statements, verify resource ownership with `profile_id = getUserId()` or `ownsResource()`
- **Migrated from SvelteKit**: Svelte rules in `.cursor/rules/svelte_standards.mdc` are NOT applicable
- **No automated tests**: Manual testing required
- **Debugging**: Set `APP_ENV=development` in `.env` for error display
- **Documentation**: See `docs/` for security audits, Stripe setup, production checklists
