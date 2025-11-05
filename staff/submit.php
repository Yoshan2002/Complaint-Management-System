<?php
require_once '../config/config.php';
requireRole('staff');

// compute project-aware base URL so links stay inside the project
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$success = '';
$error = '';

if ($_POST) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $user_id = $_SESSION['user_id'];
    
    if (empty($title) || empty($description) || empty($category)) {
        $error = 'Please fill in all required fields.';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        // Handle file upload
        $photo_path = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $photo_path = 'complaint_' . time() . '_' . random_int(1000, 9999) . '.' . $file_extension;
                $full_path = $upload_dir . $photo_path;
                
                if (!move_uploaded_file($_FILES['photo']['tmp_name'], $full_path)) {
                    $error = 'Failed to upload photo.';
                    $photo_path = null;
                }
            } else {
                $error = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
            }
        }
        
        if (!$error) {
            $query = "INSERT INTO complaints (user_id, title, description, category, priority, photo, status) 
                     VALUES (:user_id, :title, :description, :category, :priority, :photo, 'pending')";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':priority', $priority);
            $stmt->bindParam(':photo', $photo_path);
            
            if ($stmt->execute()) {
                $complaint_id = $db->lastInsertId();
                
                // Create notification for admins
                $admin_query = "SELECT id FROM users WHERE role = 'admin'";
                $admin_stmt = $db->prepare($admin_query);
                $admin_stmt->execute();
                $admins = $admin_stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($admins as $admin) {
                    $notif_query = "INSERT INTO notifications (user_id, complaint_id, title, message) 
                                   VALUES (:user_id, :complaint_id, :title, :message)";
                    $notif_stmt = $db->prepare($notif_query);
                    $notif_stmt->bindParam(':user_id', $admin['id']);
                    $notif_stmt->bindParam(':complaint_id', $complaint_id);
                    $notif_title = 'New Complaint Submitted';
                    $notif_message = $_SESSION['full_name'] . ' (Staff) submitted a new ' . $category . ' complaint: ' . $title;
                    $notif_stmt->bindParam(':title', $notif_title);
                    $notif_stmt->bindParam(':message', $notif_message);
                    $notif_stmt->execute();
                }
                
                $success = 'Complaint submitted successfully! You will be notified of any updates.';
                // Clear form data
                $_POST = [];
            } else {
                $error = 'Failed to submit complaint. Please try again.';
            }
        }
    }
}

$page_title = 'Submit Complaint - Staff Portal';
include '../includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="fade-in">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Submit New Complaint</h1>
            <p class="text-gray-600">Please provide detailed information about your issue to help us resolve it quickly.</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $success; ?>
                    <div class="mt-2">
                        <a href="<?php echo $base_url; ?>/staff/complaints.php" class="text-green-800 hover:text-green-900 font-medium">
                            View your complaints <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select id="category" name="category" required
                                class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="">Select category</option>
                            <option value="maintenance" <?php echo ($_POST['category'] ?? '') === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                            <option value="academic" <?php echo ($_POST['category'] ?? '') === 'academic' ? 'selected' : ''; ?>>Academic</option>
                            <option value="facility" <?php echo ($_POST['category'] ?? '') === 'facility' ? 'selected' : ''; ?>>Facility</option>
                            <option value="other" <?php echo ($_POST['category'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                            Priority
                        </label>
                        <select id="priority" name="priority"
                                class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="low" <?php echo ($_POST['priority'] ?? 'medium') === 'low' ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo ($_POST['priority'] ?? 'medium') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo ($_POST['priority'] ?? 'medium') === 'high' ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required
                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                           placeholder="Brief description of the issue"
                           class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" required rows="6"
                              placeholder="Please provide detailed information about the issue, including location, time, and any other relevant details..."
                              class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    <p class="text-sm text-gray-500 mt-1">Be as specific as possible to help us resolve your issue quickly.</p>
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                        Photo (Optional)
                    </label>
                    <input type="file" id="photo" name="photo" accept="image/*"
                           class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <p class="text-sm text-gray-500 mt-1">Upload a photo to help illustrate the issue. JPG, PNG, GIF formats allowed (max 5MB).</p>
                </div>

                <div class="flex items-center justify-between pt-6 border-t">
                    <a href="<?php echo $base_url; ?>/staff/dashboard.php" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Complaint
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>