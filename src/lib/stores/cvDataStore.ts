import { writable, derived, get } from 'svelte/store';
import { supabase } from '$lib/supabase';
import { browser } from '$app/environment';
import { session } from './authStore';
import type { Database } from '$lib/database.types';
import { createClient } from '@supabase/supabase-js';
import config from '$lib/config';
import { sanitizeInput } from '$lib/validation';

// Types for CV data
export interface CvData {
    userId: string | null;
    profile: any | null;
    workExperiences: any[];
    projects: any[];
    skills: any[];
    education: any[];
    certifications: any[];
    memberships: any[];
    interests: any[];
    qualificationEquivalence: any[];
    username: string | null;
}

// Default empty state
const defaultCvData: CvData = {
    userId: null,
    profile: null,
    workExperiences: [],
    projects: [],
    skills: [],
    education: [],
    certifications: [],
    memberships: [],
    interests: [],
    qualificationEquivalence: [],
    username: null
};

// Create the store
const createCvStore = () => {
    const { subscribe, set, update } = writable<CvData>({ ...defaultCvData });

    // Track loading state
    const loadingState = writable({
        loading: false,
        error: null as string | null
    });

    // Store cache by userId to avoid refetching
    let cache: Record<string, CvData> = {};

    // Add profile color validation as a utility function inside the store creator
    function validateHeaderColors(profile: any) {
        const isValidHexColor = (color: string): boolean => {
            return /^#[0-9A-F]{6}$/i.test(color);
        };

        // Set default colors if invalid or missing
        if (!profile.cv_header_from_color || !isValidHexColor(profile.cv_header_from_color)) {
            profile.cv_header_from_color = '#4338ca'; // Default indigo-700
        }

        if (!profile.cv_header_to_color || !isValidHexColor(profile.cv_header_to_color)) {
            profile.cv_header_to_color = '#7e22ce'; // Default purple-700
        }
    }

    // Helper function to load work experiences with responsibilities
    async function loadWorkExperiences(userId: string, clientInstance?: any) {
        const client = clientInstance || supabase;

        try {
            const { data: workExperiences, error: workError } = await client
                .from('work_experience')
                .select('*')
                .eq('profile_id', userId)
                .order('start_date', { ascending: false });

            if (workError || !workExperiences?.length) {
                return { data: [], error: workError };
            }

            // Load responsibilities for each work experience
            const workWithResponsibilities = await Promise.all(
                workExperiences.map(async (work: any) => {
                    try {
                        // Query the responsibility categories for this work experience
                        const { data: categories, error: categoriesError } = await client
                            .from('responsibility_categories')
                            .select('*')
                            .eq('work_experience_id', work.id)
                            .order('sort_order', { ascending: true });

                        if (categoriesError || !categories || categories.length === 0) {
                            return { ...work, responsibilities: [] };
                        }

                        // Get all items for these categories
                        const categoryIds = categories.map((cat: any) => cat.id);
                        const { data: items, error: itemsError } = await client
                            .from('responsibility_items')
                            .select('*')
                            .in('category_id', categoryIds)
                            .order('sort_order', { ascending: true });

                        if (itemsError) {
                            return {
                                ...work,
                                responsibilities: categories.map((cat: any) => ({ ...cat, items: [] }))
                            };
                        }

                        // Group items by category
                        const responsibilities = categories.map((category: any) => ({
                            ...category,
                            items: items?.filter((item: any) => item.category_id === category.id) || []
                        }));

                        return { ...work, responsibilities };
                    } catch (err) {
                        console.error(`Error loading responsibilities for work experience ${work.id}:`, err);
                        return { ...work, responsibilities: [] };
                    }
                })
            );

            return { data: workWithResponsibilities, error: null };
        } catch (err) {
            console.error("Error loading work experiences:", err);
            return { data: [], error: err };
        }
    }

    // Load CV data by username function - properly integrated with the store
    async function loadByUsername(username: string) {
        if (!username) {
            console.error('No username provided to loadByUsername');
            loadingState.update(state => ({
                ...state,
                loading: false,
                error: 'No username provided'
            }));
            return;
        }

        console.log(`Loading CV data for username: ${username}`);

        // Set loading state
        loadingState.update(state => ({
            ...state,
            loading: true,
            error: null
        }));

        try {
            // Use supabaseAdmin to avoid RLS issues for public profiles
            const client = supabase; // Use the regular client since we have public policies

            // Check if the client is properly initialized
            if (!client) {
                console.error('Supabase client not initialized in loadByUsername');
                loadingState.update(state => ({
                    ...state,
                    loading: false,
                    error: 'Database connection error'
                }));
                return;
            }

            // Load profile first to get user ID
            console.log(`Fetching profile data for username: ${username}`);
            const { data: profileData, error: profileError } = await client
                .from('profiles')
                .select('*')
                .eq('username', username)
                .single();

            // Handle errors with proper logging
            if (profileError) {
                if (profileError.code === 'PGRST116') {
                    // This is the "no rows returned" error from PostgREST
                    console.error(`No profile found for username: ${username}`);
                    loadingState.update(state => ({
                        ...state,
                        loading: false,
                        error: 'No CV found with this username'
                    }));
                } else {
                    console.error('Error loading profile for CV data:', profileError);
                    loadingState.update(state => ({
                        ...state,
                        loading: false,
                        error: `Could not load profile data: ${profileError.message}`
                    }));
                }
                return;
            }

            // Check if profile exists
            if (!profileData) {
                console.error(`Profile data is null or undefined for username: ${username}`);
                loadingState.update(state => ({
                    ...state,
                    loading: false,
                    error: 'Could not find a CV for this username'
                }));
                return;
            }

            console.log(`Found profile for ${username}:`, profileData);

            // Validate profile header colors
            validateHeaderColors(profileData);

            // Now load all other CV sections
            const userId = profileData.id;
            console.log(`Loading CV sections for user ID: ${userId}`);

            try {
                const [
                    workExperiencesResult,
                    { data: education, error: eduError },
                    { data: skills, error: skillsError },
                    { data: projects, error: projectsError },
                    { data: certifications, error: certError },
                    { data: professionalMemberships, error: membershipError },
                    { data: interests, error: interestError },
                    { data: qualificationEquivalence, error: qualError }
                ] = await Promise.all([
                    // Work experiences with responsibilities
                    loadWorkExperiences(userId, client),

                    // Education
                    client
                        .from('education')
                        .select('*')
                        .eq('profile_id', userId)
                        .order('start_date', { ascending: false }),

                    // Skills
                    client
                        .from('skills')
                        .select('*')
                        .eq('profile_id', userId)
                        .order('category', { ascending: true }),

                    // Projects
                    client
                        .from('projects')
                        .select('*')
                        .eq('profile_id', userId)
                        .order('start_date', { ascending: false }),

                    // Certifications
                    client
                        .from('certifications')
                        .select('*')
                        .eq('profile_id', userId)
                        .order('date_obtained', { ascending: false }),

                    // Professional memberships
                    client
                        .from('professional_memberships')
                        .select('*')
                        .eq('profile_id', userId)
                        .order('start_date', { ascending: false }),

                    // Interests
                    client
                        .from('interests')
                        .select('*')
                        .eq('profile_id', userId)
                        .order('name', { ascending: true }),

                    // Qualification equivalence
                    client
                        .from('professional_qualification_equivalence')
                        .select('*')
                        .eq('profile_id', userId)
                        .order('country', { ascending: true })
                ]);

                // Check for any loading errors
                const errors = [];
                if (workExperiencesResult.error) errors.push(`Work experience: ${workExperiencesResult.error}`);
                if (eduError) errors.push(`Education: ${eduError.message}`);
                if (skillsError) errors.push(`Skills: ${skillsError.message}`);
                if (projectsError) errors.push(`Projects: ${projectsError.message}`);
                if (certError) errors.push(`Certifications: ${certError.message}`);
                if (membershipError) errors.push(`Memberships: ${membershipError.message}`);
                if (interestError) errors.push(`Interests: ${interestError.message}`);
                if (qualError) errors.push(`Qualification equivalence: ${qualError.message}`);

                if (errors.length > 0) {
                    console.error('Error loading CV data:', errors);
                    // Show an error but continue with any data we have
                    loadingState.update(state => ({
                        ...state,
                        error: `Some data could not be loaded (${errors.length} errors)`
                    }));
                }

                // Update the store with all the data
                set({
                    userId,
                    profile: profileData,
                    workExperiences: workExperiencesResult.data || [],
                    education: education || [],
                    skills: skills || [],
                    projects: projects || [],
                    certifications: certifications || [],
                    memberships: professionalMemberships || [],
                    interests: interests || [],
                    qualificationEquivalence: qualificationEquivalence || [],
                    username
                });

                loadingState.update(state => ({
                    ...state,
                    loading: false
                }));
            } catch (sectionError) {
                console.error('Error loading CV sections:', sectionError);

                // Still update with profile data at minimum
                set({
                    userId,
                    profile: profileData,
                    workExperiences: [],
                    education: [],
                    skills: [],
                    projects: [],
                    certifications: [],
                    memberships: [],
                    interests: [],
                    qualificationEquivalence: [],
                    username
                });

                loadingState.update(state => ({
                    ...state,
                    loading: false,
                    error: 'Some CV sections could not be loaded'
                }));
            }
        } catch (error) {
            console.error('Unexpected error loading CV data:', error);
            loadingState.update(state => ({
                ...state,
                loading: false,
                error: 'An unexpected error occurred while loading the CV'
            }));
        }
    }

    return {
        subscribe,
        loading: {
            subscribe: loadingState.subscribe
        },

        // Reset the store to initial empty state
        reset: () => {
            set({ ...defaultCvData });
        },

        // Set cached data if available
        setCachedData: (userId: string) => {
            if (cache[userId]) {
                set(cache[userId]);
                return true;
            }
            return false;
        },

        // Load CV data for the current user
        loadCurrentUserData: async () => {
            if (!browser) return;

            const currentSession = get(session);
            if (!currentSession || !currentSession.user) {
                loadingState.update((s) => ({ ...s, error: 'User not logged in' }));
                return;
            }

            const userId = currentSession.user.id;

            // Return cached data if available
            if (cache[userId]) {
                set(cache[userId]);
                return;
            }

            try {
                loadingState.update((s) => ({ ...s, loading: true, error: null }));

                // First get the profile to check username
                const { data: profile, error: profileError } = await supabase
                    .from('profiles')
                    .select('*')
                    .eq('id', userId)
                    .single();

                if (profileError) {
                    console.error('Error loading profile:', profileError);
                    loadingState.update((s) => ({ ...s, error: 'Failed to load profile data' }));
                    return;
                }

                // Load all CV sections
                const data = await loadUserCvData(userId);

                if (data.profile) {
                    data.username = data.profile.username || null;
                }

                // Cache the data
                cache[userId] = data;

                // Update the store
                set(data);

                loadingState.update((s) => ({ ...s, loading: false }));
                return data;
            } catch (err: any) {
                console.error('Error loading CV data:', err);
                loadingState.update((s) => ({
                    ...s,
                    loading: false,
                    error: err.message || 'Failed to load CV data'
                }));
            }
        },

        // Load CV data by username (for public view)
        loadByUsername,

        // Clear the cache for a specific user
        clearCache: (userId: string | null = null) => {
            if (userId) {
                delete cache[userId];
            } else {
                cache = {};
            }
        }
    };
};

