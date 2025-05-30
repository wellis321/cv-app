-- Update existing subscription plans to use GBP instead of USD
UPDATE public.subscription_plans
SET currency = 'GBP',
    updated_at = CURRENT_TIMESTAMP
WHERE currency = 'USD';

-- Update prices for Premium and Premium Annual plans
UPDATE public.subscription_plans
SET price = 7.99,
    updated_at = CURRENT_TIMESTAMP
WHERE name = 'Premium' AND interval = 'month';

UPDATE public.subscription_plans
SET price = 79.99,
    updated_at = CURRENT_TIMESTAMP
WHERE name = 'Premium Annual' AND interval = 'year';

-- Update templates in premium plans to include all 12 templates
UPDATE public.subscription_plans
SET features = jsonb_set(
    features,
    '{templates}',
    '["basic", "professional", "modern", "creative", "executive", "simple", "classic", "elegant", "minimalist", "bold", "academic", "technical"]'::jsonb
)
WHERE price > 0;

-- Double-check that all plans are now using GBP
SELECT id, name, price, currency, interval FROM public.subscription_plans;