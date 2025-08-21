/**
 * Utility functions for formatting text content
 */

/**
 * Formats description text with proper line breaks by converting newlines to paragraphs
 * @param description - The text description that may contain newline characters
 * @returns Array of paragraphs (each paragraph is a string)
 */
export function formatDescription(description: string): string[] {
    if (!description) return [];

    // Split by newlines and filter out empty lines
    const lines = description.split('\n').filter(line => line.trim() !== '');

    // Group consecutive lines into paragraphs
    const paragraphs: string[] = [];
    let currentParagraph: string[] = [];

    for (const line of lines) {
        if (line.trim() === '') {
            // Empty line indicates paragraph break
            if (currentParagraph.length > 0) {
                paragraphs.push(currentParagraph.join(' '));
                currentParagraph = [];
            }
        } else {
            currentParagraph.push(line.trim());
        }
    }

    // Add the last paragraph if there is one
    if (currentParagraph.length > 0) {
        paragraphs.push(currentParagraph.join(' '));
    }

    return paragraphs;
}

/**
 * Formats description text with HTML line breaks (use with caution)
 * @param description - The text description that may contain newline characters
 * @returns HTML string with <br> tags for line breaks
 */
export function formatDescriptionWithHtml(description: string): string {
    if (!description) return '';
    // Convert newline characters to HTML line breaks
    return description.replace(/\n/g, '<br>');
}

/**
 * Formats description text with CSS white-space preservation
 * @param description - The text description that may contain newline characters
 * @returns The original text (to be used with CSS white-space: pre-line)
 */
export function formatDescriptionWithCss(description: string): string {
    if (!description) return '';
    // Return the original text - use CSS white-space: pre-line to preserve formatting
    return description;
}
