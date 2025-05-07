import type { Actions, PageServerLoad } from './$types';
import { supabase } from '$lib/supabase';
import { fail, redirect } from '@sveltejs/kit';

export const load: PageServerLoad = async () => {
    const { data: { session } } = await supabase.auth.getSession();
    if (!session) return { projects: [] };
    const { data, error } = await supabase
        .from('projects')
        .select('*')
        .eq('profile_id', session.user.id)
        .order('start_date', { ascending: false });
    if (error) return { projects: [], error: error.message };
    return { projects: data };
};

export const actions: Actions = {
    default: async ({ request }) => {
        const { data: { session } } = await supabase.auth.getSession();
        if (!session) return fail(401, { error: 'Not authenticated' });
        const formData = await request.formData();
        const title = formData.get('title') as string;
        const description = formData.get('description') as string;
        const startDate = formData.get('startDate') as string;
        const endDate = formData.get('endDate') as string;
        const url = formData.get('url') as string;
        const { error } = await supabase.from('projects').insert({
            profile_id: session.user.id,
            title,
            description,
            start_date: startDate || null,
            end_date: endDate || null,
            url
        });
        if (error) return fail(400, { error: error.message });
        throw redirect(303, '/projects');
    }
};