// Helper function to sanitize text fields in arrays
function sanitizeArray(arr: any[], textFields: string[]): any[] {
    if (!Array.isArray(arr)) return arr;

    return arr.map((item) => {
        const sanitizedItem = { ...item };
        for (const field of textFields) {
            if (typeof sanitizedItem[field] === 'string') {
                sanitizedItem[field] = sanitizeInput(sanitizedItem[field]);
            }
        }
        return sanitizedItem;
    });
}

// Sanitize CV data to prevent XSS
function sanitizeCvData(cvData: CvData): CvData {
    if (!cvData) return cvData;

    // Create a deep copy to avoid mutating original data
    const sanitized = { ...cvData };

    // Sanitize profile data
    if (sanitized.profile) {
        if (typeof sanitized.profile.full_name === 'string') {
            sanitized.profile.full_name = sanitizeInput(sanitized.profile.full_name);
        }
        if (typeof sanitized.profile.location === 'string') {
            sanitized.profile.location = sanitizeInput(sanitized.profile.location);
        }
    }

    // Sanitize each section
    if (Array.isArray(sanitized.workExperiences)) {
        sanitized.workExperiences = sanitizeArray(sanitized.workExperiences, [
            'company_name',
            'position',
            'description'
        ]);
    }

    if (Array.isArray(sanitized.projects)) {
        sanitized.projects = sanitizeArray(sanitized.projects, ['title', 'description', 'url']);
    }

    if (Array.isArray(sanitized.education)) {
        sanitized.education = sanitizeArray(sanitized.education, [
            'institution',
            'degree',
            'field_of_study',
            'description'
        ]);
    }

    if (Array.isArray(sanitized.skills)) {
        sanitized.skills = sanitizeArray(sanitized.skills, ['name', 'category']);
    }

    if (Array.isArray(sanitized.certifications)) {
        sanitized.certifications = sanitizeArray(sanitized.certifications, [
            'name',
            'issuer',
            'description'
        ]);
    }

    if (Array.isArray(sanitized.memberships)) {
        sanitized.memberships = sanitizeArray(sanitized.memberships, [
            'organisation',
            'role',
            'description'
        ]);
    }

    if (Array.isArray(sanitized.interests)) {
        sanitized.interests = sanitizeArray(sanitized.interests, ['name', 'description']);
    }

    return sanitized;
}

