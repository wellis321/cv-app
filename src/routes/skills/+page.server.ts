import type { Actions, PageServerLoad } from './$types';
import { supabase } from '$lib/supabase';
import { fail, redirect } from '@sveltejs/kit';

export const load: PageServerLoad = async () => {
    const { data: { session } } = await supabase.auth.getSession();
    if (!session) return { skills: [] };
    const { data, error } = await supabase
        .from('skills')
        .select('*')
        .eq('profile_id', session.user.id)
        .order('name', { ascending: true });
    if (error) return { skills: [], error: error.message };
    return { skills: data };
};

export const actions: Actions = {
    default: async ({ request }) => {
        const { data: { session } } = await supabase.auth.getSession();
        if (!session) return fail(401, { error: 'Not authenticated' });
        const formData = await request.formData();
        const name = formData.get('name') as string;
        const level = formData.get('level') as string;
        const category = formData.get('category') as string;
        const { error } = await supabase.from('skills').insert({
            profile_id: session.user.id,
            name,
            level,
            category
        });
        if (error) return fail(400, { error: error.message });
        throw redirect(303, '/skills');
    }
};