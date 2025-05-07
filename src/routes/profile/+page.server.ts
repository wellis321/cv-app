import type { Actions, PageServerLoad } from './$types';
import { fail, redirect } from '@sveltejs/kit';

export const load: PageServerLoad = async ({ locals }) => {
    try {
        const { data: { session } } = await locals.supabase.auth.getSession();
        console.log('Session in load:', session ? 'Session exists' : 'No session');

        if (!session) {
            console.log('No session found, returning empty profile');
            return { profile: null, session: null };
        }

        console.log('User ID:', session.user.id);

        // Check if the user has a profile already
        const { data, error } = await locals.supabase
            .from('profiles')
            .select('*')
            .eq('id', session.user.id)
            .maybeSingle();

        console.log('Profile load result:', { data, error });

        if (error) {
            console.error('Error loading profile:', error);
            return { profile: null, error: error.message, session };
        }

        if (!data) {
            console.log('No profile found for user, this may be a new user');
            // Create default profile data with email from session
            const defaultProfile = {
                id: session.user.id,
                email: session.user.email,
                full_name: '',
                phone: '',
                location: ''
            };
            return { profile: defaultProfile, session };
        }

        return { profile: data, session };
    } catch (error) {
        console.error('Unexpected error in profile load:', error);
        return { profile: null, error: 'An unexpected error occurred', session: null };
    }
};

export const actions: Actions = {
    default: async ({ request, locals }) => {
        try {
            const { data: { session } } = await locals.supabase.auth.getSession();
            console.log('Session in action:', session ? 'Session exists' : 'No session');

            if (!session) {
                console.error('No session found during profile save');
                return fail(401, { error: 'Not authenticated' });
            }

            const formData = await request.formData();
            const fullName = formData.get('fullName') as string;
            const email = formData.get('email') as string;
            const phone = formData.get('phone') as string;
            const location = formData.get('location') as string;

            // Log form data
            console.log('Form data:', { fullName, email, phone, location });
            console.log('User ID for profile save:', session.user.id);

            // Create the profile data object
            const profileData = {
                id: session.user.id,
                full_name: fullName || null,
                email: email || null,
                phone: phone || null,
                location: location || null,
                updated_at: new Date().toISOString()
            };

            // Log the profile data being sent
            console.log('Profile data being sent:', profileData);

            // Try to upsert the profile
            const { data, error } = await locals.supabase
                .from('profiles')
                .upsert(profileData, { onConflict: 'id' });

            // Log the complete response
            console.log('UPSERT RESULT:', { data, error });

            if (error) {
                console.error('Profile save error:', error);
                return fail(400, { error: error.message });
            }

            console.log('Profile saved successfully');
            throw redirect(303, '/profile');
        } catch (error) {
            console.error('Unexpected error saving profile:', error);
            return fail(500, { error: 'An unexpected error occurred' });
        }
    }
};