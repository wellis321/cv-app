# How to Import the Database Schema

Your database connection is working, but the tables need to be created. Here are several ways to do it:

## Method 1: Using phpMyAdmin (Easiest)

1. Log into your hosting control panel (cPanel/Plesk/etc.)
2. Open **phpMyAdmin**
3. Select your database (`u248320297_cvapp`)
4. Click the **SQL** tab
5. Copy the entire contents of `database/mysql_schema.sql`
6. Paste it into the SQL query box
7. Click **Go** to execute

## Method 2: Using Command Line (SSH)

If you have SSH access:

```bash
mysql -u your_db_user -p u248320297_cvapp < database/mysql_schema.sql
```

You'll be prompted for your database password.

## Method 3: Using a PHP Import Script (Quick Fix)

I've created `import-schema.php` that you can run once to create the tables.

**⚠️ IMPORTANT: Delete this file after importing for security!**

## Method 4: Copy SQL Manually

1. Open `database/mysql_schema.sql`
2. Copy all the SQL statements
3. In phpMyAdmin, go to your database
4. Click the SQL tab
5. Paste and execute

## After Import

Once the tables are created, you should be able to register and use the application.

To verify tables were created, check that these tables exist:
- profiles
- work_experience
- education
- skills
- projects
- certifications
- professional_memberships
- interests
- And others listed in the schema
