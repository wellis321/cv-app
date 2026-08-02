/**
 * Shared utilities for CV preview templates.
 * Matches PHP renderMarkdown logic so **bold** and *italic* render correctly.
 */

/**
 * Escapes HTML special characters.
 */
export function escapeHtml(value) {
    if (value == null) return '';
    const div = document.createElement('div');
    div.textContent = String(value);
    return div.innerHTML;
}

// Same two-column split cv.php uses by default for its built-in renderer's Edit Mode.
const CV_SECTION_LEFT_DEFAULT = ['certifications', 'education', 'skills', 'interests'];
const CV_SECTION_RIGHT_DEFAULT = ['professional-summary', 'work-experience', 'projects', 'qualification-equivalence', 'memberships'];

/**
 * Computes which CV sections go in the left/right column, and in what order, from the same
 * section_order + cv_page_columns settings the owner can drag-and-drop on cv.php's Edit Mode -
 * keeps the preview panel and PDF export in sync with how the online CV is actually laid out.
 * Section ids match cv.php's template-id convention (kebab-case).
 * @param {string[]|null} sectionOrder
 * @param {object|null} cvPageColumns
 * @returns {{ left: string[], right: string[] }}
 */
export function resolveCvSectionLayout(sectionOrder, cvPageColumns) {
    let left = CV_SECTION_LEFT_DEFAULT.slice();
    let right = CV_SECTION_RIGHT_DEFAULT.slice();

    const overrides = cvPageColumns && typeof cvPageColumns === 'object' ? cvPageColumns : {};
    Object.keys(overrides).forEach((id) => {
        const col = overrides[id];
        if (col === 'left' && right.includes(id)) {
            right = right.filter((x) => x !== id);
            left = left.concat([id]);
        } else if (col === 'right' && left.includes(id)) {
            left = left.filter((x) => x !== id);
            right = right.concat([id]);
        }
    });

    if (Array.isArray(sectionOrder) && sectionOrder.length) {
        const pos = {};
        sectionOrder.forEach((id, index) => { pos[id] = index; });
        const sortFn = (a, b) => (pos[a] ?? 999) - (pos[b] ?? 999);
        left = left.sort(sortFn);
        right = right.sort(sortFn);
    }

    return { left, right };
}

/**
 * Same as resolveCvSectionLayout but as one flat list (no left/right split) - for single-column
 * template layouts where column placement doesn't apply, only relative order does.
 * @param {string[]|null} sectionOrder
 * @returns {string[]}
 */
export function resolveCvSectionOrderFlat(sectionOrder) {
    let ids = ['professional-summary', 'work-experience', 'education', 'certifications', 'skills', 'projects', 'qualification-equivalence', 'memberships', 'interests'];
    if (Array.isArray(sectionOrder) && sectionOrder.length) {
        const pos = {};
        sectionOrder.forEach((id, index) => { pos[id] = index; });
        ids = ids.slice().sort((a, b) => (pos[a] ?? 999) - (pos[b] ?? 999));
    }
    return ids;
}

/**
 * Renders markdown (**, *, line breaks, lists) to safe HTML.
 * Mirrors PHP renderMarkdown so preview matches cv.php output.
 */
export function renderMarkdown(text) {
    if (!text || typeof text !== 'string') return '';
    const escaped = escapeHtml(text);
    let out = escaped
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/(?<!\*)\*([^*]+?)\*(?!\*)/g, '<em>$1</em>');
    const ulStyle = 'list-style-type: disc; padding-left: 1.25em;';
    const olStyle = 'list-style-type: decimal; padding-left: 1.25em;';
    const lines = out.split(/\r?\n/);
    const result = [];
    let i = 0;
    while (i < lines.length) {
        const line = lines[i];
        if (/^\s*[-*•]\s+/.test(line)) {
            const items = [];
            while (i < lines.length && /^\s*[-*•]\s+/.test(lines[i])) {
                items.push(lines[i].replace(/^\s*[-*•]\s+/, ''));
                i++;
            }
            result.push('<ul style="' + ulStyle + '">' + items.map((x) => '<li>' + x + '</li>').join('') + '</ul>');
            continue;
        }
        if (/^\s*\d+\.\s+/.test(line)) {
            const items = [];
            while (i < lines.length && /^\s*\d+\.\s+/.test(lines[i])) {
                items.push(lines[i].replace(/^\s*\d+\.\s+/, ''));
                i++;
            }
            result.push('<ol style="' + olStyle + '">' + items.map((x) => '<li>' + x + '</li>').join('') + '</ol>');
            continue;
        }
        result.push(line);
        i++;
    }
    return result.join('<br>');
}
