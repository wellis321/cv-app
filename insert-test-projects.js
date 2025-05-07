// Script to insert test projects
import { createClient } from '@supabase/supabase-js';

// Set your Supabase URL and anon key here
const SUPABASE_URL = 'https://jnebkgmkgatejsjgbaqo.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImpuZWJrZ21rZ2F0ZWpzamdiYXFvIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDU4NTcwMjUsImV4cCI6MjA2MTQzMzAyNX0.sXXpX_p4Y3g4MQjbixgKun095dMl8yicYc3K4g6ieCM';

// User login is required for this script
const EMAIL = process.argv[2];
const PASSWORD = process.argv[3];

if (!EMAIL || !PASSWORD) {
    console.error('Usage: node insert-test-projects.js <email> <password>');
    process.exit(1);
}

// Create Supabase client
const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

// Sample project data
const sampleProjects = [
    {
        title: 'Personal Portfolio Website',
        name: 'Personal Portfolio Website',
        description: 'A responsive portfolio website to showcase my skills and projects',
        start_date: '2024-01-01',
        end_date: '2024-02-15'
    },
    {
        title: 'E-commerce Platform',
        name: 'E-commerce Platform',
        description: 'Built a full-stack e-commerce platform with payment processing and inventory management',
        start_date: '2023-06-01',
        end_date: '2023-11-30',
        url: 'https://example-ecommerce.com'
    }
];

async function run() {
    try {
        // Step 1: Login to get user ID
        console.log(`Logging in as ${EMAIL}...`);
        const { data: authData, error: authError } = await supabase.auth.signInWithPassword({
            email: EMAIL,
            password: PASSWORD
        });

        if (authError) {
            console.error('Login error:', authError.message);
            return;
        }

        if (!authData.session) {
            console.error('Login failed: No session returned');
            return;
        }

        const userId = authData.session.user.id;
        console.log('Login successful. User ID:', userId);

        // Step 2: Insert sample projects
        console.log('Inserting sample projects...');

        for (const project of sampleProjects) {
            const { data, error } = await supabase
                .from('projects')
                .insert({
                    ...project,
                    profile_id: userId
                })
                .select();

            if (error) {
                console.error('Error inserting project:', error.message);
            } else {
                console.log('Project inserted successfully:', data[0].title);
            }
        }

        // Step 3: Verify projects were inserted
        const { data: projects, error: fetchError } = await supabase
            .from('projects')
            .select('*')
            .eq('profile_id', userId);

        if (fetchError) {
            console.error('Error fetching inserted projects:', fetchError.message);
        } else {
            console.log(`Total projects for user: ${projects.length}`);
            if (projects.length > 0) {
                console.log('Projects:', projects.map(p => p.title).join(', '));
            }
        }
    } catch (err) {
        console.error('Unexpected error during project insertion:', err);
    }
}

run()
    .then(() => console.log('Sample project insertion completed'))
    .catch(err => console.error('Script failed:', err));