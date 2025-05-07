import { json } from '@sveltejs/kit';
import { supabase } from '$lib/supabase';
import type { RequestHandler } from './$types';

// This endpoint will be available at /api/test-projects
export const GET: RequestHandler = async () => {
    try {
        // Get the current session
        const { data: { session } } = await supabase.auth.getSession();

        if (!session) {
            return json({
                success: false,
                error: 'Not authenticated',
                sessionStatus: 'No session found'
            }, { status: 401 });
        }

        // Fetch all projects from the database
        const { data: allProjects, error: allProjectsError } = await supabase
            .from('projects')
            .select('*')
            .limit(10);

        // Fetch user's projects
        const { data: userProjects, error: userProjectsError } = await supabase
            .from('projects')
            .select('*')
            .eq('profile_id', session.user.id);

        if (allProjectsError || userProjectsError) {
            return json({
                success: false,
                error: allProjectsError?.message || userProjectsError?.message,
                sessionStatus: 'Session found but query error',
                session: {
                    userId: session.user.id,
                    email: session.user.email
                }
            }, { status: 500 });
        }

        return json({
            success: true,
            allProjects: allProjects || [],
            userProjects: userProjects || [],
            session: {
                userId: session.user.id,
                email: session.user.email
            }
        });
    } catch (err) {
        console.error('Error in test-projects API:', err);
        return json({
            success: false,
            error: String(err),
            message: 'Unexpected error occurred'
        }, { status: 500 });
    }
};