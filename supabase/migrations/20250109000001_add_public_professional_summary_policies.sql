-- Add public access policies for professional summary tables to enable
-- viewing of public CVs without authentication

-- Professional Summary
CREATE POLICY "Public professional summary is viewable by everyone" ON professional_summary
    FOR SELECT USING (
        EXISTS (
            SELECT 1 FROM profiles
            WHERE profiles.id = professional_summary.profile_id
            AND profiles.username IS NOT NULL  -- Only show for profiles with a username
        )
    );

-- Professional Summary Strengths
CREATE POLICY "Public professional summary strengths are viewable by everyone" ON professional_summary_strengths
    FOR SELECT USING (
        EXISTS (
            SELECT 1 FROM professional_summary
            JOIN profiles ON profiles.id = professional_summary.profile_id
            WHERE professional_summary_strengths.professional_summary_id = professional_summary.id
            AND profiles.username IS NOT NULL
        )
    );
