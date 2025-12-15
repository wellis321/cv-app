# Files and Folders Safe to Delete

Since we've converted to pure PHP, the following SvelteKit/Node.js files are no longer needed:

## ✅ Safe to Delete Immediately

### SvelteKit Source Code
- `src/` - Entire SvelteKit application source
- `node_modules/` - Node.js dependencies (can be regenerated if needed)

### Configuration Files
- `package.json` - Node.js package configuration
- `package-lock.json` - Node.js dependency lock file
- `svelte.config.js` - SvelteKit configuration
- `vite.config.ts` - Vite build configuration
- `tsconfig.json` - TypeScript configuration

### Deployment Configs
- `vercel.json` - Vercel deployment configuration

### Database Migrations (Already Converted)
- `supabase/` - Supabase PostgreSQL migrations (converted to MySQL schema)
- `migrations/` - Additional Supabase migrations if they exist

### Build/Output Directories (if they exist)
- `.svelte-kit/` - SvelteKit build cache
- `build/` - Build output
- `.vercel/` - Vercel build cache

## ⚠️ Consider Before Deleting

### Static Assets
- `static/` - Contains favicon and images
  - **Decision**: Keep `static/images/` but can move to `public/` if preferred
  - Delete: `static/` if you've moved assets to a better location

### Documentation (Reference Only)
- `DEPLOYMENT.md` - Vercel deployment docs (not needed for PHP)
- `VERCEL_DEPLOYMENT.md` - Vercel-specific docs
- `STRIPE_SETUP.md` / `STRIPE_SETUP_INSTRUCTIONS.md` - Keep if using Stripe
- Other README files - Review and keep useful info, delete if obsolete

## 🔄 Update These

- `.gitignore` - Update to ignore PHP-specific files instead of Node.js

## Commands to Delete

```bash
# Delete SvelteKit source
rm -rf src/

# Delete Node.js files
rm -rf node_modules/
rm package.json package-lock.json

# Delete configuration files
rm svelte.config.js vite.config.ts tsconfig.json vercel.json

# Delete Supabase migrations (already converted)
rm -rf supabase/
rm -rf migrations/

# Delete build caches (if they exist)
rm -rf .svelte-kit/ build/ .vercel/

# Optional: Move static assets and delete static/
# mkdir -p public/images
# cp static/images/* public/images/ 2>/dev/null || true
# rm -rf static/
```
