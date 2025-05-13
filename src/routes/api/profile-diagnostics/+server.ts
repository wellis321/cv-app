import { json } from '@sveltejs/kit';
import type { RequestHandler } from './$types';
import { safeLog } from '$lib/config';

export const GET: RequestHandler = async ({ locals }) => {
    const requestId = crypto.randomUUID();

    try {
        // Test if we can select the photo_url column
        const { data, error } = await locals.supabase
            .from('profiles')
            .select('id, photo_url')
            .limit(1);

        if (error) {
            safeLog('error', `[${requestId}] Profile column test failed:`, {
                errorCode: error.code,
                errorMessage: error.message
            });

            return json({
                success: false,
                error: 'Column test failed',
                details: error.message,
                requestId
            });
        }

        // Format the result to show column names
        const columns = data && data.length > 0
            ? Object.keys(data[0]).join(', ')
            : 'No data returned';

        return json({
            success: true,
            message: 'Column test successful',
            requestId,
            columns,
            hasPhotoUrl: columns.includes('photo_url')
        });
    } catch (error) {
        safeLog('error', `[${requestId}] Unexpected error in profile diagnostics:`, {
            error: error instanceof Error ? error.message : error
        });

        return json({
            success: false,
            error: 'Test failed',
            message: error instanceof Error ? error.message : 'Unknown error',
            requestId
        });
    }
};