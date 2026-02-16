# Deploying B2B CV Builder to Hostinger via GitHub

## Database Setup

You have **two options** for setting up the database on Hostinger:

### Option A: Run SQL in phpMyAdmin (fresh/empty database)

1. **Create a database** in Hostinger hPanel → Databases → Create Database
2. **Open phpMyAdmin** (from hPanel → Databases)
3. **Select your database** in the left sidebar
4. **Run the base schema first:**
   - Click the **SQL** tab
   - Open `database/complete_schema_for_hostinger.sql` from this repo
   - Copy its entire contents, paste into phpMyAdmin, click **Go**
5. **Run the migrations:**
   - Open `database/HOSTINGER_DEPLOY.sql`
   - Copy its entire contents, paste into phpMyAdmin, click **Go**

If you get "Duplicate column" or "Table already exists" errors, the schema may already be applied—you can ignore those or run only the migrations that haven’t been applied yet.

### Option B: Copy your local database (includes your data)

If you want to move your existing data (users, CVs, etc.) to Hostinger:

1. **Export from local MySQL:**
   ```bash
   mysqldump -u root -p b2b_cv_app > backup.sql
   ```
   (Use your local DB name, user, and password.)

2. **Import in Hostinger phpMyAdmin:**
   - Create a database in Hostinger
   - Open phpMyAdmin, select the database
   - Click **Import**
   - Choose `backup.sql`, click **Go**

This copies the full schema and all data. No need to run migrations if the local DB is up to date.

---

## App deployment via GitHub

1. **Push your repo to GitHub** (if not already)
2. **In Hostinger hPanel** → Websites → your domain → **Git**
3. **Connect your GitHub repo** and deploy
4. **Configure `.env`** on the server with:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` (from Hostinger)
   - `APP_URL` = your live URL (e.g. `https://yourdomain.com`)
   - `APP_ENV=production`
   - Stripe keys and webhook secret
   - Other required variables from `.env.example`

5. **Run `composer install`** on the server (Hostinger usually does this via deploy hooks, or run it manually via SSH/File Manager)

6. **Create `storage/uploads`** and ensure it’s writable (e.g. `chmod 755`)

7. **Point your web root** to the project directory (or the `public` folder if you use one)

---

## Checklist

- [ ] Database created and schema applied (Option A or B)
- [ ] `.env` configured with production values
- [ ] `APP_ENV=production`
- [ ] `storage/uploads` exists and is writable
- [ ] Stripe webhook URL set to `https://yourdomain.com/api/stripe/webhook.php`
- [ ] Composer dependencies installed
