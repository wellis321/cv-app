# Fix MySQL 8.0 Authentication Error in MAMP

If you're seeing garbled characters like `J 8.0.44Lh[%W XNےےےےك%G*/\ ,sF:caching_sha2_password!#08S01Got packets out of order`, this is a MySQL 8.0 authentication compatibility issue.

## Solution: Change MySQL User Authentication Method

MySQL 8.0+ uses `caching_sha2_password` by default, but older PHP MySQL extensions may not support it. You need to change your MySQL user to use `mysql_native_password` instead.

### Method 1: Using phpMyAdmin (Easiest)

1. Open **MAMP** and start your servers
2. Open **phpMyAdmin** (usually at `http://localhost:8888/phpMyAdmin/` or check MAMP's start page)
3. Click on the **SQL** tab
4. Run this command (replace `root` and `your_password` with your actual MySQL username and password):

```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';
FLUSH PRIVILEGES;
```

If you're using a different username (not `root`), replace `root` with your username.

### Method 2: Using MySQL Command Line

1. Open Terminal
2. Navigate to MAMP's MySQL bin directory (usually `/Applications/MAMP/Library/bin/`)
3. Run:

```bash
./mysql -u root -p
```

4. Enter your MySQL password when prompted
5. Run these commands:

```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';
FLUSH PRIVILEGES;
EXIT;
```

### Method 3: Using MAMP's MySQL Terminal

1. In MAMP, go to **Tools** → **Terminal** (or similar)
2. Run the MySQL commands above

## Verify the Fix

After running the ALTER USER command, refresh your application. The database connection should work without the garbled character error.

## Alternative: Update PHP MySQL Extension

If you prefer to keep `caching_sha2_password`, you can update your PHP MySQL extension. However, changing the authentication method is usually easier and more reliable.

## Notes

- This change only affects the authentication method, not your data
- Your password remains the same
- This is safe to do in development environments
- For production, consider using a dedicated database user (not root) with appropriate permissions

