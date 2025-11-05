<?php
session_start();

// Site configuration
define('SITE_NAME', 'University Complaint System');
define('SITE_URL', 'http://localhost/complaint_system');
define('UPLOAD_DIR', 'uploads/');

// Email configuration (for PHPMailer)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'adyoshan155@gmail.com');
define('SMTP_PASSWORD', 'knof cnfx izhs food');
define('FROM_EMAIL', 'noreply@university.edu');
define('FROM_NAME', 'University Complaint System');

// Include database
require_once 'database.php';

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /auth/login.php');
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        header('Location: /index.php');
        exit;
    }
}

function requireAdmin() {
    requireRole('admin');
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function formatDate($date) {
    return date('M d, Y \a\t h:i A', strtotime($date));
}

function getStatusBadge($status) {
    $badges = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'in_progress' => 'bg-blue-100 text-blue-800',
        'resolved' => 'bg-green-100 text-green-800',
        'withdrawn' => 'bg-gray-100 text-gray-800',
        'approved' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800'
    ];
    
    return $badges[$status] ?? 'bg-gray-100 text-gray-800';
}
?>