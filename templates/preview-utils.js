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
