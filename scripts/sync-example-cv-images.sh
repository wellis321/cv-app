#!/bin/bash
# Script to help sync example CV project images to production
# Usage: ./scripts/sync-example-cv-images.sh [production-server-path]

PROJECT_USER_ID="80b7c3eb-fdc5-4cd7-95cb-dc975e0095dd"
LOCAL_STORAGE_DIR="storage/projects/${PROJECT_USER_ID}"

echo "Example CV Project Image Sync Helper"
echo "====================================="
echo ""

if [ ! -d "$LOCAL_STORAGE_DIR" ]; then
    echo "Error: Local storage directory not found: $LOCAL_STORAGE_DIR"
    exit 1
fi

echo "Local files found:"
ls -lh "$LOCAL_STORAGE_DIR" | tail -n +2
echo ""

echo "Files to upload to production:"
echo "-----------------------------"
for file in "$LOCAL_STORAGE_DIR"/*; do
    if [ -f "$file" ]; then
        filename=$(basename "$file")
        size=$(du -h "$file" | cut -f1)
        echo "  $filename ($size)"
    fi
done
echo ""

if [ -z "$1" ]; then
    echo "To upload these files to production, use SCP or SFTP:"
    echo ""
    echo "Example SCP command:"
    echo "  scp ${LOCAL_STORAGE_DIR}/* user@your-production-server:/path/to/app/storage/projects/${PROJECT_USER_ID}/"
    echo ""
    echo "Or use rsync:"
    echo "  rsync -avz ${LOCAL_STORAGE_DIR}/ user@your-production-server:/path/to/app/storage/projects/${PROJECT_USER_ID}/"
    echo ""
    echo "Make sure the directory exists on production first:"
    echo "  mkdir -p storage/projects/${PROJECT_USER_ID}"
    echo "  chmod 755 storage/projects/${PROJECT_USER_ID}"
else
    PROD_PATH="$1"
    echo "Production path provided: $PROD_PATH"
    echo ""
    echo "Run this command to sync:"
    echo "  rsync -avz ${LOCAL_STORAGE_DIR}/ ${PROD_PATH}/storage/projects/${PROJECT_USER_ID}/"
fi
