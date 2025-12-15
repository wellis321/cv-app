-- Allow storing local project image references
ALTER TABLE projects
    ADD COLUMN image_path VARCHAR(255) NULL;
