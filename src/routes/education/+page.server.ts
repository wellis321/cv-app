import type { Actions, PageServerLoad } from './$types';
import { supabase } from '$lib/supabase';
import { fail, redirect } from '@sveltejs/kit';

export const load: PageServerLoad = async () => {
    const { data: { session } } = await supabase.auth.getSession();
    if (!session) return { educationList: [] };
    const { data, error } = await supabase
        .from('education')
        .select('*')
        .eq('profile_id', session.user.id)
        .order('start_date', { ascending: false });
    if (error) return { educationList: [], error: error.message };
    return { educationList: data };
};

export const actions: Actions = {
    default: async ({ request }) => {
        const { data: { session } } = await supabase.auth.getSession();
        if (!session) return fail(401, { error: 'Not authenticated' });
        const formData = await request.formData();
        const institution = formData.get('institution') as string;
        const degree = formData.get('degree') as string;
        const fieldOfStudy = formData.get('fieldOfStudy') as string;
        const startDate = formData.get('startDate') as string;
        const endDate = formData.get('endDate') as string;
        const { error } = await supabase.from('education').insert({
            profile_id: session.user.id,
            institution,
            degree,
            field_of_study: fieldOfStudy,
            start_date: startDate,
            end_date: endDate || null
        });
        if (error) return fail(400, { error: error.message });
        throw redirect(303, '/education');
    }
};