# CV App - PHP Version

This is the PHP conversion of the SvelteKit CV Builder application.

## Setup Instructions

### 1. Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE cv_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the schema:
```bash
mysql -u root -p cv_app < database/mysql_schema.sql
```

### 2. Configuration

Create a `.env` file or set environment variables:

```env
DB_HOST=localhost
DB_NAME=cv_app
DB_USER=your_db_user
DB_PASS=your_db_password
APP_URL=http://localhost
APP_ENV=development
```

Or edit `php/config.php` directly with your database credentials.

### 3. Storage Directory

Create the storage directory:
```bash
mkdir -p storage/uploads
chmod 755 storage/uploads
```

### 4. Web Server

#### Apache
- Ensure mod_rewrite is enabled
- The `.htaccess` file will handle routing

#### Nginx
You'll need to configure URL rewriting in your Nginx config:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 5. PHP Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.2+
- PDO extension enabled
- File uploads enabled

## Project Structure

```
/
├── php/                 # Core PHP files
│   ├── config.php      # Configuration
│   ├── database.php    # Database connection
│   ├── auth.php        # Authentication functions
│   ├── security.php    # CSRF, validation
│   ├── storage.php     # File uploads
│   ├── utils.php       # Utility functions
│   └── helpers.php     # Helper includes
├── views/              # View templates
│   └── partials/       # Reusable components
├── storage/            # File uploads storage
├── database/           # Database schemas
│   └── mysql_schema.sql
├── index.php           # Main entry point
└── .htaccess          # Apache configuration
```

## Key Features Converted

- ✅ Authentication (register, login, logout)
- ✅ Database connection with PDO
- ✅ File storage system
- ✅ CSRF protection
- ✅ Input validation and sanitization
- ✅ Dashboard with section status
- ✅ Session management

## Remaining Work

- [ ] Convert all page routes (profile, work-experience, education, etc.)
- [ ] Convert all API endpoints
- [ ] Implement CV preview/export
- [ ] Add PDF generation
- [ ] Migrate frontend components to PHP templates

## Notes

- UUIDs are generated in PHP (compatible with all MySQL versions)
- Password hashing uses PHP's `password_hash()` with bcrypt
- File storage uses local filesystem (can be extended to S3/cloud storage)
- All database queries use prepared statements to prevent SQL injection

## Migration from Supabase

When migrating existing data:
1. Export data from Supabase PostgreSQL
2. Convert UUIDs to MySQL format (they're compatible)
3. Convert password hashes (you'll need to reset passwords or migrate hash algorithm)
4. Update file URLs to point to new storage location
