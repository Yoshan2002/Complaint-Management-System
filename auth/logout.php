<?php
require_once '../config/config.php';

// Destroy all session data
$_SESSION = [];
if (session_id()) {
    session_unset();
    session_destroy();
}

// Redirect to project home using BASE_PATH (works under subfolders)
$root = defined('BASE_PATH') ? BASE_PATH : rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
header('Location: ' . $root . '/');
exit;
?>