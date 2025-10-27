# Free Trial System

This CV Builder app now includes a free trial system that gives users 7 days of full access, then requires payment to continue.

## How It Works

1. **New users** automatically get a 7-day free trial when they sign up
2. **During the trial**, users have full access to all features
3. **After 7 days**, if users don't pay, they lose access to their data
4. **Payment** (£9.99/year one-time) grants lifetime access

## Current Status: DISABLED FOR DEVELOPMENT

The trial system is currently **DISABLED** so all users have free access during development.

### To Enable the Trial System:

1. Open `src/lib/stores/subscriptionStore.ts`

2. Find the section in `loadUserSubscription()` that says:
   ```typescript
   // FOR DEVELOPMENT - Comment out this section when ready to enable trial system
   currentSubscription.set({
       plan: {
           ...freePlan,
           id: 'free_premium',
           name: 'Free Premium (Development)',
           // ... rest of the code
       },
       expiresAt: null,
       isActive: true, // Always active during development
       isTrial: false,
       trialEndsAt: null,
       hasPaid: false
   });
   return;
   ```

3. **COMMENT OUT** that entire section (lines ~254-274)

4. **UNCOMMENT** the trial logic (lines ~276-322)

5. Also update `canAccessFeature` in the same file:
   ```typescript
   export const canAccessFeature = derived(
       currentSubscription,
       ($currentSubscription) => (featureName: string, value?: any) => {
           // During development: everyone gets free access to all features
           return true; // CHANGE THIS LINE - REMOVE "return true" and uncomment below

           // UNCOMMENT THIS CODE:
           if (!$currentSubscription.isActive || !$currentSubscription.plan) {
               return checkFeatureAccess(DEFAULT_FREE_PLAN.features, featureName, value);
           }
           return checkFeatureAccess($currentSubscription.plan.features, featureName, value);
       }
   );
   ```

6. Remove the line `return true;` and uncomment the feature checking logic

## Database Migration

The database migration has already been applied. It adds these columns to the `profiles` table:
- `trial_ends_at`: When the free trial expires
- `trial_started_at`: When the free trial started
- `has_paid`: Whether the user has paid for full access

## Features

### Trial Banner
- Shows at the top of the page for users in trial
- Displays days remaining
- "Upgrade" button to payment page

### Subscription Page
- Shows trial status
- "Upgrade Now" button for trial users
- "Pay Now to Keep Access" button for expired trial users
- Status badges showing trial/payment status

### Automatic Trial Start
- New users get 7 days free when they sign up
- Set in `src/routes/api/create-profile/+server.ts`

## Testing

To test the trial system:

1. **Enable the trial system** (see steps above)
2. **Create a new user account**
3. Check that they see "Free trial: 7 days remaining" banner
4. Wait for trial to expire OR manually set `trial_ends_at` to a past date in database
5. Verify they lose access and see upgrade prompt

## Stripe Integration

The payment system is ready to use Stripe. Make sure you have:

1. ✅ Created the product in Stripe Dashboard
2. ✅ Set `VITE_STRIPE_PUBLISHABLE_KEY` in Vercel
3. ✅ Set `STRIPE_SECRET_KEY` in Vercel
4. ✅ Set `STRIPE_WEBHOOK_SECRET` in Vercel
5. ✅ Set `STRIPE_PRICE_ID` in Vercel

## User Experience Flow

### Trial User (Active)
1. User sees banner showing days remaining
2. Can click "Upgrade Now" any time during trial
3. Payment at any point gives lifetime access

### Trial Expired User
1. User sees "Trial Expired" message
2. Can't access their CV sections
3. Must pay to regain access
4. Data is preserved for 30 days after expiry

### Paid User
1. Full access forever
2. No ads or limitations
3. Can use all features and templates

## Notes

- Users can dismiss the trial banner
- Trial starts automatically on sign-up
- Payment is one-time £9.99/year (not recurring)
- Users lose access to data if trial expires and they don't pay
