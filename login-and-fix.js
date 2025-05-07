// Script to login and fix education records
import { createClient } from '@supabase/supabase-js';
import { writeFileSync } from 'fs';

// Set your Supabase URL and anon key here
const SUPABASE_URL = 'https://jnebkgmkgatejsjgbaqo.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImpuZWJrZ21rZ2F0ZWpzamdiYXFvIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDU4NTcwMjUsImV4cCI6MjA2MTQzMzAyNX0.sXXpX_p4Y3g4MQjbixgKun095dMl8yicYc3K4g6ieCM';

const EMAIL = process.argv[2];
const PASSWORD = process.argv[3];

if (!EMAIL || !PASSWORD) {
    console.error('Usage: node login-and-fix.js <email> <password>');
    process.exit(1);
}

console.log('Starting login and migration script...');
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

        // Step 2: Find all education records for this user
        console.log('Step 2: Fetching education records...');
        const { data: educationData, error: fetchError } = await supabase
            .from('education')
            .select('*')
            .eq('profile_id', session.user.id);

        if (fetchError) {
            console.error('Error fetching education data:', fetchError.message);
            return;
        }

        console.log(`Found ${educationData?.length || 0} education records`);

        if (educationData && educationData.length > 0) {
            console.log('Sample record:', JSON.stringify(educationData[0], null, 2));

            // Write the education data to a file for inspection
            writeFileSync('education-records.json', JSON.stringify(educationData, null, 2));
            console.log('Wrote education records to education-records.json');
        }

        // Step 3: Add qualification field and update records
        console.log('Step 3: Updating records...');
        let updatedCount = 0;

        for (const edu of educationData || []) {
            if (edu.degree && (!edu.qualification || edu.qualification === null)) {
                console.log(`Updating record ${edu.id}: degree="${edu.degree}" -> qualification="${edu.degree}"`);

                const { error: updateError } = await supabase
                    .from('education')
                    .update({ qualification: edu.degree })
                    .eq('id', edu.id)
                    .eq('profile_id', session.user.id);

                if (updateError) {
                    console.error(`Error updating record ${edu.id}:`, updateError.message);
                } else {
                    updatedCount++;
                }
            }
        }

        console.log(`Migration complete. Updated ${updatedCount} of ${educationData?.length || 0} records.`);
    } catch (err) {
        console.error('Unexpected error during migration:', err);
    }
}

run()
    .then(() => console.log('Migration script completed'))
    .catch(err => console.error('Script failed:', err));