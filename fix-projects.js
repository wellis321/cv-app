// Script to fix projects name/title fields
import { createClient } from '@supabase/supabase-js';
import { writeFileSync } from 'fs';

// Set your Supabase URL and anon key here
const SUPABASE_URL = 'https://jnebkgmkgatejsjgbaqo.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImpuZWJrZ21rZ2F0ZWpzamdiYXFvIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDU4NTcwMjUsImV4cCI6MjA2MTQzMzAyNX0.sXXpX_p4Y3g4MQjbixgKun095dMl8yicYc3K4g6ieCM';

const EMAIL = process.argv[2];
const PASSWORD = process.argv[3];

if (!EMAIL || !PASSWORD) {
    console.error('Usage: node fix-projects.js <email> <password>');
    process.exit(1);
}

console.log('Starting projects fix script...');
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
            console.log('Sample project before update:', JSON.stringify(projectsData[0], null, 2));

            // Write the projects data to a file for inspection
            writeFileSync('projects-before.json', JSON.stringify(projectsData, null, 2));
            console.log('Wrote pre-update projects data to projects-before.json');

            // Step 3: Update each project
            console.log('Step 3: Updating projects...');
            let updatedCount = 0;

            for (const project of projectsData) {
                let needsUpdate = false;
                let updateData = {};

                // If project has title but not name
                if (project.title && (!project.name || project.name === null)) {
                    console.log(`Project ${project.id} has title but not name`);
                    updateData.name = project.title;
                    needsUpdate = true;
                }

                // If project has name but not title
                if (project.name && (!project.title || project.title === null)) {
                    console.log(`Project ${project.id} has name but not title`);
                    updateData.title = project.name;
                    needsUpdate = true;
                }

                if (needsUpdate) {
                    console.log(`Updating project ${project.id}:`, updateData);

                    const { error: updateError } = await supabase
                        .from('projects')
                        .update(updateData)
                        .eq('id', project.id)
                        .eq('profile_id', session.user.id);

                    if (updateError) {
                        console.error(`Error updating project ${project.id}:`, updateError.message);
                    } else {
                        updatedCount++;
                    }
                }
            }

            console.log(`Updated ${updatedCount} projects`);

            // Fetch updated projects
            const { data: updatedProjectsData } = await supabase
                .from('projects')
                .select('*')
                .eq('profile_id', session.user.id);

            if (updatedProjectsData && updatedProjectsData.length > 0) {
                console.log('Sample project after update:', JSON.stringify(updatedProjectsData[0], null, 2));
                writeFileSync('projects-after.json', JSON.stringify(updatedProjectsData, null, 2));
                console.log('Wrote updated projects data to projects-after.json');
            }
        } else {
            console.log('No projects found for this user');
        }
    } catch (err) {
        console.error('Unexpected error during project fix:', err);
    }
}

run()
    .then(() => console.log('Project fix script completed'))
    .catch(err => console.error('Script failed:', err));