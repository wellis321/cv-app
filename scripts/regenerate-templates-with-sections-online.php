#!/usr/bin/env php
<?php
/**
 * Regenerate template HTML for all templates that have template_config.
 * This adds the sections_online conditional wrapper so online CV section visibility works.
 * Run after applying 20250305_add_sections_online.sql
 *
 * Usage: php scripts/regenerate-templates-with-sections-online.php
 */

$root = dirname(__DIR__);
require_once $root . '/php/helpers.php';
require_once $root . '/php/template-config-to-twig.php';

$templates = db()->fetchAll("SELECT id, user_id, template_name, template_config FROM cv_templates WHERE template_config IS NOT NULL AND template_config != ''");
$updated = 0;
$errors = 0;

foreach ($templates as $t) {
    $config = json_decode($t['template_config'], true);
    if (!$config || !is_array($config)) {
        echo "Skip {$t['template_name']} (id={$t['id']}): invalid config\n";
        $errors++;
        continue;
    }
    try {
        $result = convertConfigToTwig($config);
        db()->update('cv_templates', [
            'template_html' => $result['html'],
            'template_css' => $result['css'] ?? ''
        ], 'id = ?', [$t['id']]);
        echo "Updated: {$t['template_name']} (id={$t['id']})\n";
        $updated++;
    } catch (Exception $e) {
        echo "Error {$t['template_name']}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\nDone. Updated: $updated, Errors: $errors\n";
