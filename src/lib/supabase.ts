import { createClient } from '@supabase/supabase-js';
import type { Database } from './database.types';
import config, { safeLog } from './config';

// Create the Supabase client with configuration from config file
export const supabase = createClient<Database>(
    config.supabase.url,
    config.supabase.anonKey,
    {
        auth: {
            persistSession: true,
            autoRefreshToken: true,
            detectSessionInUrl: true,
            storageKey: 'sb-auth-token',
            flowType: 'pkce'
        }
    }
);

// Initialize and validate the Supabase client on startup
try {
    safeLog('debug', 'Supabase client initialized', {
        environment: config.environment,
        // Do NOT log credentials here
    });
} catch (error) {
    safeLog('error', 'Failed to initialize Supabase client', error);
    // Re-throw critical initialization errors to prevent app from starting with invalid config
    throw error;
}