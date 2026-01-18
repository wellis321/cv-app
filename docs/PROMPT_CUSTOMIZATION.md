# Prompt Customisation Technical Documentation

## Overview

The CV rewriting system allows users to customise the instructions portion of the AI prompt while maintaining system-level structure and formatting requirements.

## Architecture

### Prompt Structure

The prompt sent to the AI consists of three main parts:

1. **System Role** (Fixed)
   - Defines the AI's role as a professional CV writer
   - Cannot be customised by users

2. **CV Data and Job Description** (Fixed)
   - Current CV content
   - Target job description
   - Cannot be customised by users

3. **Instructions** (Customizable)
   - Default system instructions
   - User's custom instructions (optional)
   - Merged together when generating prompts

### Data Flow

```
User Input (cv-variants/rewrite.php)
    ↓
Section Selection + Custom Instructions
    ↓
API Endpoint (api/ai-rewrite-cv.php)
    ↓
AIService::rewriteCvForJob()
    ↓
buildCvRewritePrompt()
    ↓
Merge Default + Custom Instructions
    ↓
AI Model (Ollama/OpenAI/Anthropic)
    ↓
Parsed Response
    ↓
Merge with Original CV Data
    ↓
Save to CV Variant
```

## Database Schema

### profiles Table

```sql
ALTER TABLE profiles 
ADD COLUMN cv_rewrite_prompt_instructions TEXT NULL 
COMMENT 'User-customizable instructions for CV rewrite prompts';
```

- **Type**: TEXT (nullable)
- **Purpose**: Stores user's custom prompt instructions
- **Default**: NULL (uses system defaults)
- **Max Length**: 2000 characters (enforced in application layer)

## Implementation Details

### File Structure

```
php/ai-service.php
    - buildCvRewritePrompt() - Builds the full prompt with custom instructions
    - rewriteCvForJob() - Main method that calls AI with options

api/ai-rewrite-cv.php
    - Accepts sections_to_rewrite and custom_instructions
    - Fetches user's custom instructions from database
    - Passes to AIService

cv-prompt-settings.php
    - UI for editing custom instructions
    - Character counter (2000 max)
    - Preview functionality
    - Reset to defaults

api/save-prompt-instructions.php
    - Saves user's custom instructions to database
    - Validates length (max 2000 chars)
    - CSRF protection

cv-variants/rewrite.php
    - Section selection checkboxes
    - Link to prompt settings
    - Form submission with sections_to_rewrite
```

### Prompt Building Logic

```php
// In buildCvRewritePrompt()
$defaultInstructions = "1. Maintain factual accuracy...\n2. ENHANCE and EXPAND...\n...";

$instructions = $defaultInstructions;
if (!empty($customInstructions)) {
    $instructions = $defaultInstructions . "\n\nAdditional User Instructions:\n" . $customInstructions;
}

$prompt .= "\nInstructions:\n" . $instructions . "\n\n";
```

### Section Selection

Users can select which CV sections to rewrite:
- `professional_summary` (always included)
- `work_experience`
- `skills`
- `education`
- `projects`
- `certifications`
- `professional_memberships`
- `interests`

The prompt JSON structure is dynamically built based on selected sections.

### Merge Logic

After AI returns rewritten sections, the system merges them with original CV data:

1. **Professional Summary**: Merged with `array_merge()`
2. **Work Experience**: Matched by ID, updates description and responsibility_categories
3. **Skills**: Adds new skills, preserves existing
4. **Education/Projects/Certifications/Memberships/Interests**: Matched by ID, updates descriptions

Sections not selected for rewriting are preserved from the original CV.

## Security Considerations

1. **Input Validation**
   - Max length: 2000 characters
   - Sanitized but not HTML-escaped (plain text instructions)
   - CSRF token verification

2. **SQL Injection**
   - Uses parameterized queries via `db()->update()`

3. **XSS Prevention**
   - Instructions are stored as plain text
   - Displayed with `e()` helper for HTML escaping

4. **Access Control**
   - Users can only edit their own instructions
   - Authentication required for all endpoints

## API Endpoints

### POST /api/save-prompt-instructions.php

**Request:**
```json
{
  "csrf_token": "...",
  "instructions": "User's custom instructions..."
}
```

**Response:**
```json
{
  "success": true,
  "message": "Instructions saved successfully"
}
```

### POST /api/ai-rewrite-cv.php

**Request:**
```json
{
  "csrf_token": "...",
  "sections_to_rewrite": ["professional_summary", "work_experience", "skills"],
  "job_description": "...",
  "variant_name": "..."
}
```

**Response:**
```json
{
  "success": true,
  "variant_id": "...",
  "message": "CV successfully rewritten for this job"
}
```

## Default Instructions

The system default instructions emphasize:

1. Factual accuracy
2. Enhancement over reduction
3. Keyword matching
4. Professional tone
5. Quantifiable achievements
6. Structure preservation

Users can override or supplement these with their own instructions.

## Testing

### Test Cases

1. **Empty Instructions**
   - Should use default instructions only
   - Verify prompt contains only defaults

2. **Custom Instructions**
   - Should merge with defaults
   - Verify both appear in prompt

3. **Section Selection**
   - Only selected sections should be in JSON structure
   - Verify merge logic handles missing sections

4. **Character Limit**
   - Max 2000 characters enforced
   - Error message on exceed

5. **Reset to Defaults**
   - Should restore default instructions
   - Verify database updated

## Future Enhancements

Potential improvements:

1. **Prompt Templates**
   - Pre-built instruction sets for different industries
   - Quick selection dropdown

2. **Prompt History**
   - Track which instructions produced best results
   - A/B testing capabilities

3. **Section-Specific Instructions**
   - Different instructions for different sections
   - More granular control

4. **Prompt Analytics**
   - Track prompt effectiveness
   - Suggest improvements based on results

## Migration

To enable prompt customisation:

1. Run migration: `database/20250126_add_custom_prompt_instructions.sql`
2. Verify column exists: `SHOW COLUMNS FROM profiles LIKE 'cv_rewrite_prompt_instructions';`
3. Test saving instructions via UI
4. Verify instructions appear in generated prompts

## Troubleshooting

### Column Not Found Error

If you see "Unknown column 'cv_rewrite_prompt_instructions'":
- Run the migration: `database/20250126_add_custom_prompt_instructions.sql`
- Verify migration completed successfully

### Instructions Not Appearing in Prompt

- Check user has saved instructions in database
- Verify `buildCvRewritePrompt()` receives `$options['custom_instructions']`
- Check AI service logs for actual prompt sent

### Sections Not Being Rewritten

- Verify sections are selected in UI
- Check `sections_to_rewrite` is passed to API
- Verify merge logic handles all section types
- Check AI response includes requested sections

