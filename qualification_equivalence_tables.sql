-- Create professional_qualification_equivalence table
CREATE TABLE IF NOT EXISTS professional_qualification_equivalence (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    profile_id UUID REFERENCES profiles(id) ON DELETE CASCADE,
    level TEXT NOT NULL,
    description TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL
);

-- Create supporting_evidence table for qualification equivalence
CREATE TABLE IF NOT EXISTS supporting_evidence (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    qualification_equivalence_id UUID REFERENCES professional_qualification_equivalence(id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    sort_order INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL
);

-- Enable RLS
ALTER TABLE professional_qualification_equivalence ENABLE ROW LEVEL SECURITY;
ALTER TABLE supporting_evidence ENABLE ROW LEVEL SECURITY;

-- Create policies for professional qualification equivalence
CREATE POLICY "Users can manage their own qualification equivalence"
    ON professional_qualification_equivalence FOR ALL
    USING (auth.uid() = profile_id);

-- Create policies for supporting evidence
CREATE POLICY "Users can view their own supporting evidence"
    ON supporting_evidence FOR SELECT
    USING (EXISTS (
        SELECT 1 FROM professional_qualification_equivalence
        WHERE professional_qualification_equivalence.id = supporting_evidence.qualification_equivalence_id
        AND professional_qualification_equivalence.profile_id = auth.uid()
    ));

CREATE POLICY "Users can insert their own supporting evidence"
    ON supporting_evidence FOR INSERT
    WITH CHECK (EXISTS (
        SELECT 1 FROM professional_qualification_equivalence
        WHERE professional_qualification_equivalence.id = supporting_evidence.qualification_equivalence_id
        AND professional_qualification_equivalence.profile_id = auth.uid()
    ));

CREATE POLICY "Users can update their own supporting evidence"
    ON supporting_evidence FOR UPDATE
    USING (EXISTS (
        SELECT 1 FROM professional_qualification_equivalence
        WHERE professional_qualification_equivalence.id = supporting_evidence.qualification_equivalence_id
        AND professional_qualification_equivalence.profile_id = auth.uid()
    ));

CREATE POLICY "Users can delete their own supporting evidence"
    ON supporting_evidence FOR DELETE
    USING (EXISTS (
        SELECT 1 FROM professional_qualification_equivalence
        WHERE professional_qualification_equivalence.id = supporting_evidence.qualification_equivalence_id
        AND professional_qualification_equivalence.profile_id = auth.uid()
    ));