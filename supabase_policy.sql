-- Policy to allow users to upload their own photos (to be executed in Supabase SQL Editor)
create policy "Users can upload their own photos" on storage.objects for insert to authenticated with check (bucket_id = 'profile-photos' AND auth.uid()::text = (storage.foldername(name))[1]);
-- Policy to allow users to read their own photos
create policy "Users can view their own photos"
on storage.objects for select
to authenticated
using (bucket_id = 'profile-photos' AND auth.uid()::text = (storage.foldername(name))[1]);
-- Policy to allow users to delete their own photos
create policy "Users can delete their own photos"
on storage.objects for delete
to authenticated
using (bucket_id = 'profile-photos' AND auth.uid()::text = (storage.foldername(name))[1]);
-- Policy to allow users to update their own photos
create policy "Users can update their own photos" on storage.objects for update to authenticated with check (bucket_id = 'profile-photos' AND auth.uid()::text = (storage.foldername(name))[1]);
-- Policy to allow anyone to view profile photos (optional, if you want photos publicly accessible)
create policy "Profile photos are publicly accessible" on storage.objects for select using (bucket_id = 'profile-photos');
