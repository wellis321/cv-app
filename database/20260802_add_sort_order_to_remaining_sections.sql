-- Add sort_order to the section tables that don't already have it (work_experience and
-- certifications already support a persisted custom order; these six were previously
-- always auto-sorted by date/name/category with no way to save a manual order).
-- Enables drag-and-drop item reordering within a section on the CV page's Edit Mode.

ALTER TABLE education
    ADD COLUMN sort_order INT NOT NULL DEFAULT 0,
    ADD INDEX idx_education_sort (profile_id, sort_order);

ALTER TABLE skills
    ADD COLUMN sort_order INT NOT NULL DEFAULT 0,
    ADD INDEX idx_skills_sort (profile_id, sort_order);

ALTER TABLE projects
    ADD COLUMN sort_order INT NOT NULL DEFAULT 0,
    ADD INDEX idx_projects_sort (profile_id, sort_order);

ALTER TABLE professional_memberships
    ADD COLUMN sort_order INT NOT NULL DEFAULT 0,
    ADD INDEX idx_memberships_sort (profile_id, sort_order);

ALTER TABLE interests
    ADD COLUMN sort_order INT NOT NULL DEFAULT 0,
    ADD INDEX idx_interests_sort (profile_id, sort_order);

ALTER TABLE professional_qualification_equivalence
    ADD COLUMN sort_order INT NOT NULL DEFAULT 0,
    ADD INDEX idx_qualification_equivalence_sort (profile_id, sort_order);
