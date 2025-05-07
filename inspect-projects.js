// Script to login and inspect projects
import { createClient } from '@supabase/supabase-js';
import { writeFileSync } from 'fs';

// Set your Supabase URL and anon key here
const SUPABASE_URL = 'https://jnebkgmkgatejsjgbaqo.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImpuZWJrZ21rZ2F0ZWpzamdiYXFvIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDU4NTcwMjUsImV4cCI6MjA2MTQzMzAyNX0.sXXpX_p4Y3g4MQjbixgKun095dMl8yicYc3K4g6ieCM';

const EMAIL = process.argv[2];
const PASSWORD = process.argv[3];

if (!EMAIL || !PASSWORD) {
    console.error('Usage: node inspect-projects.js <email> <password>');
    process.exit(1);
}

console.log('Starting inspection script...');
console.log('Email:', EMAIL);

// Create Supabase client
const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY, {
    auth: {
        autoRefreshToken: false,
        persistSession: false
    }
});

async function run() {
    try {
        // Step 1: Login to get session
        console.log('Step 1: Logging in...');
        const { data: { session }, error: loginError } = await supabase.auth.signInWithPassword({
            email: EMAIL,
            password: PASSWORD
        });

        if (loginError) {
            console.error('Login error:', loginError.message);
            return;
        }

        if (!session) {
            console.error('Login failed: No session returned');
            return;
        }

        console.log('Login successful. User ID:', session.user.id);

        // Step 2: Find all projects for this user
        console.log('Step 2: Fetching projects...');
        const { data: projectsData, error: fetchError } = await supabase
            .from('projects')
            .select('*')
            .eq('profile_id', session.user.id);

        if (fetchError) {
            console.error('Error fetching projects data:', fetchError.message);
            return;
        }

        console.log(`Found ${projectsData?.length || 0} projects`);

        if (projectsData && projectsData.length > 0) {
            console.log('Sample project:', JSON.stringify(projectsData[0], null, 2));

            // Write the projects data to a file for inspection
            writeFileSync('projects-data.json', JSON.stringify(projectsData, null, 2));
            console.log('Wrote projects data to projects-data.json');
        } else {
            console.log('No projects found.');

            // Step 3: Show all tables in the database
            console.log('Checking for other projects in the database...');
            const { data: rawData, error: rawError } = await supabase
                .from('projects')
                .select('*')
                .limit(10);

            if (rawError) {
                console.error('Error fetching raw projects data:', rawError.message);
            } else {
                console.log(`Found ${rawData?.length || 0} projects in the database`);
                if (rawData && rawData.length > 0) {
                    console.log('Sample raw project:', JSON.stringify(rawData[0], null, 2));
                    writeFileSync('raw-projects-data.json', JSON.stringify(rawData, null, 2));
                    console.log('Wrote raw projects data to raw-projects-data.json');
                }
            }
        }
    } catch (err) {
        console.error('Unexpected error during inspection:', err);
    }
}

run()
    .then(() => console.log('Inspection script completed'))
    .catch(err => console.error('Script failed:', err));