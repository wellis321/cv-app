import { json } from '@sveltejs/kit';
import { createClient } from '@supabase/supabase-js';
import type { RequestHandler } from './$types';
import config, { safeLog } from '$lib/config';

export const GET: RequestHandler = async ({ locals, url }) => {
    const requestId = crypto.randomUUID();
    const testType = url.searchParams.get('type') || 'connection';

    try {
        // Create a temporary admin client just for diagnostics
        const adminClient = createClient(
            config.supabase.url,
            config.supabase.anonKey
        );

        safeLog('debug', `[${requestId}] Running ${testType} test`);

        // Test basic connection
        if (testType === 'connection') {
            const { data, error } = await locals.supabase.from('profiles').select('count').limit(1);

            if (error) {
                return json({
                    success: false,
                    error: 'Connection test failed',
                    details: error.message,
                    code: error.code
                });
            }

            return json({
                success: true,
                message: 'Connection test successful',
                data
            });
        }

        // Test table access
        if (testType === 'table') {
            // Check if the profiles table exists
            const { data, error } = await adminClient.rpc('get_table_exists', {
                table_name: 'profiles'
            });

            if (error) {
                return json({
                    success: false,
                    error: 'Table check failed',
                    details: error.message
                });
            }

            return json({
                success: true,
                message: 'Table exists check successful',
                exists: data
            });
        }

        // Test schema
        if (testType === 'schema') {
            // Try to get column information for profiles table
            const { data, error } = await adminClient.from('_information_schema.columns')
                .select('column_name, data_type')
                .eq('table_name', 'profiles');

            if (error) {
                return json({
                    success: false,
                    error: 'Schema check failed',
                    details: error.message
                });
            }

            return json({
                success: true,
                message: 'Schema check successful',
                columns: data
            });
        }

        return json({
            success: false,
            error: 'Unknown test type',
            validTypes: ['connection', 'table', 'schema']
        });
    } catch (error) {
        safeLog('error', `[${requestId}] Test endpoint error:`, {
            error: error instanceof Error ? error.message : error
        });

        return json({
            success: false,
            error: 'Test failed',
            message: error instanceof Error ? error.message : 'Unknown error'
        });
    }
};