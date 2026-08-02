-- Stores per-section column overrides (left/right) for the cv.php page's Edit Mode
-- drag-and-drop. Separate from sections_order (which only controls position within
-- whichever column a section is already in) and left untouched by the content-editor
-- sidebar's own reorder tool, which keeps its unrelated Main/Sidebar split.
ALTER TABLE profiles
    ADD COLUMN cv_page_columns JSON NULL DEFAULT NULL;
