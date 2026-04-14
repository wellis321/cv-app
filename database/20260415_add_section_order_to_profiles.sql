-- Add section_order column to profiles for user-defined CV section ordering.
-- Stores a JSON array of section IDs in the user's preferred display order.
-- NULL means use the application default order.

ALTER TABLE profiles
    ADD COLUMN IF NOT EXISTS section_order JSON NULL AFTER sections_online;
