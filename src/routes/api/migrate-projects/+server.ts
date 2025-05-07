import { json } from '@sveltejs/kit';
import { supabase } from '$lib/supabase';
import type { RequestHandler } from './$types';

export const POST: RequestHandler = async ({ request }) => {
    try {
        // Check authentication
        const { data: { session } } = await supabase.auth.getSession();
        if (!session) {
            return json({ success: false, error: 'Not authenticated' }, { status: 401 });
        }

        // Only allow fixing user's own data
        const requestData = await request.json();
        const userId = requestData.userId || session.user.id;

        if (userId !== session.user.id) {
            return json({ success: false, error: 'Not authorized to fix this user data' }, { status: 403 });
        }

        // Get all projects for the user
        const { data: projectsData, error: fetchError } = await supabase
            .from('projects')
            .select('*')
            .eq('profile_id', userId);

        if (fetchError) {
            return json({ success: false, error: fetchError.message }, { status: 500 });
        }

        // Update projects
        let updatedCount = 0;
        let errorCount = 0;
        let totalToFix = 0;

        for (const project of projectsData || []) {
            let updateData: Record<string, string> = {};
            let needsUpdate = false;

            // If project has title but not name
            if (project.title && (!project.name || project.name === null)) {
                updateData.name = project.title;
                needsUpdate = true;
            }

            // If project has name but not title
            if (project.name && (!project.title || project.title === null)) {
                updateData.title = project.name;
                needsUpdate = true;
            }

            if (needsUpdate) {
                totalToFix++;
                const { error: updateError } = await supabase
                    .from('projects')
                    .update(updateData)
                    .eq('id', project.id)
                    .eq('profile_id', userId);

                if (updateError) {
                    errorCount++;
                } else {
                    updatedCount++;
                }
            }
        }

        return json({
            success: true,
            results: {
                total: projectsData?.length || 0,
                totalToFix,
                updated: updatedCount,
                errors: errorCount
            }
        });
    } catch (err) {
        console.error('Error in projects migration API:', err);
        return json({ success: false, error: String(err) }, { status: 500 });
    }
};