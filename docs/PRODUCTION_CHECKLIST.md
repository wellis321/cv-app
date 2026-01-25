# Production Deployment Checklist

This checklist should be completed before deploying the CV application to production.

## Security

- [ ] **Environment Variables**

  - [ ] Ensure all sensitive values are in `.env` file
  - [ ] Verify `.env` is in `.gitignore` and not committed
  - [ ] Set `APP_ENV=production` in `.env`
  - [ ] Verify database credentials are production values

- [ ] **Authentication**

  - [ ] Test user registration flow
  - [ ] Test user login flow
  - [ ] Verify email verification works
  - [ ] Ensure password reset functionality works
  - [ ] Test session management

- [ ] **Authorization**

  - [ ] Verify resource ownership checks (`profile_id`) work
  - [ ] Test that users can only access their own data
  - [ ] Ensure admin routes are properly protected

- [ ] **API Security**

  - [ ] CSRF protection is enabled for all state-changing operations
  - [ ] Rate limiting is working for auth endpoints
  - [ ] Verify security headers are correctly set
  - [ ] Storage proxy authentication is working

- [ ] **Data Validation**
  - [ ] All user inputs are validated server-side
  - [ ] Input sanitization prevents XSS attacks
  - [ ] File uploads are properly restricted and validated

## HTTPS & SSL

- [ ] **Enable HTTPS**
  - [ ] SSL certificate is installed on hosting
  - [ ] Uncomment HTTPS redirect in `.htaccess` (lines 5-8)
  - [ ] Uncomment HSTS header in `.htaccess` (line 37)
  - [ ] Verify `session.cookie_secure` is enabled for production

## Database

- [ ] **Schema**

  - [ ] All required tables exist
  - [ ] Database indexes are properly set up
  - [ ] Foreign key constraints are correctly implemented

- [ ] **Migrations**

  - [ ] All migrations in `database/` have been applied
  - [ ] `20250123_add_stripe_webhook_events.sql` migration applied
  - [ ] Verify schema matches production requirements

- [ ] **Backups**
  - [ ] Automated MySQL backup is configured on hosting provider
  - [ ] Backup retention policy is defined
  - [ ] Test backup restoration process

## Stripe Integration

- [ ] **Production Keys**

  - [ ] `STRIPE_PUBLISHABLE_KEY` is production key (not `pk_test_`)
  - [ ] `STRIPE_SECRET_KEY` is production key (not `sk_test_`)
  - [ ] `STRIPE_WEBHOOK_SECRET` is production webhook secret
  - [ ] Price IDs are production price IDs

- [ ] **Webhooks**
  - [ ] Production webhook endpoint registered in Stripe dashboard
  - [ ] Webhook signature verification is working
  - [ ] Test checkout flow end-to-end

## Performance

- [ ] **Optimisation**

  - [ ] Images are properly optimized
  - [ ] CSS/JS assets are minified (via CDN)
  - [ ] PHP opcache is enabled

- [ ] **Caching**
  - [ ] Static assets have appropriate cache headers
  - [ ] Storage proxy cache headers are set (1 year)

## Monitoring & Logging

- [ ] **Error Tracking**

  - [ ] `DEBUG` is `false` in production
  - [ ] Errors logged to `logs/php-errors.log`
  - [ ] Authentication attempts logged to `logs/auth.log`
  - [ ] Log files are not publicly accessible

- [ ] **Monitoring**
  - [ ] Set up uptime monitoring
  - [ ] Configure alerts for server errors

## Deployment

- [ ] **Hosting Configuration**

  - [ ] Apache mod_rewrite is enabled
  - [ ] `.htaccess` rules are working
  - [ ] PHP version is 7.4 or higher
  - [ ] Required PHP extensions installed (PDO, mbstring, etc.)

- [ ] **File Permissions**
  - [ ] `storage/` directory is writable (755)
  - [ ] `logs/` directory is writable (755)
  - [ ] Sensitive files are protected (.env, .sql, .log, .md)

- [ ] **Dependencies**
  - [ ] Run `composer install --no-dev` for production
  - [ ] PDF generator: `cd scripts && npm install --production`

## Testing

- [ ] **Functional Testing**

  - [ ] All core features work as expected
  - [ ] CV preview displays correctly
  - [ ] PDF export works
  - [ ] AI features work (if configured)

- [ ] **Cross-Browser Testing**

  - [ ] Application works in Chrome, Firefox, Safari, Edge
  - [ ] Mobile responsiveness is verified

- [ ] **Security Testing**
  - [ ] Test CSRF protection
  - [ ] Test authentication flows
  - [ ] Test file upload restrictions

## Documentation

- [ ] **Technical Documentation**

  - [ ] `CLAUDE.md` is up to date
  - [ ] API endpoints are documented
  - [ ] Deployment process is documented

- [ ] **User Documentation**
  - [ ] Help/FAQ available if needed
  - [ ] Contact information available

## Post-Deployment

- [ ] **Verification**

  - [ ] All features work in production environment
  - [ ] No errors in production logs
  - [ ] Email sending works

- [ ] **Rollback Plan**
  - [ ] Database backup taken before deployment
  - [ ] Know how to restore from backup
  - [ ] Previous code version available

## Compliance & Legal

- [ ] **Privacy**

  - [ ] Privacy policy is in place
  - [ ] Cookie consent if required
  - [ ] GDPR compliance if serving EU users

- [ ] **Accessibility**
  - [ ] Application meets basic accessibility requirements
  - [ ] Form labels and alt text are present

---

## Final Sign-Off

| Component     | Verified By | Date | Notes |
| ------------- | ----------- | ---- | ----- |
| Security      |             |      |       |
| HTTPS/SSL     |             |      |       |
| Database      |             |      |       |
| Stripe        |             |      |       |
| Performance   |             |      |       |
| Monitoring    |             |      |       |
| Deployment    |             |      |       |
| Testing       |             |      |       |
| Documentation |             |      |       |
