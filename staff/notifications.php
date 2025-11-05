<?php
require_once '../config/config.php';
requireRole('staff');

// compute project-aware base URL so links/redirects stay inside the project
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$database = new Database();
$db = $database->getConnection();

// Mark notification as read if requested
if (isset($_GET['mark_read']) && $_GET['mark_read']) {
    $notif_id = $_GET['mark_read'];
    $query = "UPDATE notifications SET is_read = TRUE WHERE id = :id AND user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $notif_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    header("Location: {$base_url}/staff/notifications.php");
    exit;
}

// Mark all as read if requested
if (isset($_POST['mark_all_read'])) {
    $query = "UPDATE notifications SET is_read = TRUE WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    header("Location: {$base_url}/staff/notifications.php");
    exit;
}

// Get notifications
$query = "SELECT n.*, c.title as complaint_title 
          FROM notifications n 
          LEFT JOIN complaints c ON n.complaint_id = c.id 
          WHERE n.user_id = :user_id 
          ORDER BY n.created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unread count (parameterized)
$count_stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = FALSE");
$count_stmt->bindParam(':user_id', $_SESSION['user_id']);
$count_stmt->execute();
$unread_count = (int) $count_stmt->fetchColumn();

$page_title = 'Notifications - Staff Portal';
include '../includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="fade-in">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Notifications</h1>
                <p class="text-gray-600">
                    <?php if ($unread_count > 0): ?>
                        You have <?php echo $unread_count; ?> unread notification<?php echo $unread_count > 1 ? 's' : ''; ?>
                    <?php else: ?>
                        All caught up! No unread notifications.
                    <?php endif; ?>
                </p>
            </div>
            
            <?php if ($unread_count > 0): ?>
                <form method="POST" class="inline">
                    <button type="submit" name="mark_all_read" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-check-double mr-2"></i>Mark All Read
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            <?php if (empty($notifications)): ?>
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <i class="fas fa-bell text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Notifications</h3>
                    <p class="text-gray-600">You don't have any notifications yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="bg-white rounded-xl shadow-lg p-6 <?php echo !$notification['is_read'] ? 'border-l-4 border-blue-500' : ''; ?>">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($notification['title']); ?>
                                    </h3>
                                    <?php if (!$notification['is_read']): ?>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">New</span>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="text-gray-600 mb-3">
                                    <?php echo htmlspecialchars($notification['message']); ?>
                                </p>
                                
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        <?php echo formatDate($notification['created_at']); ?>
                                    </span>
                                    
                                    <?php if ($notification['complaint_id']): ?>
                                        <span>
                                            <i class="fas fa-link mr-1"></i>
                                            Related to: <?php echo htmlspecialchars($notification['complaint_title']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2 ml-4">
                                <?php if ($notification['complaint_id']): ?>
                                    <a href="<?php echo $base_url; ?>/staff/complaint.php?id=<?php echo $notification['complaint_id']; ?>" 
                                       class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        <i class="fas fa-eye mr-1"></i>View Complaint
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!$notification['is_read']): ?>
                                    <a href="<?php echo $base_url; ?>/staff/notifications.php?mark_read=<?php echo $notification['id']; ?>" 
                                       class="text-green-600 hover:text-green-700 text-sm font-medium">
                                        <i class="fas fa-check mr-1"></i>Mark Read
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>