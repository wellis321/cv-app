/**
 * Global helper functions for use across the application
 */

import { browser } from '$app/environment';
import { getNonce, safeInjectScript } from './cspHelpers';

/**
 * Attach a global helper for safely injecting scripts
 * This makes it easier to write inline scripts that respect CSP
 */
if (browser) {
    // Add the function to window for global access
    (window as any).safeScript = (callback: Function): void => {
        try {
            const nonce = getNonce();
            if (!nonce) {
                console.warn('No CSP nonce found. Using direct execution fallback.');
                callback();
                return;
            }

            // Convert the function to a string and inject it with the nonce
            const functionString = callback.toString();
            const code = `(${functionString})();`;
            safeInjectScript(code);
        } catch (error) {
            console.error('Error executing safe script:', error);
            // Fallback to direct execution
            try {
                callback();
            } catch (e) {
                console.error('Fallback execution also failed:', e);
            }
        }
    };
}

/**
 * Initialize global helpers
 * Call this function in your root layout
 */
export function initGlobalHelpers(): void {
    // This function doesn't need to do anything else
    // since the browser check and function attachment happens at module level
}