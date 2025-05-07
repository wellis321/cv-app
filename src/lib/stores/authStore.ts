import { writable } from 'svelte/store';
import { supabase } from '$lib/supabase';
import type { Session } from '@supabase/supabase-js';
import { browser } from '$app/environment';

// Create a writable store with initial value of null
export const session = writable<Session | null>(null);
export const authLoading = writable<boolean>(true);
export const authError = writable<string | null>(null);

// Flag to track if we've initialized the session
let sessionInitialized = false;

// Initialize the store with the current session
export const initializeSession = async (forceRefresh = false) => {
    if (!browser) {
        return;
    }

    try {
        // Set loading state
        authLoading.set(true);
        authError.set(null);

        // Don't initialize twice unless forced
        if (sessionInitialized && !forceRefresh) {
            authLoading.set(false);
            return;
        }

        // Clear any existing session first to avoid stale data
        session.set(null);

        // Get the session from Supabase
        const { data, error } = await supabase.auth.getSession();

        if (error) {
            console.error('Error getting session:', error);
            authError.set(error.message);
            return;
        }

        if (data.session) {
            // Set the session in the store
            session.set(data.session);

            try {
                // Basic session verification with server
                const response = await fetch('/api/verify-session', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${data.session.access_token}`
                    },
                    credentials: 'include'
                });

                if (!response.ok) {
                    // Session couldn't be verified but don't force logout
                    // This could be a transient server error
                    console.warn('Server could not verify session, but continuing client-side');
                }
            } catch (err) {
                // Don't interrupt the flow for network errors - let client continue
                console.warn('Network error during session verification:', err);
            }
        }

        sessionInitialized = true;
    } catch (err) {
        console.error('Error initializing session:', err);
        authError.set('Failed to initialize session');
    } finally {
        // Clear loading state
        authLoading.set(false);
    }
};

// Subscribe to auth changes
export const setupAuthListener = () => {
    if (!browser) {
        return () => { };
    }

    try {
        const { data: { subscription } } = supabase.auth.onAuthStateChange(
            async (event, currentSession) => {
                console.log('Auth state changed:', event, currentSession ? 'Session present' : 'No session');
                authLoading.set(true);

                if (event === 'SIGNED_OUT') {
                    // Clear session when signed out
                    session.set(null);
                    sessionInitialized = false;
                    authError.set(null);
                } else if (event === 'SIGNED_IN' || event === 'TOKEN_REFRESHED') {
                    // Update the session
                    session.set(currentSession);
                }

                authLoading.set(false);
            }
        );

        return () => {
            subscription.unsubscribe();
        };
    } catch (err) {
        console.error('Error setting up auth listener:', err);
        authLoading.set(false);
        return () => { };
    }
};

// Directly login a user
export const login = async (email: string, password: string) => {
    try {
        authLoading.set(true);
        authError.set(null);

        const { data, error } = await supabase.auth.signInWithPassword({
            email,
            password
        });

        if (error) {
            console.error('Login error:', error);
            authError.set(error.message);
            throw error;
        }

        session.set(data.session);
        authError.set(null);
        return data;
    } catch (err) {
        console.error('Unexpected error during login:', err);
        throw err;
    } finally {
        authLoading.set(false);
    }
};

// Sign up a new user
export const signup = async (email: string, password: string) => {
    try {
        authLoading.set(true);
        authError.set(null);

        const { data, error } = await supabase.auth.signUp({
            email,
            password,
            options: {
                emailRedirectTo: browser ? window.location.origin : undefined
            }
        });

        if (error) {
            console.error('Signup error:', error);
            authError.set(error.message);
            throw error;
        }

        return data;
    } catch (err) {
        console.error('Unexpected error during signup:', err);
        throw err;
    } finally {
        authLoading.set(false);
    }
};

// Sign out
export const logout = async () => {
    try {
        authLoading.set(true);
        const { error } = await supabase.auth.signOut();
        if (error) {
            console.error('Logout error:', error);
            authError.set(error.message);
        } else {
            console.log('Logout successful');
            session.set(null);
            sessionInitialized = false;
            authError.set(null);
        }
    } catch (err) {
        console.error('Unexpected error during logout:', err);
        authError.set('Failed to logout');
        // Still set session to null in case of error
        session.set(null);
        sessionInitialized = false;
    } finally {
        authLoading.set(false);
    }
};

// Create a profile for a user
export const createProfile = async (userId: string, email: string) => {
    try {
        // Get current session to include the token
        const { data } = await supabase.auth.getSession();
        const accessToken = data.session?.access_token;

        const response = await fetch('/api/create-profile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                ...(accessToken ? { 'Authorization': `Bearer ${accessToken}` } : {})
            },
            body: JSON.stringify({ userId, email }),
            credentials: 'include'
        });

        return await response.json();
    } catch (err) {
        console.error('Error creating profile:', err);
        throw err;
    }
};

// Update a profile
export const updateProfile = async (profileData: any) => {
    try {
        // Get current session to include the token
        const { data } = await supabase.auth.getSession();
        const accessToken = data.session?.access_token;

        const response = await fetch('/api/update-profile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                ...(accessToken ? { 'Authorization': `Bearer ${accessToken}` } : {})
            },
            body: JSON.stringify(profileData),
            credentials: 'include'
        });

        return await response.json();
    } catch (err) {
        console.error('Error updating profile:', err);
        throw err;
    }
};