-- Allow membership dates to be optional
ALTER TABLE professional_memberships
    MODIFY start_date DATE NULL,
    MODIFY end_date DATE NULL;
