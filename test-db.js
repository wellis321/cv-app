// Simple script to test database connection and query projects
import { createClient } from '@supabase/supabase-js';

// Set your Supabase URL and anon key here - same as in your app
const SUPABASE_URL = 'https://jnebkgmkgatejsjgbaqo.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImpuZWJrZ21rZ2F0ZWpzamdiYXFvIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDU4NTcwMjUsImV4cCI6MjA2MTQzMzAyNX0.sXXpX_p4Y3g4MQjbixgKun095dMl8yicYc3K4g6ieCM';

// Optional: User credentials for login testing
const EMAIL = process.argv[2]; // First command line argument
const PASSWORD = process.argv[3]; // Second command line argument

// Create Supabase client
const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

async function run() {
    console.log('Testing database connection...');

    try {
        // Step 1: Check all projects in the database (no auth required)
        console.log('\n--- Checking all projects ---');
        const { data: allProjects, error: allProjectsError } = await supabase
            .from('projects')
            .select('*')
            .limit(10);

        if (allProjectsError) {
            console.error('Error querying projects:', allProjectsError);
        } else {
            console.log(`Found ${allProjects?.length || 0} projects in total`);
            if (allProjects && allProjects.length > 0) {
                console.log('First project:', allProjects[0]);

                // Check for name/title field issues
                let missingTitleCount = 0;
                let missingNameCount = 0;

                for (const project of allProjects) {
                    if (!project.title && project.name) missingTitleCount++;
                    if (!project.name && project.title) missingNameCount++;
                }

                console.log(`Projects missing title field: ${missingTitleCount}`);
                console.log(`Projects missing name field: ${missingNameCount}`);
            }
        }

        // Step 2: Try to log in if credentials were provided
        if (EMAIL && PASSWORD) {
            console.log(`\n--- Logging in as ${EMAIL} ---`);
            const { data: authData, error: authError } = await supabase.auth.signInWithPassword({
                email: EMAIL,
                password: PASSWORD
            });

            if (authError) {
                console.error('Login error:', authError);
            } else if (authData.session) {
                console.log('Login successful');
                console.log('User ID:', authData.session.user.id);

                // Try to get user's projects
                console.log('\n--- Checking user projects ---');
                const { data: userProjects, error: userProjectsError } = await supabase
                    .from('projects')
                    .select('*')
                    .eq('profile_id', authData.session.user.id);

                if (userProjectsError) {
                    console.error('Error querying user projects:', userProjectsError);
                } else {
                    console.log(`Found ${userProjects?.length || 0} projects for this user`);
                    if (userProjects && userProjects.length > 0) {
                        userProjects.forEach((project, index) => {
                            console.log(`Project ${index + 1}:`);
                            console.log(`  ID: ${project.id}`);
                            console.log(`  Title: ${project.title || '(missing)'}`);
                            console.log(`  Name: ${project.name || '(missing)'}`);
                            console.log(`  Description: ${project.description || '(none)'}`);
                        });
                    }
                }
            } else {
                console.log('Login returned no session');
            }
        }
    } catch (err) {
        console.error('Unexpected error during testing:', err);
    }
}

run()
    .then(() => console.log('\nDatabase test completed'))
    .catch(err => console.error('Script failed:', err));