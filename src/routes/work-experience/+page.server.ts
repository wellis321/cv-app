import type { Actions, PageServerLoad } from './$types';
import { supabase } from '$lib/supabase';
import { fail, redirect } from '@sveltejs/kit';

export const load: PageServerLoad = async ({ locals }) => {
    const { data: { session } } = await supabase.auth.getSession();
    if (!session) {
        return { workExperiences: [] };
    }
    const { data, error } = await supabase
        .from('work_experience')
        .select('*')
        .eq('profile_id', session.user.id)
        .order('start_date', { ascending: false });
    if (error) {
        return { workExperiences: [], error: error.message };
    }
    return { workExperiences: data };
};

export const actions: Actions = {
    default: async ({ request, locals }) => {
        const { data: { session } } = await supabase.auth.getSession();
        if (!session) {
            return fail(401, { error: 'Not authenticated' });
        }
        const formData = await request.formData();
        const companyName = formData.get('companyName') as string;
        const position = formData.get('position') as string;
        const startDate = formData.get('startDate') as string;
        const endDate = formData.get('endDate') as string;
        const description = formData.get('description') as string;
        const { error } = await supabase.from('work_experience').insert({
            profile_id: session.user.id,
            company_name: companyName,
            position,
            start_date: startDate,
            end_date: endDate || null,
            description
        });
        if (error) {
            return fail(400, { error: error.message });
        }
        // Optionally, redirect to clear the form and reload the list
        throw redirect(303, '/work-experience');
    }
};