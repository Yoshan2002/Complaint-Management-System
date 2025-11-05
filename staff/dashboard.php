<?php
require_once '../config/config.php';
requireRole('staff');

// compute project-aware base URL so links stay inside the project
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$database = new Database();
$db = $database->getConnection();

// Get user statistics
$user_id = $_SESSION['user_id'];
$stats = [
    'total' => $db->query("SELECT COUNT(*) FROM complaints WHERE user_id = $user_id")->fetchColumn(),
    'pending' => $db->query("SELECT COUNT(*) FROM complaints WHERE user_id = $user_id AND status = 'pending'")->fetchColumn(),
    'in_progress' => $db->query("SELECT COUNT(*) FROM complaints WHERE user_id = $user_id AND status = 'in_progress'")->fetchColumn(),
    'resolved' => $db->query("SELECT COUNT(*) FROM complaints WHERE user_id = $user_id AND status = 'resolved'")->fetchColumn()
];

// Get recent complaints
$query = "SELECT * FROM complaints WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$recent_complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get notifications
$query = "SELECT * FROM notifications WHERE user_id = :user_id AND is_read = FALSE ORDER BY created_at DESC LIMIT 3";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Staff Dashboard';
include '../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="fade-in">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
            <p class="text-gray-600">Here's an overview of your complaints and activities.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg hover-scale">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Complaints</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total']; ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-clipboard-list text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg hover-scale">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600"><?php echo $stats['pending']; ?></p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg hover-scale">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">In Progress</p>
                        <p class="text-2xl font-bold text-blue-600"><?php echo $stats['in_progress']; ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-cog text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg hover-scale">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Resolved</p>
                        <p class="text-2xl font-bold text-green-600"><?php echo $stats['resolved']; ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Complaints -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Recent Complaints</h2>
                        <a href="<?php echo $base_url; ?>/staff/complaints.php" class="text-blue-600 hover:text-blue-700 font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <?php if (empty($recent_complaints)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-600 mb-4">No complaints yet</p>
                            <a href="<?php echo $base_url; ?>/staff/submit.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Submit Your First Complaint
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recent_complaints as $complaint): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900 mb-1">
                                                <a href="<?php echo $base_url; ?>/staff/complaint.php?id=<?php echo $complaint['id']; ?>" class="hover:text-blue-600">
                                                    <?php echo htmlspecialchars($complaint['title']); ?>
                                                </a>
                                            </h3>
                                            <p class="text-gray-600 text-sm mb-2">
                                                <?php echo substr(htmlspecialchars($complaint['description']), 0, 100) . '...'; ?>
                                            </p>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span><i class="fas fa-calendar mr-1"></i><?php echo formatDate($complaint['created_at']); ?></span>
                                                <span class="capitalize"><i class="fas fa-tag mr-1"></i><?php echo $complaint['category']; ?></span>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo getStatusBadge($complaint['status']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions & Notifications -->
            <div class="space-y-8">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h2>
                    <div class="space-y-4">
                        <a href="<?php echo $base_url; ?>/staff/submit.php" class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Submit New Complaint
                        </a>
                        <a href="<?php echo $base_url; ?>/staff/complaints.php" class="block w-full bg-gray-100 text-gray-700 text-center py-3 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-list mr-2"></i>View All Complaints
                        </a>
                        <a href="<?php echo $base_url; ?>/staff/profile.php" class="block w-full bg-gray-100 text-gray-700 text-center py-3 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-user mr-2"></i>Edit Profile
                        </a>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Notifications</h2>
                        <a href="<?php echo $base_url; ?>/staff/notifications.php" class="text-blue-600 hover:text-blue-700 font-medium">
                            View All
                        </a>
                    </div>

                    <?php if (empty($notifications)): ?>
                        <p class="text-gray-600 text-center py-4">No new notifications</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <h4 class="font-medium text-gray-900 text-sm"><?php echo htmlspecialchars($notification['title']); ?></h4>
                                    <p class="text-gray-600 text-xs mt-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <p class="text-gray-500 text-xs mt-2"><?php echo formatDate($notification['created_at']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>