import { json } from '@sveltejs/kit';
import type { RequestHandler } from './$types';
import { safeLog } from '$lib/config';

export const GET: RequestHandler = async ({ locals, request }) => {
    try {
        // Get the session from the request
        const {
            data: { session }
        } = await locals.supabase.auth.getSession();

        if (!session) {
            // More detailed logging for 401 error
            const authHeader = request.headers.get('Authorization');
            safeLog('warn', 'Session verification failed - No active session', {
                hasAuthHeader: !!authHeader,
                path: '/api/verify-session'
            });

            return json(
                {
                    valid: false,
                    message: 'No active session'
                },
                { status: 401 }
            );
        }

        // Add minimal user info and set a longer cache-control to reduce calls
        return json({
            valid: true,
            userId: session.user.id,
            email: session.user.email
        }, {
            headers: {
                'Cache-Control': 'private, max-age=30'  // Cache for 30 seconds
            }
        });
    } catch (error) {
        safeLog('error', 'Error in verify-session endpoint', { error });
        return json(
            {
                valid: false,
                message: 'Server error'
            },
            { status: 500 }
        );
    }
};
