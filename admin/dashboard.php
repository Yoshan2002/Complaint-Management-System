<?php
require_once '../config/config.php';
requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get statistics
$stats = [
    'total_complaints' => $db->query("SELECT COUNT(*) FROM complaints")->fetchColumn(),
    'pending_complaints' => $db->query("SELECT COUNT(*) FROM complaints WHERE status = 'pending'")->fetchColumn(),
    'in_progress_complaints' => $db->query("SELECT COUNT(*) FROM complaints WHERE status = 'in_progress'")->fetchColumn(),
    'resolved_complaints' => $db->query("SELECT COUNT(*) FROM complaints WHERE status = 'resolved'")->fetchColumn(),
    'total_users' => $db->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn(),
    'pending_users' => $db->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn()
];

// Get recent complaints
$query = "SELECT c.*, u.full_name, u.email, u.role 
          FROM complaints c 
          JOIN users u ON c.user_id = u.id 
          ORDER BY c.created_at DESC 
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$recent_complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get pending users
$query = "SELECT * FROM users WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$pending_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Admin Dashboard';
include '../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="fade-in">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Admin Dashboard</h1>
            <p class="text-gray-600">Monitor and manage the complaint system.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg hover-scale">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Complaints</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_complaints']; ?></p>
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
                        <p class="text-2xl font-bold text-yellow-600"><?php echo $stats['pending_complaints']; ?></p>
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
                        <p class="text-2xl font-bold text-blue-600"><?php echo $stats['in_progress_complaints']; ?></p>
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
                        <p class="text-2xl font-bold text-green-600"><?php echo $stats['resolved_complaints']; ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg hover-scale">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-purple-600"><?php echo $stats['total_users']; ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg hover-scale">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Users</p>
                        <p class="text-2xl font-bold text-orange-600"><?php echo $stats['pending_users']; ?></p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-user-clock text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Complaints -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Recent Complaints</h2>
                    <a href="complaints.php" class="text-blue-600 hover:text-blue-700 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <?php if (empty($recent_complaints)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">No complaints yet</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recent_complaints as $complaint): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 mb-1">
                                            <a href="complaints.php?focus=<?php echo $complaint['id']; ?>" class="hover:text-blue-600">
                                                <?php echo htmlspecialchars($complaint['title']); ?>
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">
                                            by <?php echo htmlspecialchars($complaint['full_name']); ?> 
                                            <span class="text-blue-600">[<?php echo ucfirst($complaint['role']); ?>]</span>
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

            <!-- Pending Users -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Pending User Approvals</h2>
                    <a href="users.php" class="text-blue-600 hover:text-blue-700 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <?php if (empty($pending_users)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-user-check text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">No pending approvals</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($pending_users as $user): ?>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
                                        <p class="text-sm text-blue-600 capitalize"><?php echo $user['role']; ?></p>
                                        <p class="text-xs text-gray-500 mt-1"><?php echo formatDate($user['created_at']); ?></p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <form method="POST" action="approve_user.php" class="inline">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition-colors">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="approve_user.php" class="inline">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>