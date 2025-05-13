import { browser } from '$app/environment';
import { getCsrfHeaderName } from './csrf';

/**
 * Get the CSRF token from the meta tag
 */
export function getCsrfTokenFromDocument(): string | null {
    if (!browser) return null;

    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? (metaTag as HTMLMetaElement).content : null;
}

/**
 * Add CSRF token to a fetch configuration
 */
export function addCsrfToFetchConfig(config: RequestInit = {}): RequestInit {
    if (!browser) return config;

    const token = getCsrfTokenFromDocument();
    if (!token) {
        console.warn('CSRF token not found in document');
        return config;
    }

    // Create headers if they don't exist
    const headers = new Headers(config.headers || {});

    // Add the CSRF token header
    headers.set(getCsrfHeaderName(), token);

    return {
        ...config,
        headers
    };
}

/**
 * Fetch with CSRF protection
 */
export async function fetchWithCsrf(url: string, config: RequestInit = {}): Promise<Response> {
    // Add CSRF token to the request
    const csrfConfig = addCsrfToFetchConfig(config);

    // Perform the fetch with the updated config
    return fetch(url, csrfConfig);
}

/**
 * API client with CSRF protection
 */
export const api = {
    get: async <T>(url: string, config: RequestInit = {}): Promise<T> => {
        const response = await fetchWithCsrf(url, { ...config, method: 'GET' });
        if (!response.ok) throw new Error(`API error: ${response.status}`);
        return response.json();
    },

    post: async <T>(url: string, data: any, config: RequestInit = {}): Promise<T> => {
        try {
            // Debug log the request details (remove in production)
            if (typeof window !== 'undefined' && window.location.hostname === 'localhost') {
                console.debug('API Request:', {
                    url,
                    method: 'POST',
                    contentType: 'application/json',
                    hasToken: config.headers && 'Authorization' in (config.headers as any),
                    hasCsrf: getCsrfTokenFromDocument() !== null
                });
            }

            const response = await fetchWithCsrf(url, {
                ...config,
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...config.headers
                },
                body: JSON.stringify(data)
            });

            // If response is not OK, try to get more detailed error info
            if (!response.ok) {
                const errorText = await response.text();
                let errorMessage = `API error: ${response.status}`;

                try {
                    // Try to parse error as JSON
                    const errorJson = JSON.parse(errorText);
                    if (errorJson.error) {
                        errorMessage = errorJson.error;
                        // Also check for more detailed message field
                        if (errorJson.message) {
                            errorMessage += `: ${errorJson.message}`;
                        }
                    }
                } catch (e) {
                    // If the error text can't be parsed as JSON, use the raw text if available
                    if (errorText) {
                        errorMessage = `${errorMessage} - ${errorText}`;
                    }
                }

                throw new Error(errorMessage);
            }

            return response.json();
        } catch (error) {
            console.error('API POST request failed:', error);
            throw error;
        }
    },

    put: async <T>(url: string, data: any, config: RequestInit = {}): Promise<T> => {
        const response = await fetchWithCsrf(url, {
            ...config,
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                ...config.headers
            },
            body: JSON.stringify(data)
        });
        if (!response.ok) throw new Error(`API error: ${response.status}`);
        return response.json();
    },

    delete: async <T>(url: string, config: RequestInit = {}): Promise<T> => {
        const response = await fetchWithCsrf(url, {
            ...config,
            method: 'DELETE'
        });
        if (!response.ok) throw new Error(`API error: ${response.status}`);
        return response.json();
    },

    patch: async <T>(url: string, data: any, config: RequestInit = {}): Promise<T> => {
        const response = await fetchWithCsrf(url, {
            ...config,
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                ...config.headers
            },
            body: JSON.stringify(data)
        });
        if (!response.ok) throw new Error(`API error: ${response.status}`);
        return response.json();
    }
};