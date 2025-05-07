// Simple script to add qualification column and migrate data
import { createClient } from '@supabase/supabase-js';

// Set your Supabase URL and anon key here
const SUPABASE_URL = 'https://jnebkgmkgatejsjgbaqo.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImpuZWJrZ21rZ2F0ZWpzamdiYXFvIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDU4NTcwMjUsImV4cCI6MjA2MTQzMzAyNX0.sXXpX_p4Y3g4MQjbixgKun095dMl8yicYc3K4g6ieCM';

console.log('Starting migration script...');
console.log('SUPABASE_URL:', SUPABASE_URL);

// Create Supabase client
const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

async function run() {
    try {
        // Step 1: Check if qualification column exists, if not try to add it
        console.log('Step 1: Checking qualification column...');
        try {
            const { data: qualificationCheck, error: qualificationError } = await supabase
                .from('education')
                .select('qualification')
                .limit(1);

            if (qualificationError) {
                console.log('Qualification column does not exist, will try to add it');

                // Get a sample record to use for updating
                const { data: sampleData, error: sampleError } = await supabase
                    .from('education')
                    .select('*')
                    .limit(1);

                if (sampleError || !sampleData || sampleData.length === 0) {
                    console.error('Error fetching sample data:', sampleError?.message || 'No records found');
                } else {
                    // Try to add qualification column by updating a record
                    const { error: updateError } = await supabase
                        .from('education')
                        .update({ qualification: sampleData[0].degree || 'Sample Qualification' })
                        .eq('id', sampleData[0].id);

                    if (updateError) {
                        console.error('Error adding qualification column:', updateError.message);
                    } else {
                        console.log('Successfully added qualification column');
                    }
                }
            } else {
                console.log('Qualification column already exists');
            }
        } catch (err) {
            console.error('Error in step 1:', err);
        }

        // Step 2: Migrate data from degree to qualification
        console.log('Step 2: Migrating data from degree to qualification...');

        // Get all education records
        const { data: educationData, error: fetchError } = await supabase
            .from('education')
            .select('*');

        if (fetchError) {
            console.error('Error fetching education data:', fetchError.message);
            return;
        }

        console.log(`Found ${educationData?.length || 0} education records`);
        console.log('Sample record:', educationData[0]);

        let updatedCount = 0;

        // Update each record to copy degree to qualification if needed
        for (const edu of educationData || []) {
            if (edu.degree && (!edu.qualification || edu.qualification === null)) {
                console.log(`Updating record ${edu.id}: degree="${edu.degree}" -> qualification="${edu.degree}"`);

                const { error: updateError } = await supabase
                    .from('education')
                    .update({ qualification: edu.degree })
                    .eq('id', edu.id);

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