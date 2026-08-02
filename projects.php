<?php
require_once __DIR__ . '/php/helpers.php';
requireAuth();
// Redirect to new content editor
$editParam = isset($_GET['edit']) ? '&edit=' . urlencode($_GET['edit']) : '';
redirect('/content-editor.php#projects' . $editParam);
exit;
