# B2B Recruitment Agency Implementation Summary

This document summarises the changes made to convert the CV Builder app into a multi-tenant platform for recruitment agencies.

## Files Created

### Database
- `database/20250114_add_organisations_multitenancy.sql` - Migration to create:
  - `organisations` table (recruitment agencies)
  - `organisation_members` table (team members with roles)
  - `candidate_invitations` table
  - `team_invitations` table
  - `activity_log` table (audit trail)
  - Updates to `profiles` table (organisation_id, account_type, cv_visibility, etc.)

### PHP Core Files
- `php/authorisation.php` - Role-based access control (RBAC) system with:
  - Role hierarchy (owner > admin > recruiter > viewer)
  - Organisation membership functions
  - Candidate management authorisation
  - Activity logging

- `php/invitations.php` - Invitation system for:
  - Candidate invitations
  - Team member invitations
  - Token validation and acceptance

### Agency Pages
- `agency/dashboard.php` - Organisation overview with stats and quick actions
- `agency/candidates.php` - Candidate list with search, filter, and export
- `agency/team.php` - Team member management (roles, activate/deactivate)
- `agency/settings.php` - Organisation branding and preferences
- `agency/invite-candidate.php` - Send candidate invitations
- `agency/invite-team.php` - Send team member invitations

### Public Pages
- `accept-invitation.php` - Accept candidate/team invitations
- `create-organisation.php` - Create new organisation

### Partials
- `views/partials/agency/header.php` - Agency navigation header

## Files Modified

- `php/helpers.php` - Added includes for authorisation.php and invitations.php
- `php/email.php` - Added invitation email functions
- `php/subscriptions.php` - Added organisation subscription plans and functions
- `cv.php` - Added visibility/access control based on cv_visibility setting

## Role Hierarchy

| Role | Permissions |
|------|-------------|
| Owner | Full access, billing, transfer ownership, delete organisation |
| Admin | Manage team, all candidates, settings (cannot access billing) |
| Recruiter | Invite candidates, manage assigned candidates |
| Viewer | Read-only access to candidates |

## Organisation Subscription Plans

| Plan | Candidates | Team Members | Price |
|------|------------|--------------|-------|
| Basic | 10 | 3 | £49/month |
| Professional | 50 | 10 | £149/month |
| Enterprise | Unlimited | Unlimited | £499/month |

## CV Visibility Options

- **Private**: Only the candidate can view
- **Organisation**: Team members in the same organisation can view
- **Public**: Anyone with the link can view

## Setup Instructions

### 1. Run Database Migration
```bash
mysql -u username -p database_name < database/20250114_add_organisations_multitenancy.sql
```

### 2. Add Stripe Price IDs (in .env or config.php)
```php
// Organisation subscription prices
define('STRIPE_PRICE_AGENCY_BASIC', 'price_xxx');
define('STRIPE_PRICE_AGENCY_PRO', 'price_xxx');
define('STRIPE_PRICE_AGENCY_ENTERPRISE', 'price_xxx');
```

### 3. Create Stripe Products
Create the following products in your Stripe dashboard:
- Agency Basic (£49/month)
- Agency Professional (£149/month)
- Agency Enterprise (£499/month)

## URL Structure

| URL | Description |
|-----|-------------|
| `/create-organisation.php` | Create a new organisation |
| `/agency/dashboard.php` | Organisation dashboard |
| `/agency/candidates.php` | Candidate management |
| `/agency/team.php` | Team management |
| `/agency/settings.php` | Organisation settings |
| `/agency/invite-candidate.php` | Invite a candidate |
| `/agency/invite-team.php` | Invite a team member |
| `/accept-invitation.php` | Accept an invitation |

## Next Steps / Future Enhancements

1. **Agency Billing Page** (`agency/billing.php`) - Stripe integration for organisation billing
2. **Candidate Detail Page** (`agency/candidate.php`) - Individual candidate management
3. **Bulk Operations** - Bulk invite, export PDFs as ZIP
4. **Custom Domain Support** - Allow organisations to use their own domains
5. **White-labelling** - Organisation branding on CV exports
6. **Webhook Updates** - Update Stripe webhook to handle organisation subscriptions
7. **API Endpoints** - RESTful API for organisation management
8. **Reporting Dashboard** - Analytics and activity reports

## Notes on British English

The codebase uses British English spelling:
- `organisation` (not organization)
- `authorisation` (not authorization)
- `colour` (not color)
- `centre` (not center)

## Security Considerations

- All organisation queries include `organisation_id` filtering
- CSRF tokens required for all POST actions
- Role-based access control on all agency pages
- Invitation tokens expire after 7 days
- Activity logging for audit trails
