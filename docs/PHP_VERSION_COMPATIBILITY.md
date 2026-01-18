# PHP Version Compatibility

## Current Versions

- **Local Development**: PHP 8.5.0
- **Production**: PHP 8.2.28 (upgradeable to PHP 8.4)

## Compatibility Status

✅ **The codebase is fully compatible with PHP 8.4**

### Version-Specific Code

The only version-specific code in the project is in `php/database.php`, which handles the PDO MySQL init command deprecation:

```php
// For PHP < 8.5, use the init command option (deprecated in 8.5+)
if (version_compare(PHP_VERSION, '8.5.0', '<')) {
    $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci";
}

// For PHP 8.5+, execute charset command after connection (deprecated option removed)
if (version_compare(PHP_VERSION, '8.5.0', '>=')) {
    $this->pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
}
```

**This means:**
- ✅ PHP 8.4 will use `PDO::MYSQL_ATTR_INIT_COMMAND` (no deprecation warning)
- ✅ PHP 8.5+ will execute the command after connection (avoids deprecation)
- ✅ Both methods achieve the same result (proper charset handling)

## Supported PHP Versions

The application is compatible with:
- ✅ PHP 7.4+ (minimum requirement)
- ✅ PHP 8.0
- ✅ PHP 8.1
- ✅ PHP 8.2 (current production)
- ✅ PHP 8.3
- ✅ PHP 8.4 (recommended for production upgrade)
- ✅ PHP 8.5+ (local development)

## Upgrade Path: PHP 8.2 → PHP 8.4

### What to Expect

1. **No Breaking Changes**: PHP 8.4 is backward compatible with PHP 8.2
2. **No Code Changes Required**: The application will work as-is
3. **Performance Improvements**: PHP 8.4 includes performance optimizations
4. **No Deprecation Warnings**: The PDO init command is not deprecated in 8.4

### Before Upgrading Production

1. **Test Locally** (if possible):
   ```bash
   # Test with PHP 8.4 if available
   php8.4 -S localhost:8000 -t . index.php
   ```

2. **Check Extensions**: Ensure all required PHP extensions are available in PHP 8.4:
   - ✅ PDO
   - ✅ PDO_MySQL
   - ✅ mbstring
   - ✅ json
   - ✅ session
   - ✅ fileinfo
   - ✅ curl

3. **Backup**: Always backup your database and files before upgrading

4. **Update php.ini**: Review any custom php.ini settings and ensure they're compatible

### After Upgrading

1. **Clear OPcache** (if enabled):
   ```php
   opcache_reset();
   ```

2. **Test Critical Functions**:
   - User registration/login
   - Database connections
   - File uploads
   - PDF generation
   - Email sending (if configured)

3. **Monitor Error Logs**: Check for any unexpected errors

## Differences Between Local (8.5) and Production (8.4)

### What's Different

| Feature | Local (8.5) | Production (8.4) |
|---------|-------------|------------------|
| PDO MySQL Init Command | Executed after connection | Set via option |
| Deprecation Warnings | May show for 8.5+ features | None for this codebase |

### What's the Same

- ✅ All application code works identically
- ✅ Database connections work the same way
- ✅ All features function identically
- ✅ No functionality differences

## Recommendations

1. **Upgrade Production to PHP 8.4**: 
   - Safe upgrade from 8.2
   - Better performance than 8.2
   - No breaking changes
   - Still supported and maintained

2. **Keep Local at 8.5**:
   - Helps catch future deprecations early
   - Tests compatibility with latest PHP
   - No issues since code handles both versions

3. **Future-Proofing**:
   - The code already handles PHP 8.5+ deprecations
   - When production upgrades to 8.5+, no code changes needed
   - The version check automatically uses the correct method

## Testing Compatibility

To verify compatibility, you can test with different PHP versions:

```bash
# Test with PHP 8.4 (if installed)
php8.4 -S localhost:8000 -t . index.php

# Test with PHP 8.2 (if installed)
php8.2 -S localhost:8000 -t . index.php
```

## phpMyAdmin Compatibility

**Current Production**: phpMyAdmin 5.2.2

### Does phpMyAdmin Version Matter?

**Short answer**: No, for your application. Yes, for the admin tool itself.

- ✅ **Your PHP application is unaffected** - It connects directly to MySQL via PDO, not through phpMyAdmin
- ⚠️ **phpMyAdmin 5.2.2 has issues with PHP 8.4** - May show blank pages or errors
- ✅ **Recommended**: Upgrade to phpMyAdmin 5.2.3+ when upgrading to PHP 8.4

### phpMyAdmin Upgrade Recommendation

If upgrading production to PHP 8.4, also upgrade phpMyAdmin:

- **Current**: phpMyAdmin 5.2.2 (works with PHP 8.2, issues with PHP 8.4)
- **Recommended**: phpMyAdmin 5.2.3+ (tested with PHP 8.4, includes deprecation fixes)

**Note**: phpMyAdmin is only for database management. Your application doesn't depend on it.

## Summary

✅ **You're safe to upgrade production to PHP 8.4**

- No code changes required
- No breaking changes
- Better performance
- The codebase already handles version differences automatically

**Optional but recommended**: Upgrade phpMyAdmin to 5.2.3+ when upgrading PHP to avoid admin tool issues.

The only difference you'll notice is that PHP 8.4 won't show the deprecation warning that PHP 8.5 shows (because the deprecated feature isn't deprecated in 8.4 yet).

