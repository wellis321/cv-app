# Super Admin Setup Guide

This guide explains how to set up and manage super admin accounts in the system.

## Overview

Super admin accounts have system-wide access to:
- All organisations
- All users
- System-wide activity logs
- System settings

## Creating a Super Admin Account

### Method 1: Using the Setup Script (Recommended)

1. Run the setup script from the command line:
```bash
php scripts/create-super-admin.php
```

2. Follow the prompts to:
   - Enter email address
   - Enter full name (optional)
   - Enter and confirm password

The script will create a new super admin account or promote an existing user to super admin.

### Method 2: Direct Database Update

If you need to create a super admin account directly in the database:

1. First, ensure the user account exists in the `profiles` table
2. Update the user's `is_super_admin` field:

```sql
UPDATE profiles 
SET is_super_admin = 1 
WHERE email = 'admin@example.com';
```

Or create a new super admin account:

```sql
INSERT INTO profiles (
    id, 
    email, 
    password_hash, 
    full_name, 
    username, 
    email_verified, 
    is_super_admin, 
    account_type,
    created_at, 
    updated_at
) VALUES (
    UUID(), 
    'admin@example.com', 
    '$2y$10$...', -- Use password_hash() function to generate
    'Super Admin', 
    'admin123', 
    1, 
    1, 
    'individual',
    NOW(), 
    NOW()
);
```

**Note:** When creating a password hash, use PHP's `password_hash()` function:
```php
password_hash('your-password', PASSWORD_DEFAULT);
```

## Security Considerations

1. **Limited Accounts**: Only create super admin accounts for trusted system administrators
2. **Strong Passwords**: Ensure super admin accounts use strong, unique passwords
3. **Two-Factor Authentication**: Consider implementing 2FA for super admin accounts
4. **Audit Trail**: All super admin actions are logged in the activity log
5. **Regular Review**: Periodically review super admin accounts and remove unnecessary ones

## Accessing the Super Admin Dashboard

Once logged in as a super admin:

1. You'll see an "Admin" link in the main navigation (red text)
2. Click it to access `/admin/dashboard.php`
3. From there you can:
   - View system-wide statistics
   - Manage all organisations
   - Manage all users
   - View system-wide activity logs
   - Access system settings

## Removing Super Admin Status

To remove super admin status from a user:

```sql
UPDATE profiles 
SET is_super_admin = 0 
WHERE email = 'user@example.com';
```

## Super Admin Permissions

Super admins can:
- Access all organisations (bypass organisation membership checks)
- View and manage all users
- Edit organisation settings (plans, limits, subscription status)
- View system-wide activity logs
- Access all candidate CVs regardless of visibility settings
- Manage any candidate regardless of organisation

## Activity Logging

All super admin actions are automatically logged in the `activity_log` table with:
- Action type (e.g., `admin.organisation.updated`)
- User ID (the super admin)
- Target user/organisation ID (if applicable)
- Details (JSON format)
- Timestamp

## Troubleshooting

### Cannot Access Admin Dashboard

1. Verify the user has `is_super_admin = 1` in the database
2. Check that the user is logged in
3. Clear browser cache and cookies
4. Verify the migration has been run: `database/20250115_add_super_admin.sql`

### Super Admin Actions Not Logged

1. Check the `activity_log` table exists
2. Verify the `logActivity()` function is being called
3. Check database connection and permissions

## Migration

Before creating super admin accounts, ensure the database migration has been run:

```bash
mysql -u username -p database_name < database/20250115_add_super_admin.sql
```

This migration adds the `is_super_admin` field to the `profiles` table.

