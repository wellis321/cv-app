-- Create admin_users table first
CREATE TABLE IF NOT EXISTS public.admin_users (
    email VARCHAR(255) PRIMARY KEY,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Create subscription_plans table
CREATE TABLE IF NOT EXISTS public.subscription_plans (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'GBP',
    interval VARCHAR(20) NOT NULL DEFAULT 'month', -- 'month', 'year', etc.
    features JSONB DEFAULT '{}',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Add subscription fields to profiles table
ALTER TABLE public.profiles
ADD COLUMN IF NOT EXISTS subscription_plan_id UUID REFERENCES public.subscription_plans(id),
ADD COLUMN IF NOT EXISTS subscription_expires_at TIMESTAMP WITH TIME ZONE;

-- Create RLS policies for subscription_plans
ALTER TABLE public.subscription_plans ENABLE ROW LEVEL SECURITY;

-- Allow all users to read subscription plans
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_policy
        WHERE polname = 'Allow users to view subscription plans'
        AND polrelid = 'public.subscription_plans'::regclass
    ) THEN
        CREATE POLICY "Allow users to view subscription plans"
        ON public.subscription_plans FOR SELECT
        TO authenticated
        USING (true);
    END IF;
END
$$;

-- Allow only admins to modify subscription plans
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_policy
        WHERE polname = 'Allow admins to modify subscription plans'
        AND polrelid = 'public.subscription_plans'::regclass
    ) THEN
        CREATE POLICY "Allow admins to modify subscription plans"
        ON public.subscription_plans FOR ALL
        TO authenticated
        USING (auth.jwt() ->> 'email' IN (SELECT email FROM public.admin_users));
    END IF;
END
$$;

-- Insert default subscription plans
INSERT INTO public.subscription_plans (name, description, price, currency, interval, features, is_active)
VALUES
('Free', 'Basic CV features', 0, 'GBP', 'month', '{"max_sections": 3, "pdf_export": false, "online_cv": true, "templates": ["basic"]}', true),
('Premium', 'Full CV features with multiple templates', 7.99, 'GBP', 'month', '{"max_sections": -1, "pdf_export": true, "online_cv": true, "templates": ["basic", "professional", "modern", "creative", "executive", "simple", "classic", "elegant", "minimalist", "bold", "academic", "technical"]}', true),
('Premium Annual', 'Full CV features with multiple templates', 79.99, 'GBP', 'year', '{"max_sections": -1, "pdf_export": true, "online_cv": true, "templates": ["basic", "professional", "modern", "creative", "executive", "simple", "classic", "elegant", "minimalist", "bold", "academic", "technical"]}', true)
ON CONFLICT (id) DO NOTHING;

-- Create or replace function to update the updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Add trigger to subscription_plans table
DROP TRIGGER IF EXISTS update_subscription_plans_updated_at ON public.subscription_plans;
CREATE TRIGGER update_subscription_plans_updated_at
BEFORE UPDATE ON public.subscription_plans
FOR EACH ROW
EXECUTE FUNCTION update_updated_at();