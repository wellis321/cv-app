# Production Readiness Plan

**Date:** 2025-01-XX
**Status:** ⚠️ **REQUIRES FIXES BEFORE PRODUCTION**

## Executive Summary

The codebase has a **solid security foundation** with proper CSRF protection, SQL injection prevention, and authentication. However, there are **inconsistencies** across files that need to be addressed for production readiness. Most critical issues are already fixed in the core configuration, but several files need updates for consistency.

---

## ✅ Already Implemented (Good!)

1. **Error Reporting** - ✅ Properly configured in `php/config.php` (lines 109-121)
2. **Rate Limiting** - ✅ Implemented in `php/security.php` (checkRateLimit function)
3. **Session Regeneration** - ✅ Implemented in `php/auth.php` (line 160)
4. **Security Headers** - ✅ Implemented in `php/security.php` (setSecurityHeaders function)
5. **Password Strength** - ✅ Implemented in `php/security.php` (validatePasswordStrength function)
6. **CSRF Protection** - ✅ Implemented across all forms
7. **SQL Injection Prevention** - ✅ All queries use prepared statements
8. **Canonical Domain Redirect** - ✅ Implemented in `php/helpers.php`

---

## 🔴 CRITICAL ISSUES (Must Fix Before Production)

### 1. Inconsistent Error Handling

**Issue:** Some files expose exception messages to users, others don't.

**Files Affected:**
- `projects.php` (lines 67, 108, 175) - Exposes `$e->getMessage()`
- `work-experience.php` - Needs verification
- `education.php` - Needs verification
- `skills.php` - Needs verification
- `memberships.php` - Needs verification
- `qualification-equivalence.php` - Needs verification

**Fix Required:**
```php
// BEFORE (INSECURE):
catch (Exception $e) {
    setFlash('error', 'Failed to add: ' . $e->getMessage());
}

// AFTER (SECURE):
catch (Exception $e) {
    error_log("Operation error: " . $e->getMessage());
    setFlash('error', 'Failed to add. Please try again.');
}
```

**Priority:** 🔴 CRITICAL
**Estimated Time:** 2-3 hours

---

### 2. Missing XSS Validation

**Issue:** Some files don't check for XSS attacks on all input fields.

**Files Affected:**
- `projects.php` - Missing XSS check on title, description, url
- `work-experience.php` - Missing XSS check on company_name, position, description
- `education.php` - Needs verification
- `skills.php` - Missing XSS check on name, category, level
- `memberships.php` - Needs verification
- `qualification-equivalence.php` - Needs verification

**Fix Required:**
```php
// Add after sanitizeInput:
if (checkForXss($fieldValue)) {
    setFlash('error', 'Invalid content detected');
    redirect('/page.php');
}
```

**Priority:** 🔴 CRITICAL
**Estimated Time:** 3-4 hours

---

### 3. Missing Input Length Validation

**Issue:** Form inputs don't have `maxlength` attributes and server-side validation.

**Files Affected:**
- `projects.php` - title (VARCHAR(255)), description (TEXT)
- `work-experience.php` - company_name (VARCHAR(255)), position (VARCHAR(255))
- `education.php` - institution (VARCHAR(255)), degree (VARCHAR(255))
- `skills.php` - name (VARCHAR(255)), category (VARCHAR(100)), level (VARCHAR(50))
- `memberships.php` - organisation (VARCHAR(255)), role (VARCHAR(255))
- `qualification-equivalence.php` - level (VARCHAR(255))

**Fix Required:**
1. Add `maxlength` attributes to HTML inputs
2. Add server-side length validation before database insert/update

**Priority:** 🔴 CRITICAL
**Estimated Time:** 2-3 hours

---

### 4. Missing Head Partial Usage

**Issue:** Some pages don't use the `partial('head')` function, missing SEO meta tags.

**Files Affected:**
- `profile.php` - Uses inline head tags
- `projects.php` - Uses inline head tags
- `work-experience.php` - Needs verification
- `education.php` - Needs verification
- `skills.php` - Uses inline head tags
- `memberships.php` - Needs verification
- `qualification-equivalence.php` - Needs verification
- `professional-summary.php` - Needs verification

**Fix Required:**
```php
// BEFORE:
<head>
    <meta charset="UTF-8">
    <title>Page Title</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

// AFTER:
<head>
    <?php partial('head', [
        'pageTitle' => 'Page Title | Simple CV Builder',
        'metaDescription' => 'Description here',
        'canonicalUrl' => APP_URL . '/page.php',
        'metaNoindex' => true,
    ]); ?>
</head>
```

**Priority:** 🟡 MEDIUM (SEO/Consistency)
**Estimated Time:** 1-2 hours

---

## 🟡 MEDIUM PRIORITY ISSUES

### 5. Inconsistent Error Message Patterns

**Issue:** Error messages vary in detail level across the codebase.

**Recommendation:** Standardize error messages:
- Generic messages for users
- Detailed logging for debugging
- Consistent error handling pattern

**Priority:** 🟡 MEDIUM
**Estimated Time:** 1 hour

---

### 6. Missing Input Validation on Some Fields

**Issue:** Some optional fields don't validate format when provided.

**Examples:**
- URL fields should validate URL format
- Date fields should validate date format
- Email fields (if any) should validate email format

**Priority:** 🟡 MEDIUM
**Estimated Time:** 2 hours

