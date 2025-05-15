/**
 * Utilities for working with Content Security Policy (CSP)
 */

import { browser } from '$app/environment';

/**
 * Get the CSP nonce from the document head
 */
export function getNonce(): string | null {
    if (!browser) return null;

    // First try to get from meta tag (for initial page load)
    const metaNonce = document.querySelector('meta[name="csp-nonce"]');
    if (metaNonce) {
        return metaNonce.getAttribute('content');
    }

    // Then try to get from header (for subsequent requests)
    // This requires the server to send the X-CSP-Nonce header
    return null;
}

/**
 * Safely inject a script with the current nonce
 * @param code - JavaScript code to execute
 * @param async - Whether to load the script asynchronously
 */
export function safeInjectScript(code: string, async = false): HTMLScriptElement | null {
    if (!browser) return null;

    const nonce = getNonce();
    if (!nonce) {
        console.warn('No CSP nonce found. Script injection might be blocked.');
    }

    const script = document.createElement('script');
    script.textContent = code;

    // Apply nonce if available
    if (nonce) {
        script.setAttribute('nonce', nonce);
    }

    if (async) {
        script.async = true;
    }

    document.head.appendChild(script);
    return script;
}

/**
 * Safely load an external script with the current nonce
 * @param src - Source URL of the script
 * @param async - Whether to load the script asynchronously
 */
export function safeLoadScript(src: string, async = true): HTMLScriptElement | null {
    if (!browser) return null;

    const nonce = getNonce();
    if (!nonce) {
        console.warn('No CSP nonce found. Script loading might be blocked.');
    }

    const script = document.createElement('script');
    script.src = src;

    // Apply nonce if available
    if (nonce) {
        script.setAttribute('nonce', nonce);
    }

    if (async) {
        script.async = true;
    }

    document.head.appendChild(script);
    return script;
}