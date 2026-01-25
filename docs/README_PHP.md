# CV App - PHP Version

A professional CV builder web application built with PHP and MySQL.

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

3. Apply all migrations in chronological order:
```bash
mysql -u root -p cv_app < database/20241107_add_project_image_path.sql
# ... apply all migrations in order
```

### 2. Configuration

Create a `.env` file in the project root:

```env
DB_HOST=localhost
DB_NAME=cv_app
DB_USER=your_db_user
DB_PASS=your_db_password
APP_URL=http://localhost:8000
APP_ENV=development

# Stripe (optional)
STRIPE_PUBLISHABLE_KEY=pk_test_xxx
STRIPE_SECRET_KEY=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# AI Service (optional)
AI_SERVICE=ollama
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_MODEL=llama3:latest
```

### 3. Install Dependencies

```bash
composer install
```

### 4. Storage Directory

Create the storage directory:
```bash
mkdir -p storage/uploads
mkdir -p logs
chmod 755 storage/uploads logs
```

### 5. Web Server

#### Development
```bash
php -S localhost:8000
```

#### Apache
- Ensure mod_rewrite is enabled
- The `.htaccess` file handles routing

#### Nginx
Configure URL rewriting:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 6. PDF Generation (Optional)

For PDF export functionality:
```bash
cd scripts
npm install
```

## PHP Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Required extensions: PDO, mbstring, fileinfo, json
- File uploads enabled

## Project Structure

```
/
├── php/                 # Core PHP files
│   ├── config.php       # Configuration & .env loader
│   ├── database.php     # Database connection (PDO)
│   ├── auth.php         # Authentication functions
│   ├── security.php     # CSRF, validation, rate limiting
│   ├── storage.php      # File uploads
│   ├── utils.php        # Utility functions
│   ├── helpers.php      # Main includes file
│   ├── ai-service.php   # AI abstraction layer
│   ├── cv-templates.php # CV template management
│   ├── cv-variants.php  # CV variants
│   ├── subscriptions.php # Subscription management
│   └── stripe.php       # Stripe integration
├── api/                 # API endpoints
│   ├── stripe/          # Stripe webhooks & checkout
│   └── *.php            # Various API endpoints
├── views/               # View templates
│   └── partials/        # Reusable components
├── storage/             # File uploads storage
├── logs/                # Application logs
├── database/            # Database schemas & migrations
├── scripts/             # PDF generation (Node.js)
├── index.php            # Main entry point
└── .htaccess            # Apache configuration
```

## Key Features

- ✅ User authentication (register, login, logout, password reset)
- ✅ Email verification required
- ✅ CV sections (work experience, education, projects, skills, etc.)
- ✅ Public CV pages (`/cv/@username`)
- ✅ PDF export
- ✅ Stripe subscriptions (free, pro, lifetime)
- ✅ AI-powered CV rewriting and assessment
- ✅ CV templates and variants
- ✅ Cover letter generation
- ✅ Job application tracking
- ✅ Organisation/agency support (B2B)

## Security Features

- ✅ CSRF protection with timing-safe verification
- ✅ Rate limiting for authentication
- ✅ Password hashing with bcrypt
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ XSS prevention (input sanitization)
- ✅ Secure file upload handling
- ✅ Security headers (CSP, X-Frame-Options, etc.)

## Notes

- UUIDs generated in PHP (compatible with all MySQL versions)
- Password hashing uses PHP's `password_hash()` with bcrypt
- File storage uses local filesystem
- All database queries use prepared statements
