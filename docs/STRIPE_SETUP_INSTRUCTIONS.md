# Stripe Setup Instructions for £9.99 Full Access

## What You Need From Me

**Don't share your keys with anyone.** Instead, you'll add them to your Vercel environment variables.

## Step-by-Step Setup

### 1. Get Your Stripe API Keys

1. Go to [Stripe Dashboard](https://dashboard.stripe.com)
2. Click **Developers** → **API keys**
3. You'll see:
   - **Publishable key** (starts with `pk_test_` for test mode)
   - **Secret key** (starts with `sk_test_` for test mode)
   - Click "Reveal" to see the secret key

### 2. Create a Product in Stripe

1. Go to **Products** → **+ Add product**
2. Set up:
   - **Name**: CV Builder Full Access
   - **Description**: Full access to CV Builder with all premium features
   - **Pricing**: One-time payment
   - **Price**: £9.99 GBP
3. Click **Save**
4. Copy the **Price ID** (starts with `price_`)

### 3. Add Environment Variables to Vercel

1. Go to your [Vercel Dashboard](https://vercel.com/dashboard)
2. Select your project (cv-app)
3. Go to **Settings** → **Environment Variables**
4. Add these variables:

```bash
# Test mode keys (for development)
VITE_STRIPE_PUBLISHABLE_KEY=pk_test_your_key_here
STRIPE_SECRET_KEY=sk_test_your_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
VITE_STRIPE_PRICE_ID=price_your_price_id_here
```

### 4. Set Up Webhook Endpoint

1. In Stripe Dashboard, go to **Developers** → **Webhooks**
2. Click **Add endpoint**
3. Endpoint URL: `https://simple-cv-builder.com/api/stripe/webhook`
4. Select these events:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `checkout.session.completed`
5. Copy the **Signing secret** (starts with `whsec_`)

### 5. Test It Out

Use Stripe's test card numbers:
- **Success**: `4242 4242 4242 4242`
- **Decline**: `4000 0000 0000 0002`
- Use any future expiry date and any 3-digit CVC

## When You're Ready for Live Payments

1. Switch to **Live mode** in Stripe Dashboard
2. Get your live keys:
   - `pk_live_...` (Publishable)
   - `sk_live_...` (Secret)
3. Update environment variables in Vercel with live keys
4. Create a live product with price ID
5. Update webhook to point to production URL
6. Monitor payments in Stripe Dashboard

## Security Notes

- **Never commit API keys to GitHub**
- Secrets go in Vercel environment variables, not in code
- Test thoroughly with test keys before going live
- Use Stripe's fraud prevention features

## What Happens After Payment

When a user pays:
1. Stripe sends webhook to `/api/stripe/webhook`
2. The webhook handler updates their subscription in the database
3. User gets full access to all features
4. They can use all templates, export PDFs, etc.

## Need Help?

- [Stripe Documentation](https://stripe.com/docs)
- [Stripe Testing Guide](https://stripe.com/docs/testing)
- Check webhook logs in Stripe Dashboard if payments aren't working