// Modify the loadUserCvData function to apply sanitization
async function loadUserCvData(userId: string, clientInstance?: any): Promise<CvData> {
    // Use provided client instance or default to supabase
    const client = clientInstance || supabase;

    // Try to refresh token if using the main client (not for public viewing)
    if (!clientInstance && browser) {
        try {
            const { error: refreshError } = await supabase.auth.refreshSession();
            if (refreshError) {
                console.warn('Could not refresh session before loading CV data:', refreshError.message);
            }
        } catch (refreshErr) {
            console.warn('Error during session refresh before loading CV data:', refreshErr);
        }
    }

    // Start with empty data
    const data: CvData = { ...defaultCvData, userId };

    try {
        // Load all sections in parallel for efficiency
        const [
            profileResult,
            workExperiencesResult,
            projectsResult,
            skillsResult,
            educationResult,
            certificationsResult,
            membershipResult,
            interestsResult,
            qualificationEquivalenceResult
        ] = await Promise.all([
            client.from('profiles').select('*').eq('id', userId).single(),
            client.from('work_experience').select('*').eq('profile_id', userId).order('start_date', { ascending: false }),
            client.from('projects').select('*').eq('profile_id', userId).order('start_date', { ascending: false }),
            client.from('skills').select('*').eq('profile_id', userId),
            client.from('education').select('*').eq('profile_id', userId).order('start_date', { ascending: false }),
            client.from('certifications').select('*').eq('profile_id', userId).order('date_obtained', { ascending: false }),
            client.from('professional_memberships').select('*').eq('profile_id', userId).order('start_date', { ascending: false }),
            client.from('interests').select('*').eq('profile_id', userId),
            client
                .from('professional_qualification_equivalence')
                .select('*')
                .eq('profile_id', userId)
        ]);

        // Check profile result - this is required
        if (profileResult.error) {
            console.error('Error loading profile:', profileResult.error);
            throw new Error('Failed to load profile data');
        }

        // Populate data with results, handle any errors gracefully
        data.profile = profileResult.data;
        data.workExperiences = workExperiencesResult.error ? [] : workExperiencesResult.data || [];
        data.projects = projectsResult.error ? [] : projectsResult.data || [];
        data.skills = skillsResult.error ? [] : skillsResult.data || [];
        data.education = educationResult.error ? [] : educationResult.data || [];
        data.certifications = certificationsResult.error ? [] : certificationsResult.data || [];
        data.memberships = membershipResult.error ? [] : membershipResult.data || [];
        data.interests = interestsResult.error ? [] : interestsResult.data || [];
        data.qualificationEquivalence = qualificationEquivalenceResult.error ? [] : qualificationEquivalenceResult.data || [];

        // Load responsibilities for each work experience entry
        if (data.workExperiences.length > 0) {
            try {
                // Fetch responsibilities for each work experience
                const workResponsibilities = await Promise.all(
                    data.workExperiences.map(async (work) => {
                        try {
                            // Query the responsibility categories for this work experience
                            const { data: categories, error: categoriesError } = await client
                                .from('responsibility_categories')
                                .select('*')
                                .eq('work_experience_id', work.id)
                                .order('sort_order', { ascending: true });

                            if (categoriesError || !categories || categories.length === 0) {
                                return { ...work, responsibilities: [] };
                            }

                            // Get all items for these categories
                            const categoryIds = categories.map((cat: any) => cat.id);
                            const { data: items, error: itemsError } = await client
                                .from('responsibility_items')
                                .select('*')
                                .in('category_id', categoryIds)
                                .order('sort_order', { ascending: true });

                            if (itemsError) {
                                return {
                                    ...work,
                                    responsibilities: categories.map((cat: any) => ({ ...cat, items: [] }))
                                };
                            }

                            // Group items by category
                            const responsibilities = categories.map((category: any) => ({
                                ...category,
                                items: items?.filter((item: any) => item.category_id === category.id) || []
                            }));

                            return { ...work, responsibilities };
                        } catch (err) {
                            console.error(`Error loading responsibilities for work experience ${work.id}:`, err);
                            return { ...work, responsibilities: [] };
                        }
                    })
                );

                // Replace the work experiences with the ones including responsibilities
                data.workExperiences = workResponsibilities;
            } catch (respErr) {
                console.error('Error loading work responsibilities:', respErr);
                // Continue with the work experiences we have, without responsibilities
            }
        }

        // Log any errors that occurred
        [
            { name: 'work experiences', result: workExperiencesResult },
            { name: 'projects', result: projectsResult },
            { name: 'skills', result: skillsResult },
            { name: 'education', result: educationResult },
            { name: 'certifications', result: certificationsResult },
            { name: 'memberships', result: membershipResult },
            { name: 'interests', result: interestsResult },
            { name: 'qualification equivalence', result: qualificationEquivalenceResult }
        ].forEach(item => {
            if (item.result.error) {
                console.error(`Error loading ${item.name}:`, item.result.error);
            }
        });

        // Apply sanitization before returning the data
        return sanitizeCvData(data);
    } catch (err) {
        console.error('Error loading CV data:', err);
        throw err;
    }
}

// Create and export the store instance
export const cvStore = createCvStore();

// Derived stores for convenience
export const profile = derived(cvStore, ($cvStore) => $cvStore.profile);
export const workExperiences = derived(cvStore, ($cvStore) => $cvStore.workExperiences);
export const skills = derived(cvStore, ($cvStore) => $cvStore.skills);
export const education = derived(cvStore, ($cvStore) => $cvStore.education);
export const projects = derived(cvStore, ($cvStore) => $cvStore.projects);
export const certifications = derived(cvStore, ($cvStore) => $cvStore.certifications);
export const memberships = derived(cvStore, ($cvStore) => $cvStore.memberships);
export const interests = derived(cvStore, ($cvStore) => $cvStore.interests);
export const qualificationEquivalence = derived(
    cvStore,
    ($cvStore) => $cvStore.qualificationEquivalence
);
