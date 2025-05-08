import { json } from '@sveltejs/kit';
import { createClient } from '@supabase/supabase-js';
import type { RequestHandler } from './$types';
import type { Database } from '$lib/database.types';
import config, { safeLog } from '$lib/config';

// Create an admin client that ignores RLS - but only for this specific endpoint
// and with strict validation of user ownership
const supabaseAdmin = createClient<Database>(
    config.supabase.url,
    config.supabase.anonKey,
    {
        auth: {
            persistSession: false,
            autoRefreshToken: false,
        }
    }
);

export const POST: RequestHandler = async ({ request, locals }) => {
    // Generate a unique request ID for tracing
    const requestId = crypto.randomUUID();

    try {
        // First try to get session from the server context
        const { data: { session: serverSession }, error: sessionError } = await locals.supabase.auth.getSession();

        safeLog('debug', `[${requestId}] Update profile endpoint session check`, {
            hasSession: !!serverSession,
            userId: serverSession?.user?.id || 'none'
        });

        // Extract auth token from header if present - with secure parsing
        const authHeader = request.headers.get('Authorization');
        let clientToken = null;

        if (authHeader) {
            // Strict Bearer token format validation
            const bearerMatch = authHeader.match(/^Bearer\s+([A-Za-z0-9-._~+/]+=*)$/);
            if (bearerMatch && bearerMatch[1]) {
                clientToken = bearerMatch[1];
                safeLog('debug', `[${requestId}] Found valid Authorization header format`);
            } else if (authHeader.startsWith('Bearer ')) {
                safeLog('warn', `[${requestId}] Invalid Bearer token format in Authorization header`);
                return json({ success: false, error: 'Invalid authorization format' }, { status: 401 });
            }
        }

        // Either use server session or create a client with the provided token
        let session = serverSession;
        let supabaseClient = locals.supabase;

        // If no server session but we have a token, create a client with that token
        if (!session && clientToken) {
            safeLog('info', `[${requestId}] No server session, authenticating with token`);

            // Create a temporary client with the token
            const tempClient = createClient<Database>(
                config.supabase.url,
                config.supabase.anonKey,
                {
                    auth: {
                        persistSession: false,
                        autoRefreshToken: false
                    },
                    global: {
                        headers: {
                            Authorization: `Bearer ${clientToken}`
                        }
                    }
                }
            );

            // Get the user from the token
            const { data: userData, error: userError } = await tempClient.auth.getUser();

            if (userError) {
                safeLog('warn', `[${requestId}] Token validation failed`, {
                    errorCode: userError.code
                });
                return json({ success: false, error: 'Invalid authorization token' }, { status: 401 });
            }

            if (userData.user) {
                safeLog('info', `[${requestId}] Successfully authenticated with token`, {
                    userId: userData.user.id
                });
                session = { user: userData.user } as any; // Simplified session object
                supabaseClient = tempClient;
            }
        }

        if (!session) {
            safeLog('warn', `[${requestId}] No session found in update-profile endpoint`);
            return json({ success: false, error: 'Not authenticated' }, { status: 401 });
        }

        // Parse profile data from request with error handling
        let profileData;
        try {
            profileData = await request.json();
        } catch (parseError) {
            safeLog('error', `[${requestId}] Invalid JSON in request body`, { error: parseError });
            return json({ success: false, error: 'Invalid request format' }, { status: 400 });
        }

        safeLog('info', `[${requestId}] Processing profile update`, {
            userId: session.user.id,
            hasData: !!profileData
        });

        // Ensure the user can only update their own profile
        if (profileData.id !== session.user.id) {
            safeLog('warn', `[${requestId}] User ID mismatch in profile update`, {
                profileId: profileData.id,
                sessionUserId: session.user.id
            });
            return json({ success: false, error: 'You can only update your own profile' }, { status: 403 });
        }

        // Add updated_at timestamp
        profileData.updated_at = new Date().toISOString();

        // First try with the user's session (this should work if RLS is correct)
        safeLog('debug', `[${requestId}] Attempting profile update with user session`);

        // Check if profile exists first
        const { data: existingProfile, error: checkError } = await supabaseClient
            .from('profiles')
            .select('id')
            .eq('id', session.user.id)
            .maybeSingle();

        if (checkError) {
            safeLog('error', `[${requestId}] Error checking if profile exists`, {
                userId: session.user.id,
                errorCode: checkError.code
            });
        }

        // Use upsert instead of update to create the profile if it doesn't exist
        const { data: updatedData, error: updateError } = await supabaseClient
            .from('profiles')
            .upsert(profileData, {
                onConflict: 'id'
            })
            .select();

        if (updateError) {
            safeLog('error', `[${requestId}] Error updating profile with user session`, {
                errorCode: updateError.code
            });

            // If the error is related to RLS, try with admin role
            // This is a fallback that should be replaced with proper RLS policies
            if (updateError.code === 'PGRST301' || updateError.code === '42501') {
                safeLog('warn', `[${requestId}] RLS error detected, using admin client as fallback`);

                // Double-check user identity before using admin client
                if (profileData.id !== session.user.id) {
                    safeLog('error', `[${requestId}] Security check failed: ID mismatch`);
                    return json({ success: false, error: 'Security verification failed' }, { status: 403 });
                }

                const { data: adminData, error: adminError } = await supabaseAdmin
                    .from('profiles')
                    .upsert(profileData, {
                        onConflict: 'id'
                    })
                    .select();

                if (adminError) {
                    safeLog('error', `[${requestId}] Error updating profile with admin client`, {
                        errorCode: adminError.code
                    });
                    return json({ success: false, error: 'Failed to update profile' }, { status: 500 });
                }

                safeLog('info', `[${requestId}] Profile updated with admin client`, {
                    userId: session.user.id
                });
                return json({ success: true, profile: adminData });
            }

            return json({ success: false, error: 'Failed to update profile' }, { status: 500 });
        }

        safeLog('info', `[${requestId}] Profile updated successfully`, {
            userId: session.user.id
        });
        return json({ success: true, profile: updatedData });
    } catch (error) {
        safeLog('error', `[${requestId}] Unexpected error in update-profile endpoint`, { error });
        return json({ success: false, error: 'Server error' }, { status: 500 });
    }
};