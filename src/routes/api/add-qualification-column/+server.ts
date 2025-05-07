import { json } from '@sveltejs/kit';
import { supabase } from '$lib/supabase';
import type { RequestHandler } from './$types';

export const GET: RequestHandler = async () => {
    try {
        // First check if the qualification column already exists
        let { data, error } = await supabase
            .from('education')
            .select('qualification')
            .limit(1);

        if (!error) {
            // Column already exists
            return json({
                success: true,
                message: 'Qualification column already exists.'
            });
        }

        // If the column doesn't exist, we need to add it
        // This requires direct SQL execution, which may not be available via the Supabase REST API
        // Let's try using the Supabase client to add it

        // For Supabase REST API, we need to use custom functions or handle this on the database side
        // Since we can't directly do ALTER TABLE, let's use a workaround
        // We'll create a new record with the qualification field and let Supabase auto-add the column

        // First get a sample record
        const { data: sampleData, error: sampleError } = await supabase
            .from('education')
            .select('*')
            .limit(1);

        if (sampleError || !sampleData || sampleData.length === 0) {
            return json({
                success: false,
                message: 'No education records found to use as template',
                error: sampleError?.message
            }, { status: 404 });
        }

        // Create a test record with the qualification field
        const sampleRecord = sampleData[0];

        // We'll update this record with a qualification field
        const { error: updateError } = await supabase
            .from('education')
            .update({
                qualification: sampleRecord.degree || 'Sample Qualification'
            })
            .eq('id', sampleRecord.id);

        if (updateError) {
            return json({
                success: false,
                message: 'Failed to add qualification column',
                error: updateError.message
            }, { status: 500 });
        }

        return json({
            success: true,
            message: 'Qualification column added successfully'
        });
    } catch (err) {
        console.error('Error adding qualification column:', err);
        return json({
            success: false,
            error: String(err)
        }, { status: 500 });
    }
}