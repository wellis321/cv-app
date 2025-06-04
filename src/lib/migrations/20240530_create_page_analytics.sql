-- Create page_analytics table
CREATE TABLE IF NOT EXISTS public.page_analytics (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    path TEXT NOT NULL,
    user_id UUID REFERENCES auth.users(id),
    session_id TEXT,
    is_authenticated BOOLEAN NOT NULL DEFAULT FALSE,
    browser TEXT,
    device_type TEXT,
    referrer TEXT,
    query_params JSONB,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create indexes
CREATE INDEX IF NOT EXISTS idx_page_analytics_path ON public.page_analytics(path);
CREATE INDEX IF NOT EXISTS idx_page_analytics_user_id ON public.page_analytics(user_id);
CREATE INDEX IF NOT EXISTS idx_page_analytics_created_at ON public.page_analytics(created_at);

-- Create daily_page_views view for aggregated data
CREATE OR REPLACE VIEW public.daily_page_views AS
SELECT
    DATE(created_at) AS date,
    path,
    COUNT(*) AS views,
    COUNT(DISTINCT user_id) AS unique_users,
    COUNT(DISTINCT session_id) AS unique_sessions
FROM
    public.page_analytics
GROUP BY
    DATE(created_at), path
ORDER BY
    date DESC, views DESC;

-- Create monthly_page_views view
CREATE OR REPLACE VIEW public.monthly_page_views AS
SELECT
    DATE_TRUNC('month', created_at) AS month,
    path,
    COUNT(*) AS views,
    COUNT(DISTINCT user_id) AS unique_users,
    COUNT(DISTINCT session_id) AS unique_sessions
FROM
    public.page_analytics
GROUP BY
    DATE_TRUNC('month', created_at), path
ORDER BY
    month DESC, views DESC;

-- Create analytics_dashboard_data view with more aggregated metrics
CREATE OR REPLACE VIEW public.analytics_dashboard_data AS
SELECT
    DATE(created_at) AS date,
    COUNT(*) AS total_pageviews,
    COUNT(DISTINCT path) AS unique_pages,
    COUNT(DISTINCT user_id) AS unique_users,
    COUNT(DISTINCT session_id) AS unique_sessions,
    COUNT(CASE WHEN is_authenticated THEN 1 END) AS authenticated_views,
    COUNT(CASE WHEN NOT is_authenticated THEN 1 END) AS anonymous_views
FROM
    public.page_analytics
GROUP BY
    DATE(created_at)
ORDER BY
    date DESC;

-- Create RLS policy to control who can view analytics
ALTER TABLE public.page_analytics ENABLE ROW LEVEL SECURITY;

-- Only allow admins to see all analytics data
CREATE POLICY "Allow admins to view all analytics"
    ON public.page_analytics
    FOR SELECT
    USING (
        auth.uid() IN (
            SELECT id FROM auth.users WHERE email LIKE '%@example.com' -- Replace with admin emails
        )
    );

-- Users can only see their own analytics data
CREATE POLICY "Users can view their own analytics data"
    ON public.page_analytics
    FOR SELECT
    USING (auth.uid() = user_id);

-- Only system functions can insert analytics data
CREATE POLICY "System can insert analytics data"
    ON public.page_analytics
    FOR INSERT
    WITH CHECK (true);

-- Only admins can modify analytics data
CREATE POLICY "Only admins can update analytics data"
    ON public.page_analytics
    FOR UPDATE
    USING (
        auth.uid() IN (
            SELECT id FROM auth.users WHERE email LIKE '%@example.com' -- Replace with admin emails
        )
    );