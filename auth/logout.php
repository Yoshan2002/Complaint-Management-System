<?php
require_once '../config/config.php';

// compute project-aware base URL so redirect stays inside the project
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// Destroy all session data
$_SESSION = [];
if (session_id()) {
    session_unset();
    session_destroy();
}

// Redirect to project home
header("Location: {$base_url}/");
exit;
?>