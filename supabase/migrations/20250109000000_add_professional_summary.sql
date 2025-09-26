-- Create professional_summary table
CREATE TABLE IF NOT EXISTS public.professional_summary (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    profile_id UUID REFERENCES profiles(id) ON DELETE CASCADE,
    description TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL
);

-- Create professional_summary_strengths table
CREATE TABLE IF NOT EXISTS public.professional_summary_strengths (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    professional_summary_id UUID REFERENCES professional_summary(id) ON DELETE CASCADE,
    strength TEXT NOT NULL,
    sort_order INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL
);

-- Enable RLS
ALTER TABLE public.professional_summary ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.professional_summary_strengths ENABLE ROW LEVEL SECURITY;

-- Create RLS policies for professional_summary
CREATE POLICY "Users can manage their own professional summary"
    ON public.professional_summary FOR ALL
    USING (auth.uid() = profile_id);

-- Create RLS policies for professional_summary_strengths
CREATE POLICY "Users can view their own professional summary strengths"
    ON public.professional_summary_strengths FOR SELECT
    USING (EXISTS (
        SELECT 1 FROM professional_summary
        WHERE professional_summary.id = professional_summary_strengths.professional_summary_id
        AND professional_summary.profile_id = auth.uid()
    ));

CREATE POLICY "Users can insert their own professional summary strengths"
    ON public.professional_summary_strengths FOR INSERT
    WITH CHECK (EXISTS (
        SELECT 1 FROM professional_summary
        WHERE professional_summary.id = professional_summary_strengths.professional_summary_id
        AND professional_summary.profile_id = auth.uid()
    ));

CREATE POLICY "Users can update their own professional summary strengths"
    ON public.professional_summary_strengths FOR UPDATE
    USING (EXISTS (
        SELECT 1 FROM professional_summary
        WHERE professional_summary.id = professional_summary_strengths.professional_summary_id
        AND professional_summary.profile_id = auth.uid()
    ));

CREATE POLICY "Users can delete their own professional summary strengths"
    ON public.professional_summary_strengths FOR DELETE
    USING (EXISTS (
        SELECT 1 FROM professional_summary
        WHERE professional_summary.id = professional_summary_strengths.professional_summary_id
        AND professional_summary.profile_id = auth.uid()
    ));

-- Create trigger to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_professional_summary_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_professional_summary_updated_at
    BEFORE UPDATE ON public.professional_summary
    FOR EACH ROW
    EXECUTE FUNCTION update_professional_summary_updated_at();
