#!/bin/sh
# Download Liberation Serif fonts for academic template PDF (matches Georgia/Times New Roman in preview)
# Run from project root: ./scripts/download-academic-fonts.sh

set -e
FONTS_DIR="static/fonts/liberation-serif"
# TTF tarball from release (see https://github.com/liberationfonts/liberation-fonts/releases/tag/2.1.5)
# Note: releases/download has source only; use files attachment URL for prebuilt TTFs
RELEASE_URL="https://github.com/liberationfonts/liberation-fonts/files/7261482/liberation-fonts-ttf-2.1.5.tar.gz"
TMP_DIR=$(mktemp -d)

echo "Downloading Liberation Fonts..."
curl -sL -o "$TMP_DIR/liberation.tar.gz" "$RELEASE_URL"
echo "Extracting..."
tar -xzf "$TMP_DIR/liberation.tar.gz" -C "$TMP_DIR"

mkdir -p "$FONTS_DIR"
cp "$TMP_DIR"/liberation-fonts-ttf-*/LiberationSerif-Regular.ttf "$FONTS_DIR/"
cp "$TMP_DIR"/liberation-fonts-ttf-*/LiberationSerif-Bold.ttf "$FONTS_DIR/"
cp "$TMP_DIR"/liberation-fonts-ttf-*/LiberationSerif-Italic.ttf "$FONTS_DIR/"
cp "$TMP_DIR"/liberation-fonts-ttf-*/LiberationSerif-BoldItalic.ttf "$FONTS_DIR/"

rm -rf "$TMP_DIR"
echo "Done. Fonts saved to $FONTS_DIR/"
