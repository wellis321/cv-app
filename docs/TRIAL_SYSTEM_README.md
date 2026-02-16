# Subscription & Trial System

This app uses a **4-plan model** with 7-day free trials on all paid plans:

1. **Basic access (Free)** — CV builder, templates, limited job tracking & AI. PDF export.
2. **1 week** — £4.99/week. 7-day free trial. Cancel anytime.
3. **1 month** — £14.99/month. 7-day free trial. Most popular. Cancel anytime.
4. **3 months** — £34.99 every 3 months. 7-day free trial. Best value. Cancel anytime.

Pro Annual, Pro Trial 7-day (legacy), and Lifetime exist in the backend for existing users but are **not shown** in marketing/pricing.

## How it works

1. **New users** register and start on the **free plan**.
2. **Paid plans**: User selects 1 week, 1 month, or 3 months. Stripe Checkout creates a subscription with `trial_period_days=7`. User gets 7 days free, then is charged (weekly, monthly, or every 3 months).
3. **Trial end**: If they cancel before the trial ends, they stay on free. Otherwise they're charged and continue with full access.

## Implementation

- **Marketing plans** (`getMarketingPlanIds()`): `free`, `pro_1week`, `pro_monthly`, `pro_3month`.
- **Full plans** (`getSubscriptionPlansConfig()`): All plans for backend (existing users, legacy).

### Environment

```
STRIPE_PRICE_PRO_1WEEK=price_1T1GcsEMgRyvTqUXq1uIM8dT  # £4.99/week, recurring
STRIPE_PRICE_PRO_MONTHLY=price_1T1GcuEMgRyvTqUXcBTpflSH  # £14.99/month, recurring
STRIPE_PRICE_PRO_3MONTH=price_1T1GcvEMgRyvTqUXWF8UdH7C  # £34.99 every 3 months, recurring
```

All three use Stripe subscription mode with `subscription_data[trial_period_days]=7`.
