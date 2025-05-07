import type { Actions, PageServerLoad } from './$types';
import { supabase } from '$lib/supabase';
import { fail, redirect } from '@sveltejs/kit';

export const load: PageServerLoad = async () => {
    const { data: { session } } = await supabase.auth.getSession();
    if (!session) return { interests: [] };
    const { data, error } = await supabase
        .from('interests')
        .select('*')
        .eq('profile_id', session.user.id)
        .order('name', { ascending: true });
    if (error) return { interests: [], error: error.message };
    return { interests: data };
};

export const actions: Actions = {
    default: async ({ request }) => {
        const { data: { session } } = await supabase.auth.getSession();
        if (!session) return fail(401, { error: 'Not authenticated' });
        const formData = await request.formData();
        const name = formData.get('name') as string;
        const description = formData.get('description') as string;
        const { error } = await supabase.from('interests').insert({
            profile_id: session.user.id,
            name,
            description
        });
        if (error) return fail(400, { error: error.message });
        throw redirect(303, '/interests');
    }
};