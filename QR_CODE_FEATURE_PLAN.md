# QR Code Feature for PDF CVs

## Overview
Add a QR code to PDF exports that links to the user's online CV (e.g., `https://cv-app.vercel.app/cv/@wellis`)

## Implementation Plan

### 1. Install QR Code Library
```bash
npm install qrcode
npm install --save-dev @types/qrcode
```

### 2. Update PDF Configuration
Add to `src/lib/pdfGenerator.ts`:
- Add `includeQRCode: boolean` to `PdfExportConfig` interface
- Default to `false` in `defaultPdfConfig`

### 3. Update PDF Generator
In `createCvDocDefinition` function:
- Generate QR code image from the user's CV URL
- Place QR code at the top-right of the first page (opposite to name)
- Make it a clickable link
- Size: ~70x70 pixels (or similar to photo size)

### 4. Update UI
In `src/routes/preview-cv/+page.svelte`:
- Add checkbox toggle: "Include QR code linking to online CV"
- Position it with the other PDF options
- Style: Similar to "Include Photo" toggle

### 5. Get User's Online CV URL
- Use the username from profile to construct: `https://cv-app.vercel.app/cv/@${username}`
- Or use the current URL if viewing on the public CV page

## Technical Details

### QR Code Generation
```typescript
import QRCode from 'qrcode';

// Generate QR code as base64 image
const qrCodeDataUrl = await QRCode.toDataURL(cvUrl, {
  width: 200,
  margin: 2,
  color: { dark: '#000000', light: '#FFFFFF' }
});
```

### PDF Placement
- Top-right corner of header
- Same size as profile photo (70x70 pixels)
- Maintain professional spacing

### User Control
- Toggle on/off in PDF export options
- Default: ON (since it's a useful feature)
- Users can disable if they don't want it

## Benefits
1. ✅ Easy access to online portfolio
2. ✅ Modern professional touch
3. ✅ Better ATS and hiring managers can quickly see more
4. ✅ Optional - users can toggle it off
