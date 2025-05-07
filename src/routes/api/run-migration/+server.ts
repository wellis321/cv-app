import { json } from '@sveltejs/kit';
import { supabase } from '$lib/supabase';
import type { RequestHandler } from './$types';

export const GET: RequestHandler = async () => {
    try {
        // First check if the column exists
        const { data: degreeData, error: degreeError } = await supabase
            .from('education')
            .select('degree')
            .limit(1);

        if (degreeError && degreeError.code !== 'PGRST116') {
            // If error is not "Column degree does not exist" then something else went wrong
            return json({
                error: 'Error checking degree column: ' + degreeError.message,
                code: degreeError.code
            }, { status: 500 });
        }

        if (!degreeError) {
            // Column exists, so we need to rename it
            // Using raw SQL through RPC
            const { error: renameError } = await supabase.rpc('run_sql', {
                sql: 'ALTER TABLE education RENAME COLUMN degree TO qualification;'
            });

            if (renameError) {
                return json({
                    error: 'Error renaming column: ' + renameError.message,
                    code: renameError.code
                }, { status: 500 });
            }

            return json({
                success: true,
                message: 'Migration completed successfully - column renamed from degree to qualification'
            });
        } else {
            // Check if qualification column exists
            const { data: qualificationData, error: qualificationError } = await supabase
                .from('education')
                .select('qualification')
                .limit(1);

            if (!qualificationError) {
                return json({
                    success: true,
                    message: 'Migration not needed - qualification column already exists'
                });
            }

            return json({
                error: 'Both degree and qualification columns do not exist',
                degreeError,
                qualificationError
            }, { status: 500 });
        }
    } catch (err) {
        console.error('Error during migration:', err);
        return json({ error: 'Migration failed: ' + String(err) }, { status: 500 });
    }
}