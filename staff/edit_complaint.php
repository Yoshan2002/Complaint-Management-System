<?php
require_once '../config/config.php';
requireRole('staff');

$database = new Database();
$db = $database->getConnection();

$complaint_id = $_GET['id'] ?? null;

if (!$complaint_id) {
    header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/staff/complaints.php');
    exit;
}

// Get complaint details (verify it belongs to user and is pending)
$query = "SELECT * FROM complaints WHERE id = :id AND user_id = :user_id AND status = 'pending'";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $complaint_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$complaint = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$complaint) {
    header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/staff/complaints.php');
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_POST && isset($_POST['update_complaint'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $priority = trim($_POST['priority'] ?? '');

    // Validation
    if (empty($title)) {
        $error = 'Title is required.';
    } elseif (empty($description)) {
        $error = 'Description is required.';
    } elseif (empty($category)) {
        $error = 'Category is required.';
    } elseif (empty($priority)) {
        $error = 'Priority is required.';
    } else {
        // Update complaint
        $update_query = "UPDATE complaints 
                         SET title = :title, description = :description, category = :category, 
                             priority = :priority, updated_at = CURRENT_TIMESTAMP 
                         WHERE id = :id";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->bindParam(':title', $title);
        $update_stmt->bindParam(':description', $description);
        $update_stmt->bindParam(':category', $category);
        $update_stmt->bindParam(':priority', $priority);
        $update_stmt->bindParam(':id', $complaint_id);

        if ($update_stmt->execute()) {
            $success = 'Complaint updated successfully!';
            // Redirect after 1 second
            header('Refresh: 1; url=/staff/complaints.php');
        } else {
            $error = 'Failed to update complaint. Please try again.';
        }
    }
}

$page_title = 'Edit Complaint - Staff Portal';
include '../includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="fade-in">
        <!-- Back button -->
        <div class="mb-6">
            <a href="/staff/complaints.php" class="text-blue-600 hover:text-blue-700 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to My Complaints
            </a>
        </div>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Complaint</h1>
            <p class="text-gray-600">
                You can only edit pending complaints. Once your complaint is being reviewed, it will be locked for editing.
            </p>
        </div>

        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <!-- Edit Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Complaint Title <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="title" name="title" maxlength="255"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="<?php echo htmlspecialchars($complaint['title']); ?>" required>
                    <p class="text-sm text-gray-500 mt-1">Be specific and descriptive</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Detailed Description <span class="text-red-600">*</span>
                    </label>
                    <textarea id="description" name="description" rows="8"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              required><?php echo htmlspecialchars($complaint['description']); ?></textarea>
                    <p class="text-sm text-gray-500 mt-1">Provide all relevant details about your complaint</p>
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-600">*</span>
                    </label>
                    <select id="category" name="category" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <option value="">-- Select Category --</option>
                        <option value="academic" <?php echo $complaint['category'] === 'academic' ? 'selected' : ''; ?>>Academic</option>
                        <option value="facility" <?php echo $complaint['category'] === 'facility' ? 'selected' : ''; ?>>Facility</option>
                        <option value="maintenance" <?php echo $complaint['category'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                        <option value="other" <?php echo $complaint['category'] === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priority Level <span class="text-red-600">*</span>
                    </label>
                    <select id="priority" name="priority" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <option value="">-- Select Priority --</option>
                        <option value="low" <?php echo $complaint['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                        <option value="medium" <?php echo $complaint['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="high" <?php echo $complaint['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                        <option value="urgent" <?php echo $complaint['priority'] === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                    </select>
                </div>

                <!-- Current Status Info -->
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-900">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Current Status:</strong> 
                        <span class="capitalize"><?php echo $complaint['status']; ?></span>
                    </p>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" name="update_complaint" value="1"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                    <a href="/staff/complaints.php" 
                       class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
