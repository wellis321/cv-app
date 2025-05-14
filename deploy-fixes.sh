#!/bin/bash
# Script to deploy profile fix changes to production

echo "🚀 Deploying profile fixes to production"

# Step 1: Build the project
echo "📦 Building the project..."
npm run build

if [ $? -ne 0 ]; then
  echo "❌ Build failed, aborting deployment"
  exit 1
fi

# Step 2: Run the new migration on the Supabase production database
echo "🔄 To apply the database fix to production only:"
echo "1. Go to your Supabase project dashboard"
echo "2. Navigate to SQL Editor"
echo "3. Copy and paste the content of fix-production-db.sql"
echo "4. Run the SQL query"
echo ""
echo "📝 Key fixes in the production-only migration:"
echo "- Makes 'username' field nullable to allow updates"
echo "- Ensures the photo_url column is present in production"
echo "- Copies data from production-specific fields to development fields"
echo "- Updates RLS policies to match your working development environment"

# Step 3: Deploy to Vercel
echo "🚀 Deploying to Vercel..."
vercel --prod

echo "✅ Deployment complete!"
echo ""
echo "After deployment:"
echo "1. Log out and log back in to your application"
echo "2. Check that your profile data is now persistent"
echo "3. Verify that you can update all profile fields using your working development code"