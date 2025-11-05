<?php
require_once '../config/config.php';
requireAdmin();

// compute project-aware base URL so fallback redirects stay inside the project
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

if ($_POST && isset($_POST['action']) && isset($_POST['user_id'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];
    
    if ($action === 'approve') {
        $query = "UPDATE users SET status = 'approved' WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $user_id);
        
        if ($stmt->execute()) {
            // Get user details for notification
            $user_query = "SELECT * FROM users WHERE id = :id";
            $user_stmt = $db->prepare($user_query);
            $user_stmt->bindParam(':id', $user_id);
            $user_stmt->execute();
            $user = $user_stmt->fetch(PDO::FETCH_ASSOC);
            
            // Create notification for user
            $notif_query = "INSERT INTO notifications (user_id, title, message) 
                           VALUES (:user_id, :title, :message)";
            $notif_stmt = $db->prepare($notif_query);
            $notif_stmt->bindParam(':user_id', $user_id);
            $title = 'Account Approved';
            $message = 'Your account has been approved! You can now log in and start using the complaint system.';
            $notif_stmt->bindParam(':title', $title);
            $notif_stmt->bindParam(':message', $message);
            $notif_stmt->execute();
        }
    } elseif ($action === 'reject') {
        $query = "UPDATE users SET status = 'rejected' WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
    }
}

// Redirect back to the referring page or project-aware dashboard
$redirect = $_SERVER['HTTP_REFERER'] ?? ($base_url . '/dashboard.php');
header("Location: $redirect");
exit;
?>