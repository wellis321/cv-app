import { createSupabaseServerClient } from '@supabase/auth-helpers-sveltekit';
import { PUBLIC_SUPABASE_URL, PUBLIC_SUPABASE_ANON_KEY } from '$env/static/public';
import type { Handle } from '@sveltejs/kit';
import { redirect, error } from '@sveltejs/kit';

// List of public routes that don't require authentication
const publicRoutes = ['/', '/login', '/signup'];

export const handle: Handle = async ({ event, resolve }) => {
    try {
        // Create a Supabase client specifically for this request
        event.locals.supabase = createSupabaseServerClient({
            supabaseUrl: PUBLIC_SUPABASE_URL,
            supabaseKey: PUBLIC_SUPABASE_ANON_KEY,
            event
        });

        // Get the session from the request
        const { data: { session }, error: sessionError } = await event.locals.supabase.auth.getSession();

        // Add session to event.locals
        if (sessionError) {
            console.error('Error getting session in hooks:', sessionError);
            event.locals.session = null;
        } else if (session) {
            // Set the session in locals
            event.locals.session = session;

            // Optionally verify the user exists in profiles
            try {
                const { data: profile, error: profileError } = await event.locals.supabase
                    .from('profiles')
                    .select('id')
                    .eq('id', session.user.id)
                    .maybeSingle();

                if (profileError && profileError.code !== 'PGRST116') {
                    console.error('Error verifying profile in hooks:', profileError);
                }

                // Create profile if it doesn't exist
                if (!profile) {
                    const { error: insertError } = await event.locals.supabase
                        .from('profiles')
                        .insert({
                            id: session.user.id,
                            email: session.user.email,
                            updated_at: new Date().toISOString()
                        });

                    if (insertError) {
                        console.error('Error creating profile in hooks:', insertError);
                    }
                }
            } catch (profileError) {
                console.error('Error checking profile:', profileError);
            }
        } else {
            // No session
            event.locals.session = null;
        }

        // Check if this is a protected route and redirect if needed
        const path = event.url.pathname;
        const isPublicRoute = publicRoutes.some(route => path === route || path.startsWith(`${route}/`));

        if (!isPublicRoute && !event.locals.session) {
            // Only redirect if not an API route
            if (!path.startsWith('/api')) {
                // Add current path to redirect back after login
                const returnTo = encodeURIComponent(path);
                throw redirect(303, `/?returnTo=${returnTo}`);
            }
        }
    } catch (hookError) {
        if (hookError instanceof Response && hookError.status === 303) {
            throw hookError;
        }

        // For other errors, set session to null
        event.locals.session = null;
    }

    // Process the request and get the response
    const response = await resolve(event);

    return response;
};
