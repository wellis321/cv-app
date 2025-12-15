-- Allow certification dates to be optional
ALTER TABLE certifications
    MODIFY date_obtained DATE NULL,
    MODIFY expiry_date DATE NULL;
