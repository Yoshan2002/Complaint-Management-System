<?php
require_once '../config/config.php';
requireRole('staff');

// compute project-aware base URL so links stay inside the project
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

// Get current user data
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($full_name) || empty($email)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if email is already taken by another user
        $email_query = "SELECT id FROM users WHERE email = :email AND id != :user_id";
        $email_stmt = $db->prepare($email_query);
        $email_stmt->bindParam(':email', $email);
        $email_stmt->bindParam(':user_id', $_SESSION['user_id']);
        $email_stmt->execute();
        
        if ($email_stmt->rowCount() > 0) {
            $error = 'Email address is already in use by another account.';
        } else {
            // Handle password change
            $update_password = false;
            $hashed_password = $user['password']; // Keep current password by default
            
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $error = 'Please enter your current password to change it.';
                } elseif (!password_verify($current_password, $user['password'])) {
                    $error = 'Current password is incorrect.';
                } elseif (strlen($new_password) < 6) {
                    $error = 'New password must be at least 6 characters long.';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'New passwords do not match.';
                } else {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_password = true;
                }
            }
            
            if (!$error) {
                // Update user information
                $update_query = "UPDATE users SET full_name = :full_name, email = :email, password = :password, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
                $update_stmt = $db->prepare($update_query);
                $update_stmt->bindParam(':full_name', $full_name);
                $update_stmt->bindParam(':email', $email);
                $update_stmt->bindParam(':password', $hashed_password);
                $update_stmt->bindParam(':id', $_SESSION['user_id']);
                
                if ($update_stmt->execute()) {
                    // Update session data
                    $_SESSION['full_name'] = $full_name;
                    $_SESSION['email'] = $email;
                    
                    $success = 'Profile updated successfully!' . ($update_password ? ' Your password has been changed.' : '');
                    
                    // Refresh user data
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $error = 'Failed to update profile. Please try again.';
                }
            }
        }
    }
}

$page_title = 'My Profile - Staff Portal';
include '../includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="fade-in">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Profile</h1>
            <p class="text-gray-600">Manage your account information and settings</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Account Information</h2>
                    
                    <?php if ($success): ?>
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                            <i class="fas fa-check-circle mr-2"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="full_name" name="full_name" required
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>"
                                       class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" required
                                       value="<?php echo htmlspecialchars($user['email']); ?>"
                                       class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h3>
                            <p class="text-sm text-gray-600 mb-4">Leave password fields empty if you don't want to change your password.</p>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Current Password
                                    </label>
                                    <input type="password" id="current_password" name="current_password"
                                           class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                            New Password
                                        </label>
                                        <input type="password" id="new_password" name="new_password"
                                               class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        <p class="text-sm text-gray-500 mt-1">Minimum 6 characters</p>
                                    </div>

                                    <div>
                                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                            Confirm New Password
                                        </label>
                                        <input type="password" id="confirm_password" name="confirm_password"
                                               class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t">
                            <a href="<?php echo $base_url; ?>/staff/dashboard.php" class="text-gray-600 hover:text-gray-700">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-save mr-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Summary -->
            <div class="space-y-6">
                <!-- Account Info -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Summary</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Role</span>
                            <span class="capitalize font-medium text-blue-600"><?php echo $user['role']; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Status</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo getStatusBadge($user['status']); ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Member Since</span>
                            <span class="text-gray-900"><?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">My Statistics</h3>
                    <?php
                    $user_id = $_SESSION['user_id'];
                    $stats = [
                        'total' => $db->query("SELECT COUNT(*) FROM complaints WHERE user_id = $user_id")->fetchColumn(),
                        'pending' => $db->query("SELECT COUNT(*) FROM complaints WHERE user_id = $user_id AND status = 'pending'")->fetchColumn(),
                        'resolved' => $db->query("SELECT COUNT(*) FROM complaints WHERE user_id = $user_id AND status = 'resolved'")->fetchColumn()
                    ];
                    ?>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Complaints</span>
                            <span class="font-bold text-gray-900"><?php echo $stats['total']; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Pending</span>
                            <span class="font-bold text-yellow-600"><?php echo $stats['pending']; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Resolved</span>
                            <span class="font-bold text-green-600"><?php echo $stats['resolved']; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="<?php echo $base_url; ?>/staff/submit.php" class="block w-full bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Submit Complaint
                        </a>
                        <a href="<?php echo $base_url; ?>/staff/complaints.php" class="block w-full bg-gray-100 text-gray-700 text-center py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-list mr-2"></i>View Complaints
                        </a>
                        <a href="<?php echo $base_url; ?>/staff/notifications.php" class="block w-full bg-gray-100 text-gray-700 text-center py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-bell mr-2"></i>Notifications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>