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

/**
 * Renders markdown (**, *, line breaks) to safe HTML.
 * Mirrors PHP renderMarkdown so preview matches cv.php output.
 */
export function renderMarkdown(text) {
    if (!text || typeof text !== 'string') return '';
    const escaped = escapeHtml(text);
    return escaped
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/(?<!\*)\*([^*]+?)\*(?!\*)/g, '<em>$1</em>')
        .replace(/\n/g, '<br>');
}
