<?php
// Email configuration using PHPMailer
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailConfig {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.gmail.com';      // Gmail SMTP server
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = 'mdkpjayashan@gmail.com'; // Replace with your Gmail
            $this->mail->Password   = 'coht rvpd kgee ijye';    // Replace with your Gmail App Password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = 587;
            
            // Recipients
            $this->mail->setFrom('mdkpjayashan@gmail.com', 'Complaint Management System');
            
            // Content settings
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            error_log("Email configuration error: " . $e->getMessage());
        }
    }
    
    /**
     * Send email notification to admins for new complaint
     */
    public function notifyAdminsOnNewComplaint($complaintData, $userData) {
        try {
            $this->mail->Subject = 'New Complaint Submitted: ' . $complaintData['title'];
            
            // Create HTML email body
            $body = $this->getNewComplaintEmailTemplate($complaintData, $userData);
            $this->mail->Body = $body;
            
            // Plain text alternative
            $this->mail->AltBody = strip_tags($body);
            
            // Add all admin recipients
            $database = new Database();
            $db = $database->getConnection();
            
            $admin_query = "SELECT email FROM users WHERE role = 'admin'";
            $admin_stmt = $db->prepare($admin_query);
            $admin_stmt->execute();
            $admins = $admin_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($admins as $admin) {
                $this->mail->addAddress($admin['email']);
            }
            
            return $this->mail->send();
            
        } catch (Exception $e) {
            error_log("Admin notification email failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send email notification to user on complaint update
     */
    public function notifyUserOnComplaintUpdate($complaintData, $userData, $newStatus, $notes = '') {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($userData['email'], $userData['full_name']);
            
            $this->mail->Subject = 'Your Complaint Status Has Been Updated';
            
            // Create HTML email body
            $body = $this->getComplaintUpdateEmailTemplate($complaintData, $userData, $newStatus, $notes);
            $this->mail->Body = $body;
            
            // Plain text alternative
            $this->mail->AltBody = strip_tags($body);
            
            return $this->mail->send();
            
        } catch (Exception $e) {
            error_log("User notification email failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Email template for new complaint notification
     */
    private function getNewComplaintEmailTemplate($complaintData, $userData) {
        $priorityColor = $complaintData['priority'] === 'high' ? '#dc2626' : 
                        ($complaintData['priority'] === 'medium' ? '#f59e0b' : '#16a34a');
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9fafb; }
                .complaint-details { background: white; padding: 15px; border-radius: 8px; margin: 15px 0; }
                .priority { display: inline-block; padding: 4px 8px; border-radius: 4px; color: white; font-size: 12px; font-weight: bold; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🚨 New Complaint Submitted</h2>
                </div>
                <div class='content'>
                    <p>A new complaint has been submitted by <strong>{$userData['full_name']}</strong> and requires your attention.</p>
                    
                    <div class='complaint-details'>
                        <h3>Complaint Details:</h3>
                        <p><strong>Title:</strong> {$complaintData['title']}</p>
                        <p><strong>Category:</strong> " . ucfirst($complaintData['category']) . "</p>
                        <p><strong>Priority:</strong> <span class='priority' style='background-color: {$priorityColor}'>" . ucfirst($complaintData['priority']) . "</span></p>
                        <p><strong>Description:</strong><br>" . nl2br(htmlspecialchars($complaintData['description'])) . "</p>
                        <p><strong>Submitted By:</strong> {$userData['full_name']} ({$userData['email']})</p>
                        <p><strong>Submitted On:</strong> " . date('F j, Y, g:i a', strtotime($complaintData['created_at'])) . "</p>
                    </div>
                    
                    <p>Please log in to the admin panel to review and take appropriate action.</p>
                    <p><a href='http://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/admin/complaints.php' style='background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Complaints</a></p>
                </div>
                <div class='footer'>
                    <p>This is an automated notification from the Complaint Management System.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Email template for complaint update notification
     */
    private function getComplaintUpdateEmailTemplate($complaintData, $userData, $newStatus, $notes) {
        $statusColor = $newStatus === 'resolved' ? '#16a34a' : 
                      ($newStatus === 'in_progress' ? '#f59e0b' : '#6b7280');
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9fafb; }
                .status-update { background: white; padding: 15px; border-radius: 8px; margin: 15px 0; }
                .status { display: inline-block; padding: 6px 12px; border-radius: 4px; color: white; font-weight: bold; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>📋 Complaint Status Updated</h2>
                </div>
                <div class='content'>
                    <p>Dear {$userData['full_name']},</p>
                    <p>Your complaint status has been updated. Here are the details:</p>
                    
                    <div class='status-update'>
                        <h3>Complaint: {$complaintData['title']}</h3>
                        <p><strong>New Status:</strong> <span class='status' style='background-color: {$statusColor}'>" . ucfirst(str_replace('_', ' ', $newStatus)) . "</span></p>";
                        
        if ($notes) {
            $body .= "<p><strong>Admin Notes:</strong><br>" . nl2br(htmlspecialchars($notes)) . "</p>";
        }
        
        $body .= "
                        <p><strong>Updated On:</strong> " . date('F j, Y, g:i a') . "</p>
                    </div>
                    
                    <p>You can view your complaint details and track progress by logging into your account.</p>
                    <p><a href='http://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/student/complaints.php' style='background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View My Complaints</a></p>
                </div>
                <div class='footer'>
                    <p>This is an automated notification from the Complaint Management System.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $body;
    }
}
?>
