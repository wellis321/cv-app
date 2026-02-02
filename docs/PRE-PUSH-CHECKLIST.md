# Pre-push & production checklist

## Sensitive data (safe to push if you follow this)

- **`.env`** – Ignored by `.gitignore`. **Never** add it. It holds DB credentials, Stripe keys, AI API keys. Before first push run: `git status` and confirm `.env` is not listed.
- **`.env.example`** – Safe to commit (no real secrets). Use it as a template; copy to `.env` locally and fill in values.
- **`.cursor/`** – Ignored. Debug logs stay local.
- **`/storage/*`** – Ignored. User uploads stay off the repo.
- **`database/create_account_*.sql`** – Ignored (may contain sensitive data).
- **`scripts/exports/*.json`** – Ignored (user data).

No API keys or passwords are hardcoded in the repo; config uses `env()` from `.env`.

## Before production

1. **Set `APP_ENV=production`** in `.env` on the server. This turns off `DEBUG`, hides errors from users, and enables secure cookies.
2. **Use HTTPS** in production and set `APP_URL` to your real URL (e.g. `https://yourdomain.com`).
3. **Restrict `storage/` and `logs/`** so they are not web-accessible (only your app should read/write).
4. **Remove or gate debug instrumentation** – All unconditional `file_put_contents(..., debug.log)` calls have been removed so the app does not write debug/PII to disk in production.

## Security (already in place)

- CSRF on POST forms and API calls.
- Auth checks (`requireAuth()`, `getUserId()`) on protected pages and APIs.
- Queries use PDO prepared statements (no raw SQL concatenation).
- Passwords hashed with `password_hash()`.
- Session cookies: `httponly`, `samesite`, and `secure` in production.

## Quick check before pushing

```bash
git status          # Ensure .env and .cursor/ are not staged
git diff --cached   # Review what will be committed
```

If `.env` ever appeared in `git status`, do **not** commit it. If it was committed in the past, rotate all secrets in that file and remove it from history (e.g. `git filter-branch` or BFG).