---

### 7. Database Error Handling

**Issue:** Some database operations don't handle specific error cases gracefully.

**Recommendation:** Add specific handling for:
- Duplicate key errors (unique constraints)
- Foreign key violations
- Data type errors

**Priority:** 🟡 MEDIUM
**Estimated Time:** 2 hours

---

## 🟢 LOW PRIORITY / RECOMMENDATIONS

### 8. Code Consistency

**Recommendation:** Standardize:
- Variable naming conventions
- Function organization
- Comment style

**Priority:** 🟢 LOW
**Estimated Time:** Ongoing

---

### 9. Performance Optimisation

**Recommendation:**
- Add database indexes where needed
- Optimise queries (check for N+1 problems)
- Add caching where appropriate

**Priority:** 🟢 LOW
**Estimated Time:** Ongoing

---

## Implementation Plan

### Phase 1: Critical Security Fixes (Day 1)

1. **Fix Error Handling** (2-3 hours)
   - [ ] Update `projects.php` error handling
   - [ ] Update `work-experience.php` error handling
   - [ ] Update `education.php` error handling
   - [ ] Update `skills.php` error handling
   - [ ] Update `memberships.php` error handling
   - [ ] Update `qualification-equivalence.php` error handling
   - [ ] Update `professional-summary.php` error handling

2. **Add XSS Validation** (3-4 hours)
   - [ ] Add XSS checks to `projects.php`
   - [ ] Add XSS checks to `work-experience.php`
   - [ ] Add XSS checks to `education.php`
   - [ ] Add XSS checks to `skills.php`
   - [ ] Add XSS checks to `memberships.php`
   - [ ] Add XSS checks to `qualification-equivalence.php`

3. **Add Length Validation** (2-3 hours)
   - [ ] Add `maxlength` attributes to all form inputs
   - [ ] Add server-side length validation to all forms
   - [ ] Test with maximum length inputs

**Total Phase 1 Time:** 7-10 hours

---

### Phase 2: Consistency & SEO (Day 2)

4. **Update Head Partials** (1-2 hours)
   - [ ] Update `profile.php` to use head partial
   - [ ] Update `projects.php` to use head partial
   - [ ] Update `work-experience.php` to use head partial
   - [ ] Update `education.php` to use head partial
   - [ ] Update `skills.php` to use head partial
   - [ ] Update `memberships.php` to use head partial
   - [ ] Update `qualification-equivalence.php` to use head partial
   - [ ] Update `professional-summary.php` to use head partial

5. **Standardize Error Messages** (1 hour)
   - [ ] Create error message constants
   - [ ] Update all error messages to use constants
   - [ ] Ensure consistent user-facing messages

**Total Phase 2 Time:** 2-3 hours

---

### Phase 3: Testing & Verification (Day 3)

6. **Security Testing** (4-6 hours)
   - [ ] Test XSS prevention on all forms
   - [ ] Test SQL injection prevention
   - [ ] Test CSRF protection
   - [ ] Test authorization checks
   - [ ] Test input validation
   - [ ] Test error handling (no information leakage)

7. **Functional Testing** (2-3 hours)
   - [ ] Test all CRUD operations
   - [ ] Test form submissions
   - [ ] Test edit/delete operations
   - [ ] Test edge cases

**Total Phase 3 Time:** 6-9 hours

---

## Pre-Production Checklist

Before deploying to production, verify:

- [ ] **Environment Configuration**
  - [ ] `.env` file has `APP_ENV=production`
  - [ ] `DEBUG=false` in production
  - [ ] All secrets are in environment variables
  - [ ] Database credentials are secure

- [ ] **Security**
  - [ ] All error handling updated (no exception messages exposed)
  - [ ] All XSS validation added
  - [ ] All length validation added
  - [ ] CSRF protection verified on all forms
  - [ ] Authorization checks verified on all operations

- [ ] **Code Quality**
  - [ ] All pages use head partial
  - [ ] Error messages are consistent
  - [ ] Code follows consistent patterns

- [ ] **Testing**
  - [ ] Security testing completed
  - [ ] Functional testing completed
  - [ ] Edge cases tested

- [ ] **Monitoring**
  - [ ] Error logging configured
  - [ ] Log rotation configured
  - [ ] Monitoring alerts set up

---

## Estimated Total Time to Production Ready

- **Phase 1 (Critical):** 7-10 hours
- **Phase 2 (Consistency):** 2-3 hours
- **Phase 3 (Testing):** 6-9 hours
- **Total:** 15-22 hours (2-3 days)

---

## Risk Assessment

| Issue | Risk Level | Impact | Likelihood |
|-------|-----------|--------|------------|
| Error message exposure | HIGH | Information disclosure | HIGH |
| Missing XSS validation | HIGH | XSS attacks | MEDIUM |
| Missing length validation | MEDIUM | Data corruption, DoS | MEDIUM |
| Missing head partial | LOW | SEO impact | LOW |

---

## Notes

- The core security infrastructure is **already in place** and working correctly
- Most issues are **consistency problems** rather than fundamental security flaws
- The codebase follows good security practices overall
- With the fixes above, the application will be **production-ready**

---

## Next Steps

1. Review this plan with the team
2. Prioritize fixes based on deployment timeline
3. Assign tasks to developers
4. Set up testing environment
5. Begin Phase 1 implementation
6. Schedule code review before production deployment
