<?php
/**
 * CV Variants – redirect to content editor.
 * All variant list/create/rename/delete is in the content editor (#cv-variants).
 */

require_once __DIR__ . '/php/helpers.php';

requireAuth();

redirect('/content-editor.php#cv-variants');
