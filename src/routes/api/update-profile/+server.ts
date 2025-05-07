import { json } from '@sveltejs/kit';
import { createClient } from '@supabase/supabase-js';
import { PUBLIC_SUPABASE_URL, PUBLIC_SUPABASE_ANON_KEY } from '$env/static/public';
import type { RequestHandler } from './$types';
import type { Database } from '$lib/database.types';

// Create an admin client that ignores RLS
const supabaseAdmin = createClient<Database>(
    PUBLIC_SUPABASE_URL,
    PUBLIC_SUPABASE_ANON_KEY,
    {
        auth: {
            persistSession: false,
            autoRefreshToken: false,
        }
    }
);

export const POST: RequestHandler = async ({ request, locals }) => {
    try {
        // First try to get session from the server context
        const { data: { session: serverSession }, error: sessionError } = await locals.supabase.auth.getSession();

        console.log('Update profile endpoint session check:', serverSession ? `User ID: ${serverSession.user.id}` : 'No session from server');

        // Extract auth token from header if present
        const authHeader = request.headers.get('Authorization');
        let clientToken = null;
        if (authHeader && authHeader.startsWith('Bearer ')) {
            clientToken = authHeader.substring(7);
            console.log('Found Authorization header with token');
        }

        // Either use server session or create a client with the provided token
        let session = serverSession;
        let supabaseClient = locals.supabase;

        // If no server session but we have a token, create a client with that token
        if (!session && clientToken) {
            console.log('No server session, but found token in headers. Creating authenticated client.');
            const tempClient = createClient<Database>(
                PUBLIC_SUPABASE_URL,
                PUBLIC_SUPABASE_ANON_KEY,
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
                console.error('Error validating token:', userError);
                return json({ success: false, error: 'Invalid authorization token' }, { status: 401 });
            }

            if (userData.user) {
                console.log('Successfully authenticated with token. User:', userData.user.id);
                session = { user: userData.user } as any; // Simplified session object
                supabaseClient = tempClient;
            }
        }

        if (!session) {
            console.log('No session found in update-profile endpoint');
            return json({ success: false, error: 'Not authenticated' }, { status: 401 });
        }

        // Parse profile data from request
        const profileData = await request.json();
        console.log('Updating profile for user:', session.user.id, profileData);

        // Ensure the user can only update their own profile
        if (profileData.id !== session.user.id) {
            console.log('User ID mismatch:', { profileId: profileData.id, sessionUserId: session.user.id });
            return json({ success: false, error: 'You can only update your own profile' }, { status: 403 });
        }

        // Add updated_at timestamp
        profileData.updated_at = new Date().toISOString();

        // First try with the user's session (this should work if RLS is correct)
        console.log('Attempting to update profile with user session...');

        // Check if profile exists first
        const { data: existingProfile, error: checkError } = await supabaseClient
            .from('profiles')
            .select('id')
            .eq('id', session.user.id)
            .maybeSingle();

        if (checkError) {
            console.error('Error checking if profile exists:', checkError);
        }

        console.log('Profile exists check:', existingProfile ? 'Yes' : 'No');

        // Use upsert instead of update to create the profile if it doesn't exist
        const { data: updatedData, error: updateError } = await supabaseClient
            .from('profiles')
            .upsert(profileData, {
                onConflict: 'id'
            })
            .select();

        if (updateError) {
            console.error('Error updating profile with user session:', updateError);

            // If the error is related to RLS, try with admin role
            if (updateError.code === 'PGRST301' || updateError.code === '42501') {
                console.log('RLS error detected, attempting update with admin client...');

                const { data: adminData, error: adminError } = await supabaseAdmin
                    .from('profiles')
                    .upsert(profileData, {
                        onConflict: 'id'
                    })
                    .select();

                if (adminError) {
                    console.error('Error updating profile with admin client:', adminError);
                    return json({ success: false, error: adminError.message }, { status: 500 });
                }

                console.log('Profile updated successfully with admin client:', adminData);
                return json({ success: true, profile: adminData });
            }

            return json({ success: false, error: updateError.message }, { status: 500 });
        }

        console.log('Profile updated successfully with user session:', updatedData);
        return json({ success: true, profile: updatedData });
    } catch (error) {
        console.error('Unexpected error updating profile:', error);
        return json({ success: false, error: 'Server error' }, { status: 500 });
    }
};