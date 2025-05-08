import { createSupabaseServerClient } from '@supabase/auth-helpers-sveltekit';
import config, { safeLog } from '$lib/config';
import type { Handle } from '@sveltejs/kit';
import { redirect, error } from '@sveltejs/kit';

// List of public routes that don't require authentication
const publicRoutes = ['/', '/login', '/signup', '/security-review-client'];

export const handle: Handle = async ({ event, resolve }) => {
    try {
        const path = event.url.pathname;
        const requestId = crypto.randomUUID(); // Generate a unique ID for this request

        // Use safeLog instead of console.log for secure logging
        safeLog('debug', `[${requestId}] Processing request`, { path });

        // Create a Supabase client specifically for this request
        event.locals.supabase = createSupabaseServerClient({
            supabaseUrl: config.supabase.url,
            supabaseKey: config.supabase.anonKey,
            event
        });

        // Get the session from the request
        const { data: { session }, error: sessionError } = await event.locals.supabase.auth.getSession();

        // Add session to event.locals
        if (sessionError) {
            safeLog('error', `[${requestId}] Error getting session`, {
                path,
                errorCode: sessionError.code,
                errorMessage: sessionError.message
            });
            event.locals.session = null;
        } else if (session) {
            // Set the session in locals
            event.locals.session = session;
            safeLog('debug', `[${requestId}] Session found for user`, {
                userId: session.user.id,
                path
            });

            // Optionally verify the user exists in profiles
            try {
                const { data: profile, error: profileError } = await event.locals.supabase
                    .from('profiles')
                    .select('id')
                    .eq('id', session.user.id)
                    .maybeSingle();

                if (profileError && profileError.code !== 'PGRST116') {
                    safeLog('error', `[${requestId}] Error verifying profile`, {
                        userId: session.user.id,
                        errorCode: profileError.code
                    });
                }

                // Create profile if it doesn't exist
                if (!profile) {
                    safeLog('info', `[${requestId}] Creating new profile`, { userId: session.user.id });
                    const { error: insertError } = await event.locals.supabase
                        .from('profiles')
                        .insert({
                            id: session.user.id,
                            email: session.user.email,
                            updated_at: new Date().toISOString()
                        });

                    if (insertError) {
                        safeLog('error', `[${requestId}] Error creating profile`, {
                            userId: session.user.id,
                            errorCode: insertError.code
                        });
                    }
                }
            } catch (profileError) {
                safeLog('error', `[${requestId}] Unexpected error checking profile`, { error: profileError });
            }
        } else {
            // No session
            event.locals.session = null;
            safeLog('debug', `[${requestId}] No session found`, { path });
        }

        // Check if this is a protected route and redirect if needed
        const isPublicRoute = publicRoutes.some(route => path === route || path.startsWith(`${route}/`));

        safeLog('debug', `[${requestId}] Route check`, {
            path,
            isPublic: isPublicRoute,
            hasSession: !!event.locals.session
        });

        if (!isPublicRoute && !event.locals.session) {
            // Only redirect if not an API route
            if (!path.startsWith('/api')) {
                safeLog('info', `[${requestId}] Redirecting unauthenticated user`, { from: path });
                // Add current path to redirect back after login
                const returnTo = encodeURIComponent(path);
                throw redirect(303, `/?returnTo=${returnTo}`);
            }
        }
    } catch (hookError) {
        if (hookError instanceof Response && hookError.status === 303) {
            throw hookError;
        }

        safeLog('error', 'Unexpected error in hooks', { error: hookError });
        // For other errors, set session to null
        event.locals.session = null;
    }

    // Process the request and get the response
    const response = await resolve(event);

    // Add security headers if enabled
    if (config.security.strictHeaders) {
        // Set security headers
        response.headers.set('X-Content-Type-Options', 'nosniff');
        response.headers.set('X-Frame-Options', 'DENY');
        response.headers.set('X-XSS-Protection', '1; mode=block');
        response.headers.set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Only set in production - in dev we need to allow inline scripts for HMR
        if (config.isProduction) {
            response.headers.set('Content-Security-Policy',
                "default-src 'self'; " +
                "script-src 'self' 'unsafe-inline'; " +
                "style-src 'self' 'unsafe-inline'; " +
                "img-src 'self' data: blob:; " +
                "font-src 'self'; " +
                "connect-src 'self' https://*.supabase.co;"
            );
        }
    }

    return response;
};
