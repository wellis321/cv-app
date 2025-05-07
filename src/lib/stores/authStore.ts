import { writable } from 'svelte/store';
import { supabase } from '$lib/supabase';
import type { Session } from '@supabase/supabase-js';

// Create a writable store with initial value of null
export const session = writable<Session | null>(null);

// Initialize the store with the current session
export const initializeSession = async () => {
    try {
        const { data, error } = await supabase.auth.getSession();
        if (error) {
            console.error('Error getting session:', error);
        } else {
            console.log('Session initialized:', data.session ? 'Session present' : 'No session');
            session.set(data.session);
        }
    } catch (err) {
        console.error('Error initializing session:', err);
    }
};

// Subscribe to auth changes
export const setupAuthListener = () => {
    try {
        const { data: { subscription } } = supabase.auth.onAuthStateChange(
            (event, currentSession) => {
                console.log('Auth state changed in store:', event, 'Session:', currentSession ? 'Present' : 'None');
                session.set(currentSession);
            }
        );

        return () => {
            subscription.unsubscribe();
        };
    } catch (err) {
        console.error('Error setting up auth listener:', err);
        return () => { }; // Return a no-op function if there's an error
    }
};

// Directly login a user
export const login = async (email: string, password: string) => {
    try {
        const { data, error } = await supabase.auth.signInWithPassword({
            email,
            password
        });

        if (error) {
            console.error('Login error:', error);
            throw error;
        }

        console.log('Login successful, session:', data.session ? 'Present' : 'No session');
        session.set(data.session);
        return data;
    } catch (err) {
        console.error('Unexpected error during login:', err);
        throw err;
    }
};

// Sign up a new user
export const signup = async (email: string, password: string) => {
    try {
        const { data, error } = await supabase.auth.signUp({
            email,
            password,
            options: {
                emailRedirectTo: window.location.origin
            }
        });

        if (error) {
            console.error('Signup error:', error);
            throw error;
        }

        console.log('Signup successful, session:', data.session ? 'Present' : 'No session');
        return data;
    } catch (err) {
        console.error('Unexpected error during signup:', err);
        throw err;
    }
};

// Sign out
export const logout = async () => {
    try {
        const { error } = await supabase.auth.signOut();
        if (error) {
            console.error('Logout error:', error);
        } else {
            console.log('Logout successful');
            session.set(null);
        }
    } catch (err) {
        console.error('Unexpected error during logout:', err);
        // Still set session to null in case of error
        session.set(null);
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