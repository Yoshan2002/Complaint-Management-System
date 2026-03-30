<?php
require_once '../config/config.php';

// Destroy all session data
$_SESSION = [];
if (session_id()) {
    session_unset();
    session_destroy();
}

// Redirect to project home
header('Location: /');
exit;
?>