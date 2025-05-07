import { json } from '@sveltejs/kit';
import { supabase } from '$lib/supabase';
import type { RequestHandler } from './$types';

export const GET: RequestHandler = async ({ request }) => {
    try {
        // Check if session exists
        const { data: { session } } = await supabase.auth.getSession();
        if (!session) {
            return json({ error: 'Not authenticated' }, { status: 401 });
        }

        // Get all education entries for the current user
        const { data: educationData, error: fetchError } = await supabase
            .from('education')
            .select('*')
            .eq('profile_id', session.user.id);

        if (fetchError) {
            return json({ error: 'Error fetching education data: ' + fetchError.message }, { status: 500 });
        }

        let updatedCount = 0;

        // Update each record to add a qualification field with the value from degree
        for (const edu of educationData || []) {
            // Check if the education record has a degree value and the qualification doesn't exist or is null
            if (edu.degree && (!edu.hasOwnProperty('qualification') || edu.qualification === null)) {
                const { error: updateError } = await supabase
                    .from('education')
                    .update({ qualification: edu.degree })
                    .eq('id', edu.id);

                if (!updateError) {
                    updatedCount++;
                } else {
                    console.error('Error updating education record:', updateError);
                }
            }
        }

        return json({
            success: true,
            message: `Updated ${updatedCount} of ${educationData?.length || 0} education records.`,
            updatedRecords: updatedCount
        });
    } catch (err) {
        console.error('Error during education update:', err);
        return json({ error: 'Update failed: ' + String(err) }, { status: 500 });
    }
}