<?php
require_once '../config/config.php';
requireRole('staff');

$database = new Database();
$db = $database->getConnection();

$complaint_id = $_GET['id'] ?? null;

// Verify complaint exists and belongs to user
if (!$complaint_id) {
    header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/staff/complaints.php');
    exit;
}

$query = "SELECT c.*, u.full_name, u.email 
          FROM complaints c 
          JOIN users u ON c.user_id = u.id 
          WHERE c.id = :id AND c.user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $complaint_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$complaint = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$complaint) {
    header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/staff/complaints.php');
    exit;
}

// Get any updates/notes from admin
$updates_query = "SELECT cu.*, a.full_name as admin_name 
                  FROM complaint_updates cu 
                  LEFT JOIN users a ON cu.admin_id = a.id 
                  WHERE cu.complaint_id = :id 
                  ORDER BY cu.created_at DESC";
$updates_stmt = $db->prepare($updates_query);
$updates_stmt->bindParam(':id', $complaint_id);
$updates_stmt->execute();
$updates = $updates_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Complaint Details - Staff Portal';
include '../includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="fade-in">
        <!-- Back button -->
        <div class="mb-6">
            <a href="<?php echo $root; ?>/staff/complaints.php" class="text-blue-600 hover:text-blue-700 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to My Complaints
            </a>
        </div>

        <!-- Complaint Header Card -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <?php echo htmlspecialchars($complaint['title']); ?>
                    </h1>
                    <p class="text-gray-600">
                        Submitted by <strong><?php echo htmlspecialchars($complaint['full_name']); ?></strong> 
                        on <strong><?php echo formatDate($complaint['created_at']); ?></strong>
                    </p>
                </div>
                <div class="text-right ml-6">
                    <span class="px-4 py-2 rounded-full text-sm font-medium <?php echo getStatusBadge($complaint['status']); ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                    </span>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600">Category</p>
                    <p class="font-semibold text-gray-900 capitalize"><?php echo $complaint['category']; ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Priority</p>
                    <p class="font-semibold text-gray-900 capitalize"><?php echo $complaint['priority']; ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Submitted</p>
                    <p class="font-semibold text-gray-900"><?php echo formatDate($complaint['created_at']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Last Updated</p>
                    <p class="font-semibold text-gray-900"><?php echo formatDate($complaint['updated_at']); ?></p>
                </div>
            </div>
        </div>

        <!-- Complaint Description -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Description</h2>
            <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">
                <?php echo htmlspecialchars($complaint['description']); ?>
            </p>

            <!-- Attached Photo -->
            <?php if ($complaint['photo']): ?>
                <div class="mt-6">
                    <h3 class="text-md font-semibold text-gray-900 mb-3">Attached Photo</h3>
                    <div class="max-w-md">
                        <img src="<?php echo $root; ?>/<?php echo htmlspecialchars($complaint['photo']); ?>" 
                             alt="Complaint photo" 
                             class="w-full rounded-lg shadow-md border border-gray-200">
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Admin Updates/History -->
        <?php if (!empty($updates)): ?>
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-history mr-2"></i>Status Updates
                </h2>

                <div class="space-y-4">
                    <?php foreach ($updates as $update): ?>
                        <div class="border-l-4 border-blue-500 pl-4 py-2">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        Status changed: 
                                        <span class="capitalize text-red-600">
                                            <?php echo str_replace('_', ' ', $update['old_status']); ?>
                                        </span>
                                        → 
                                        <span class="capitalize text-green-600">
                                            <?php echo str_replace('_', ' ', $update['new_status']); ?>
                                        </span>
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    <?php echo formatDate($update['created_at']); ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">
                                Updated by: <strong><?php echo htmlspecialchars($update['admin_name'] ?? 'System'); ?></strong>
                            </p>
                            <?php if ($update['notes']): ?>
                                <div class="mt-2 p-3 bg-gray-50 rounded text-sm text-gray-700">
                                    <strong>Note:</strong> <?php echo htmlspecialchars($update['notes']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Edit Option for Pending Complaints -->
        <?php if ($complaint['status'] === 'pending'): ?>
            <div class="flex gap-4">
                <a href="<?php echo $root; ?>/staff/edit_complaint.php?id=<?php echo $complaint['id']; ?>" 
                   class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Complaint
                </a>
                <a href="<?php echo $root; ?>/staff/complaints.php" 
                   class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        <?php else: ?>
            <a href="<?php echo $root; ?>/staff/complaints.php" 
               class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Complaints
            </a>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
