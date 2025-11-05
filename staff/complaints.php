<?php
require_once '../config/config.php';
requireRole('staff');

// compute project-aware base URL so links stay inside the project
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'];

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';

// Build query with filters
$where_conditions = ["user_id = :user_id"];
$params = [':user_id' => $user_id];

if ($status_filter) {
    $where_conditions[] = "status = :status";
    $params[':status'] = $status_filter;
}

if ($category_filter) {
    $where_conditions[] = "category = :category";
    $params[':category'] = $category_filter;
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

$query = "SELECT * FROM complaints $where_clause ORDER BY created_at DESC";
$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'My Complaints - Staff Portal';
include '../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="fade-in">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">My Complaints</h1>
                <p class="text-gray-600">Track and manage your submitted complaints</p>
            </div>
            <a href="<?php echo $base_url; ?>/staff/submit.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Submit New Complaint
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="in_progress" <?php echo $status_filter === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="resolved" <?php echo $status_filter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                        <option value="withdrawn" <?php echo $status_filter === 'withdrawn' ? 'selected' : ''; ?>>Withdrawn</option>
                    </select>
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="category" name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        <option value="maintenance" <?php echo $category_filter === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                        <option value="academic" <?php echo $category_filter === 'academic' ? 'selected' : ''; ?>>Academic</option>
                        <option value="facility" <?php echo $category_filter === 'facility' ? 'selected' : ''; ?>>Facility</option>
                        <option value="other" <?php echo $category_filter === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Complaints List -->
        <?php if (empty($complaints)): ?>
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Complaints Found</h3>
                <p class="text-gray-600 mb-6">
                    <?php if ($status_filter || $category_filter): ?>
                        Try adjusting your filters or submit your first complaint.
                    <?php else: ?>
                        You haven't submitted any complaints yet.
                    <?php endif; ?>
                </p>
                <a href="submit.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Submit Your First Complaint
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-6">
                <?php foreach ($complaints as $complaint): ?>
                    <div class="bg-white rounded-xl shadow-lg p-6 hover-scale">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                    <a href="complaint.php?id=<?php echo $complaint['id']; ?>" class="hover:text-blue-600 transition-colors">
                                        <?php echo htmlspecialchars($complaint['title']); ?>
                                    </a>
                                </h3>
                                <p class="text-gray-600 mb-3">
                                    <?php echo substr(htmlspecialchars($complaint['description']), 0, 200) . '...'; ?>
                                </p>
                            </div>
                            <div class="ml-6 text-right">
                                <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo getStatusBadge($complaint['status']); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-6 text-sm text-gray-500">
                                <span>
                                    <i class="fas fa-calendar mr-1"></i>
                                    <?php echo formatDate($complaint['created_at']); ?>
                                </span>
                                <span class="capitalize">
                                    <i class="fas fa-tag mr-1"></i>
                                    <?php echo $complaint['category']; ?>
                                </span>
                                <span class="capitalize">
                                    <i class="fas fa-flag mr-1"></i>
                                    <?php echo $complaint['priority']; ?> Priority
                                </span>
                                <?php if ($complaint['photo']): ?>
                                    <span>
                                        <i class="fas fa-image mr-1"></i>
                                        Photo Attached
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-center space-x-3">
                                <a href="<?php echo $base_url; ?>/staff/complaint.php?id=<?php echo $complaint['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-700 font-medium">
                                    <i class="fas fa-eye mr-1"></i>View Details
                                </a>
                                
                                <?php if ($complaint['status'] === 'pending'): ?>
                                    <a href="<?php echo $base_url; ?>/staff/edit_complaint.php?id=<?php echo $complaint['id']; ?>" 
                                       class="text-green-600 hover:text-green-700 font-medium">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>