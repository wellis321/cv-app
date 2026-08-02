# Local Development Setup Guide

This guide will help you set up and run the CV App locally on your machine.

## Prerequisites

Before you begin, make sure you have:

1. **PHP 7.4+** installed
   - Check with: `php --version`
   - Download from: [php.net](https://www.php.net/downloads.php)

2. **MySQL 5.7+ or MariaDB**
   - For macOS: Use MAMP, XAMPP, or Homebrew
   - For Windows: Use XAMPP, WAMP, or MySQL directly
   - For Linux: Use your package manager

3. **Node.js 18+** (for MCP browser extension, optional)
   - Check with: `node --version`
   - Download from: [nodejs.org](https://nodejs.org/)

## Step 1: Verify Setup

No PHP dependencies need to be installed. PDF generation is handled client-side using pdfmake (loaded from CDN), so no Composer or PHP package installation is required.

## Step 2: Database Setup

### 2.1 Create the Database

1. Open phpMyAdmin (or your MySQL client)
2. Create a new database named `cv_app`:

```sql
CREATE DATABASE cv_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2.2 Import the Schema

1. In phpMyAdmin, select the `cv_app` database
2. Click the **SQL** tab
3. Open `database/mysql_schema.sql` in a text editor
4. Copy and paste the entire contents into the SQL tab
5. Click **Go** to execute

### 2.3 Fix MySQL 8.0 Authentication (if needed)

If you're using MySQL 8.0+ and see authentication errors, run this in phpMyAdmin:

```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';
FLUSH PRIVILEGES;
```

Replace `'root'` with your actual MySQL password if different.

See `docs/MAMP_MYSQL_FIX.md` for detailed troubleshooting.

### 2.4 Run Migrations (Optional)

If you need additional features, run the migration files in order:

1. `database/20241110_add_subscription_fields.sql`
2. `database/20241216_add_template_preferences.sql`
3. Other migration files as needed

## Step 3: Configuration

### 3.1 Create .env File

Create a `.env` file in the project root with your database credentials:

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=cv_app
DB_USER=root
DB_PASS=root

# Application Configuration
APP_URL=http://localhost:8000
APP_ENV=development

# Stripe Configuration (optional, for subscriptions)
STRIPE_PUBLISHABLE_KEY=
STRIPE_SECRET_KEY=
STRIPE_WEBHOOK_SECRET=
STRIPE_PRICE_PRO_MONTHLY=
STRIPE_PRICE_PRO_ANNUAL=
STRIPE_PRICE_PRO_TRIAL_7DAY=
STRIPE_PRICE_PRO_3MONTH=
STRIPE_PRICE_LIFETIME=
```

**Important:** 
- Adjust `DB_PASS` to match your MySQL password
- Adjust `APP_URL` to match your server URL and port
- **Do not use port 8889 for the web server** - on macOS, MAMP's MySQL defaults to port 8889 (this is also the default in the `.env` examples above and in `DB_PORT`). Running the PHP dev server on the same port MySQL uses causes an intermittent, hard-to-diagnose bug: depending on whether your browser resolves `localhost` to IPv4 or IPv6, you can end up connected directly to the MySQL process instead of PHP, and see raw MySQL protocol bytes (a version string, `caching_sha2_password`, "Got packets out of order") rendered as garbled page content. This tends to work fine in one browser and break in another, which makes it especially confusing - always pick a port that's different from `DB_PORT`.
- The `.env` file is gitignored for security

## Step 4: Run the Development Server

### Option 1: PHP Built-in Server (Recommended for Development)

From the project root directory, run:

```bash
npm start
```

This starts the PHP built-in server with the correct upload limits (see `scripts/start-server.js`; defaults to port 8890, or set `PORT=xxxx npm start` to override). Alternatively, run PHP directly on a port of your choosing - just make sure it's different from `DB_PORT` (MySQL/MAMP commonly uses 8889):

```bash
php -S localhost:8000 -t . index.php
```

This will:
- Start a PHP development server on the port you specify
- Use `index.php` as the router
- Serve files from the current directory

**Access your app at:** `http://localhost:8000/` (or whichever port you chose)

### Option 2: Using MAMP (macOS)

1. Open MAMP
2. Set the document root to your project directory:
   - MAMP → Preferences → Web Server → Document Root
   - Select your project folder
3. Start MAMP servers
4. Access at: `http://localhost:8888/` (or the port MAMP uses)

### Option 3: Using XAMPP

1. Copy your project to `htdocs/` folder
2. Start Apache and MySQL in XAMPP Control Panel
3. Access at: `http://localhost/cv-app/`

### Option 4: Using Apache/Nginx

Configure your web server to point to the project directory and use `index.php` as the entry point.

## Step 5: Verify Installation

1. Open `http://localhost:8000/test-connection.php` (or your configured URL)
2. You should see:
   - ✅ Config loaded successfully
   - ✅ Database connection successful
   - ✅ List of database tables

If you see errors, check the troubleshooting section below.

## Step 6: Create Your First Account

1. Go to `http://localhost:8000/`
2. Click "Register" or "Sign Up"
3. Fill in your details
4. Check your email for verification (if email is configured)
5. Log in and start building your CV!

## Troubleshooting

### Database Connection Errors

**Error: "caching_sha2_password" or garbled characters**
- This is a MySQL 8.0 authentication issue
- Fix: Run the SQL command in Step 2.3 above
- See `docs/MAMP_MYSQL_FIX.md` for details

**Error: "Access denied for user"**
- Check your `.env` file has correct credentials
- Verify MySQL is running
- Check database exists: `SHOW DATABASES;`

**Error: "Unknown database 'cv_app'"**
- Create the database (Step 2.1)
- Or update `DB_NAME` in `.env` to match your database name

### Character Encoding Warning / Garbled Page Content

If you see: "The character encoding of the document was not declared", or the page shows garbled binary text containing things like a MySQL version string, `caching_sha2_password`, or "Got packets out of order" - **your web server and MySQL are sharing the same port.** This is almost always caused by running the PHP dev server on port 8889, which is also MAMP's default MySQL port (`DB_PORT` in `.env`). Depending on whether a given browser resolves `localhost` to IPv4 or IPv6, it can connect straight into MySQL instead of PHP, and MySQL's raw protocol handshake gets rendered as page content - which is also why this often "works" in one browser and not another.

Fix: run the PHP dev server on a different port than `DB_PORT` (see Step 4 above - `npm start` or `php -S localhost:8000 ...`), and update `APP_URL` in `.env` to match.

If the encoding warning shows up on an otherwise normal-looking page (no garbled binary content), it's a harmless browser warning - check `php/config.php` has the Content-Type header set and clear your browser cache.

### Port Already in Use

If you get "Address already in use" error:

```bash
# Find what's using a port (macOS/Linux) - replace 8000 with the port in question
lsof -i :8000

# Kill the process, or just run on a different port
php -S localhost:8001 -t . index.php
```

### Permission Errors

If you see file permission errors:

```bash
# Make storage directories writable
chmod -R 755 storage/
chmod -R 755 logs/
```


## Development Tips

### Enable Error Display

For development, errors are shown by default. To disable:

Edit `php/config.php`:
```php
define('APP_ENV', 'production'); // Change from 'development'
```

### View Logs

Application logs are stored in:
- `logs/auth.log` - Authentication attempts
- `logs/php-errors.log` - PHP errors (in production mode)

### Database Migrations

When adding new features, create migration files in `database/` following the naming pattern:
- `YYYYMMDD_description.sql`

Run migrations in phpMyAdmin or via command line.

### Testing

To verify your setup is working:
1. Visit the homepage: `http://localhost:8000/`
2. Try registering a new account
3. Check browser console (F12) for any errors
4. Verify database connection by checking if you can log in

## Next Steps

- Read `docs/README.md` for application overview
- Check `docs/SECURITY_AUDIT.md` for security considerations
- Review `docs/PRODUCTION_CHECKLIST.md` before deploying

## Getting Help

- Check other documentation in the `docs/` folder
- Review error messages in browser console (F12)
- Check PHP error logs
- Verify all prerequisites are installed correctly

