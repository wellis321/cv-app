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
        const requestData = await request.json().catch(() => ({}));
        const userId = requestData.userId || session.user.id;

        if (userId !== session.user.id) {
            return json({ success: false, error: 'Not authorized to fix this user data' }, { status: 403 });
        }

        const results = {
            education: { total: 0, updated: 0, errors: 0 },
            projects: { total: 0, updated: 0, errors: 0 }
        };

        // Step 1: Fix education entries
        try {
            // Get all education entries for the user
            const { data: educationData, error: eduFetchError } = await supabase
                .from('education')
                .select('*')
                .eq('profile_id', userId);

            if (eduFetchError) {
                console.error('Error fetching education:', eduFetchError);
                results.education.errors++;
            } else {
                results.education.total = educationData?.length || 0;

                // Update entries with missing fields
                for (const edu of educationData || []) {
                    let updateData = {};
                    let needsUpdate = false;

                    // If education has degree but not qualification
                    if (edu.degree && (!edu.qualification || edu.qualification === null)) {
                        updateData = { ...updateData, qualification: edu.degree };
                        needsUpdate = true;
                    }

                    // If education has qualification but not degree
                    if (edu.qualification && (!edu.degree || edu.degree === null)) {
                        updateData = { ...updateData, degree: edu.qualification };
                        needsUpdate = true;
                    }

                    if (needsUpdate) {
                        const { error: updateError } = await supabase
                            .from('education')
                            .update(updateData)
                            .eq('id', edu.id)
                            .eq('profile_id', userId);

                        if (updateError) {
                            results.education.errors++;
                        } else {
                            results.education.updated++;
                        }
                    }
                }
            }
        } catch (eduErr) {
            console.error('Error fixing education data:', eduErr);
            results.education.errors++;
        }

        // Step 2: Fix projects
        try {
            // Get all projects for the user
            const { data: projectsData, error: projFetchError } = await supabase
                .from('projects')
                .select('*')
                .eq('profile_id', userId);

            if (projFetchError) {
                console.error('Error fetching projects:', projFetchError);
                results.projects.errors++;
            } else {
                results.projects.total = projectsData?.length || 0;

                // Update projects with missing fields
                for (const project of projectsData || []) {
                    let updateData = {};
                    let needsUpdate = false;

                    // If project has title but not name
                    if (project.title && (!project.name || project.name === null)) {
                        updateData = { ...updateData, name: project.title };
                        needsUpdate = true;
                    }

                    // If project has name but not title
                    if (project.name && (!project.title || project.title === null)) {
                        updateData = { ...updateData, title: project.name };
                        needsUpdate = true;
                    }

                    if (needsUpdate) {
                        const { error: updateError } = await supabase
                            .from('projects')
                            .update(updateData)
                            .eq('id', project.id)
                            .eq('profile_id', userId);

                        if (updateError) {
                            results.projects.errors++;
                        } else {
                            results.projects.updated++;
                        }
                    }
                }
            }
        } catch (projErr) {
            console.error('Error fixing projects data:', projErr);
            results.projects.errors++;
        }

        return json({
            success: true,
            results,
            session: {
                userId: session.user.id,
                email: session.user.email
            }
        });
    } catch (err) {
        console.error('Error in fix-all-data API:', err);
        return json({
            success: false,
            error: String(err),
            message: 'Unexpected error occurred'
        }, { status: 500 });
    }
};