import type { Actions, PageServerLoad } from './$types';
import { supabase } from '$lib/supabase';
import { fail, redirect } from '@sveltejs/kit';

export const load: PageServerLoad = async () => {
    const { data: { session } } = await supabase.auth.getSession();
    if (!session) return { memberships: [] };
    const { data, error } = await supabase
        .from('professional_memberships')
        .select('*')
        .eq('profile_id', session.user.id)
        .order('start_date', { ascending: false });
    if (error) return { memberships: [], error: error.message };
    return { memberships: data };
};

export const actions: Actions = {
    default: async ({ request }) => {
        const { data: { session } } = await supabase.auth.getSession();
        if (!session) return fail(401, { error: 'Not authenticated' });
        const formData = await request.formData();
        const organisation = formData.get('organisation') as string;
        const role = formData.get('role') as string;
        const startDate = formData.get('startDate') as string;
        const endDate = formData.get('endDate') as string;
        const { error } = await supabase.from('professional_memberships').insert({
            profile_id: session.user.id,
            organisation,
            role,
            start_date: startDate,
            end_date: endDate || null
        });
        if (error) return fail(400, { error: error.message });
        throw redirect(303, '/memberships');
    }
};