import { json } from '@sveltejs/kit';
import type { RequestHandler } from './$types';
import { supabase } from '$lib/supabase';
import { safeLog } from '$lib/config';
import type { StorageError } from '@supabase/storage-js';
import { ensureBucketExists, ensurePublicAccess } from '$lib/fileUpload';

export const GET: RequestHandler = async ({ url }) => {
    const bucket = url.searchParams.get('bucket') || 'profile-photos';
    const requestId = crypto.randomUUID();
    const tryCreate = url.searchParams.get('create') === 'true';

    safeLog('debug', `[${requestId}] Testing storage bucket access: ${bucket}`);

    try {
        // Check if bucket exists
        const { data: buckets, error: listError } = await supabase.storage.listBuckets();

        if (listError) {
            safeLog('error', `[${requestId}] Error listing buckets:`, listError);
            return json({
                success: false,
                error: 'Failed to list buckets',
                details: listError.message,
                requestId
            });
        }

        let bucketExists = buckets.some(b => b.name === bucket);
        let bucketCreated = false;
        let accessFixed = false;

        // If bucket doesn't exist and create flag is set, try to create it
        if (!bucketExists && tryCreate) {
            safeLog('info', `[${requestId}] Attempting to create bucket: ${bucket}`);

            const { exists, error: createError } = await ensureBucketExists(bucket);

            if (!exists) {
                safeLog('error', `[${requestId}] Failed to create bucket: ${createError}`);
                return json({
                    success: false,
                    error: 'Failed to create bucket',
                    details: createError,
                    availableBuckets: buckets.map(b => b.name),
                    requestId
                });
            }

            bucketExists = true;
            bucketCreated = true;

            safeLog('info', `[${requestId}] Successfully created bucket: ${bucket}`);
        } else if (!bucketExists) {
            safeLog('warn', `[${requestId}] Bucket ${bucket} not found`);
            return json({
                success: false,
                error: 'Bucket not found',
                availableBuckets: buckets.map(b => b.name),
                requestId,
                canCreate: true
            });
        }

        // Check and fix public access if needed
        if (bucketExists && tryCreate) {
            safeLog('info', `[${requestId}] Checking bucket public access: ${bucket}`);

            const { success, error: accessError } = await ensurePublicAccess(bucket);

            if (!success) {
                safeLog('warn', `[${requestId}] Public access check failed: ${accessError}`);
            } else {
                accessFixed = true;
                safeLog('info', `[${requestId}] Public access verified for bucket: ${bucket}`);
            }
        }

        // Try to list bucket contents
        const { data: files, error: filesError } = await supabase.storage.from(bucket).list();

        if (filesError) {
            safeLog('error', `[${requestId}] Error listing bucket contents:`, filesError);
            return json({
                success: false,
                error: 'Failed to list bucket contents',
                details: filesError.message,
                bucketFound: true,
                requestId
            });
        }

        // Create a diagnostic image for testing
        const testImageBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
        const testImageBlob = Uint8Array.from(atob(testImageBase64), c => c.charCodeAt(0));

        // Try to upload test image
        const testPath = `test/${requestId}.png`;
        const { data: uploadData, error: uploadError } = await supabase.storage
            .from(bucket)
            .upload(testPath, testImageBlob, {
                contentType: 'image/png',
                cacheControl: '3600',
                upsert: true
            });

        let uploadSuccess = !uploadError;
        let publicUrl = null;

        if (uploadSuccess) {
            // Try to get public URL
            const { data: urlData } = supabase.storage
                .from(bucket)
                .getPublicUrl(testPath);

            publicUrl = urlData.publicUrl;

            // Clean up test file
            await supabase.storage.from(bucket).remove([testPath]);
        }

        return json({
            success: true,
            bucketFound: true,
            bucketCreated,
            accessFixed,
            bucketAccess: true,
            uploadTest: uploadSuccess,
            fileCount: files.length,
            publicUrlGenerated: !!publicUrl,
            testUrl: publicUrl,
            requestId
        });
    } catch (err) {
        safeLog('error', `[${requestId}] Unexpected error testing storage:`, err);
        return json({
            success: false,
            error: 'Unexpected error testing storage',
            message: err instanceof Error ? err.message : String(err),
            requestId
        });
    }
};