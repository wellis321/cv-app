import { supabase } from './supabase';

export interface UploadResult {
    success: boolean;
    url?: string;
    error?: string;
}

/**
 * Uploads a file to Supabase storage
 * @param userId User ID to associate with the file
 * @param file File to upload
 * @param bucket Storage bucket name
 * @param path Optional path within the bucket
 * @returns Upload result with public URL if successful
 */
export async function uploadFile(
    userId: string,
    file: File,
    bucket: string,
    path: string = ''
): Promise<UploadResult> {
    try {
        // Create a unique file name using the original file extension
        const fileExt = file.name.split('.').pop();
        const fileName = `${userId}/${Date.now()}.${fileExt}`;
        const fullPath = path ? `${path}/${fileName}` : fileName;

        // Upload file to Supabase storage
        const { data, error } = await supabase.storage
            .from(bucket)
            .upload(fullPath, file, {
                cacheControl: '3600',
                upsert: true
            });

        if (error) {
            console.error('Error uploading file:', error);
            return { success: false, error: error.message };
        }

        // Get the public URL for the file
        const { data: { publicUrl } } = supabase.storage
            .from(bucket)
            .getPublicUrl(data.path);

        return { success: true, url: publicUrl };
    } catch (error) {
        console.error('Unexpected error during file upload:', error);
        return {
            success: false,
            error: error instanceof Error ? error.message : 'Unknown error during upload'
        };
    }
}

/**
 * Deletes a file from Supabase storage
 * @param bucket Storage bucket name
 * @param path Path to the file
 * @returns Success status and optional error
 */
export async function deleteFile(bucket: string, path: string): Promise<{ success: boolean; error?: string }> {
    try {
        const { error } = await supabase.storage.from(bucket).remove([path]);

        if (error) {
            console.error('Error deleting file:', error);
            return { success: false, error: error.message };
        }

        return { success: true };
    } catch (error) {
        console.error('Unexpected error during file deletion:', error);
        return {
            success: false,
            error: error instanceof Error ? error.message : 'Unknown error during deletion'
        };
    }
}

/**
 * Extracts the file path from a public URL
 * @param url Public URL from Supabase storage
 * @param bucket Bucket name
 * @returns File path or null if not found
 */
export function getPathFromUrl(url: string, bucket: string): string | null {
    // Try to extract the path from the URL
    const regex = new RegExp(`${bucket}/([^?#]+)`);
    const match = url.match(regex);

    return match ? match[1] : null;
}