import { redirect } from '@sveltejs/kit';
import { supabase } from '$lib/supabase';
import type { PageServerLoad, Actions } from './$types';

export const load: PageServerLoad = async ({ locals }) => {
    // Check if user is authenticated
    const { data: { session } } = await supabase.auth.getSession();

    if (!session) {
        throw redirect(302, '/');
    }

    // Get data stats for this user
    try {
        // Check education issues
        const { data: educationData, error: eduError } = await supabase
            .from('education')
            .select('*')
            .eq('profile_id', session.user.id);

        let educationNeedsUpdate = 0;
        for (const edu of educationData || []) {
            if ((edu.degree && !edu.qualification) || (edu.qualification && !edu.degree)) {
                educationNeedsUpdate++;
            }
        }

        // Check project issues
        const { data: projectsData, error: projError } = await supabase
            .from('projects')
            .select('*')
            .eq('profile_id', session.user.id);

        let projectsNeedsUpdate = 0;
        for (const project of projectsData || []) {
            if ((project.title && !project.name) || (project.name && !project.title)) {
                projectsNeedsUpdate++;
            }
        }

        return {
            user: session.user,
            stats: {
                education: {
                    total: educationData?.length || 0,
                    needsUpdate: educationNeedsUpdate
                },
                projects: {
                    total: projectsData?.length || 0,
                    needsUpdate: projectsNeedsUpdate
                }
            }
        };
    } catch (err) {
        console.error('Error getting data stats:', err);
        return {
            user: session.user,
            stats: {
                education: { total: 0, needsUpdate: 0 },
                projects: { total: 0, needsUpdate: 0 }
            }
        };
    }
};

export const actions: Actions = {
    fixEducation: async ({ request }) => {
        const { data: { session } } = await supabase.auth.getSession();

        if (!session) {
            return { success: false, error: 'Not authenticated' };
        }

        try {
            // Find education entries for this user
            const { data: educationData, error: fetchError } = await supabase
                .from('education')
                .select('*')
                .eq('profile_id', session.user.id);

            if (fetchError) {
                return { success: false, error: fetchError.message };
            }

            // Update education entries that need fixing
            let updatedCount = 0;
            let totalToUpdate = 0;

            for (const edu of educationData || []) {
                let updateData: Record<string, string> = {};
                let needsUpdate = false;

                // If education has degree but not qualification
                if (edu.degree && (!edu.qualification || edu.qualification === null)) {
                    updateData.qualification = edu.degree;
                    needsUpdate = true;
                }

                // If education has qualification but not degree
                if (edu.qualification && (!edu.degree || edu.degree === null)) {
                    updateData.degree = edu.qualification;
                    needsUpdate = true;
                }

                if (needsUpdate) {
                    totalToUpdate++;
                    const { error: updateError } = await supabase
                        .from('education')
                        .update(updateData)
                        .eq('id', edu.id);

                    if (!updateError) {
                        updatedCount++;
                    }
                }
            }

            return {
                success: true,
                stats: {
                    total: educationData?.length || 0,
                    needsUpdate: totalToUpdate,
                    updated: updatedCount
                }
            };
        } catch (err) {
            console.error('Error fixing education data:', err);
            return { success: false, error: String(err) };
        }
    },

    fixProjects: async ({ request }) => {
        const { data: { session } } = await supabase.auth.getSession();

        if (!session) {
            return { success: false, error: 'Not authenticated' };
        }

        try {
            // Find projects for this user
            const { data: projectsData, error: fetchError } = await supabase
                .from('projects')
                .select('*')
                .eq('profile_id', session.user.id);

            if (fetchError) {
                return { success: false, error: fetchError.message };
            }

            // Update projects that need fixing
            let updatedCount = 0;
            let totalToUpdate = 0;

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
                    totalToUpdate++;
                    const { error: updateError } = await supabase
                        .from('projects')
                        .update(updateData)
                        .eq('id', project.id);

                    if (!updateError) {
                        updatedCount++;
                    }
                }
            }

            return {
                success: true,
                stats: {
                    total: projectsData?.length || 0,
                    needsUpdate: totalToUpdate,
                    updated: updatedCount
                }
            };
        } catch (err) {
            console.error('Error fixing projects data:', err);
            return { success: false, error: String(err) };
        }
    },

    fixAll: async ({ request }) => {
        const { data: { session } } = await supabase.auth.getSession();

        if (!session) {
            return { success: false, error: 'Not authenticated' };
        }

        try {
            // Fix education
            const educationResult = await actions.fixEducation({ request } as any);

            // Fix projects
            const projectsResult = await actions.fixProjects({ request } as any);

            return {
                success: true,
                education: educationResult,
                projects: projectsResult
            };
        } catch (err) {
            console.error('Error fixing all data:', err);
            return { success: false, error: String(err) };
        }
    }
};