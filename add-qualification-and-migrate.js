// Simple script to add qualification column and migrate data
import { createClient } from '@supabase/supabase-js';
import dotenv from 'dotenv';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';
import fs from 'fs';

// Load environment variables from .env file
const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);
dotenv.config({ path: join(__dirname, '.env') });

const SUPABASE_URL = process.env.PUBLIC_SUPABASE_URL;
const SUPABASE_ANON_KEY = process.env.PUBLIC_SUPABASE_ANON_KEY;

console.log('Starting migration script...');
console.log('SUPABASE_URL:', SUPABASE_URL);

if (!SUPABASE_URL || !SUPABASE_ANON_KEY) {
    console.error('Missing Supabase environment variables. Check your .env file.');
    process.exit(1);
}

// Create Supabase client
const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

async function run() {
    try {
        // Step 1: Check if qualification column exists, if not try to add it
        console.log('Step 1: Checking qualification column...');
        const { data: qualificationCheck, error: qualificationError } = await supabase
            .from('education')
            .select('qualification')
            .limit(1);

        if (qualificationError && qualificationError.code === 'PGRST116') {
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
    .catch(err => console.error('Script failed:', err))
    .finally(() => process.exit(0));