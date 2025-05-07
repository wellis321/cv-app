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

        // Get all education entries for the user
        const { data: educationData, error: fetchError } = await supabase
            .from('education')
            .select('*')
            .eq('profile_id', userId);

        if (fetchError) {
            return json({ success: false, error: fetchError.message }, { status: 500 });
        }

        // Update education entries
        let updatedCount = 0;
        let errorCount = 0;
        let totalToFix = 0;

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
                totalToFix++;
                const { error: updateError } = await supabase
                    .from('education')
                    .update(updateData)
                    .eq('id', edu.id)
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
                total: educationData?.length || 0,
                totalToFix,
                updated: updatedCount,
                errors: errorCount
            }
        });
    } catch (err) {
        console.error('Error in education migration API:', err);
        return json({ success: false, error: String(err) }, { status: 500 });
    }
};