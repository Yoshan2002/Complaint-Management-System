<?php
require_once '../config/config.php';
requireAdmin();

// compute project-aware base URL so links stay inside the project
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$database = new Database();
$db = $database->getConnection();

// Handle status updates
if ($_POST && isset($_POST['update_status'])) {
    $complaint_id = $_POST['complaint_id'];
    $new_status = $_POST['status'];
    $notes = trim($_POST['notes']);
    $admin_id = $_SESSION['user_id'];
    
    // Get current status
    $current_query = "SELECT status, user_id FROM complaints WHERE id = :id";
    $current_stmt = $db->prepare($current_query);
    $current_stmt->bindParam(':id', $complaint_id);
    $current_stmt->execute();
    $current_complaint = $current_stmt->fetch(PDO::FETCH_ASSOC);
    $old_status = $current_complaint['status'];
    
    // Update complaint status
    $update_query = "UPDATE complaints SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindParam(':status', $new_status);
    $update_stmt->bindParam(':id', $complaint_id);
    
    if ($update_stmt->execute()) {
        // Log the status change
        $log_query = "INSERT INTO complaint_updates (complaint_id, admin_id, old_status, new_status, notes) 
                     VALUES (:complaint_id, :admin_id, :old_status, :new_status, :notes)";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->bindParam(':complaint_id', $complaint_id);
        $log_stmt->bindParam(':admin_id', $admin_id);
        $log_stmt->bindParam(':old_status', $old_status);
        $log_stmt->bindParam(':new_status', $new_status);
        $log_stmt->bindParam(':notes', $notes);
        $log_stmt->execute();
        
        // Create notification for user
        $notif_query = "INSERT INTO notifications (user_id, complaint_id, title, message) 
                       VALUES (:user_id, :complaint_id, :title, :message)";
        $notif_stmt = $db->prepare($notif_query);
        $notif_stmt->bindParam(':user_id', $current_complaint['user_id']);
        $notif_stmt->bindParam(':complaint_id', $complaint_id);
        $title = 'Complaint Status Updated';
        $message = 'Your complaint status has been updated to: ' . ucfirst(str_replace('_', ' ', $new_status));
        if ($notes) {
            $message .= '. Notes: ' . $notes;
        }
        $notif_stmt->bindParam(':title', $title);
        $notif_stmt->bindParam(':message', $message);
        $notif_stmt->execute();
        
        $success = 'Complaint status updated successfully!';
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';
$priority_filter = $_GET['priority'] ?? '';
$search = $_GET['search'] ?? '';

// Build query with filters
$where_conditions = [];
$params = [];

if ($status_filter) {
    $where_conditions[] = "c.status = :status";
    $params[':status'] = $status_filter;
}

if ($category_filter) {
    $where_conditions[] = "c.category = :category";
    $params[':category'] = $category_filter;
}

if ($priority_filter) {
    $where_conditions[] = "c.priority = :priority";
    $params[':priority'] = $priority_filter;
}

if ($search) {
    $where_conditions[] = "(c.title LIKE :search OR c.description LIKE :search OR u.full_name LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$query = "SELECT c.*, u.full_name, u.email, u.role 
          FROM complaints c 
          JOIN users u ON c.user_id = u.id 
          $where_clause
          ORDER BY c.created_at DESC";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Manage Complaints - Admin Portal';
include '../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="fade-in">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Complaints</h1>
            <p class="text-gray-600">Review and update complaint statuses</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="search" name="search" 
                           value="<?php echo htmlspecialchars($search); ?>"
                           placeholder="Title, description, or user..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

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

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select id="priority" name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Priorities</option>
                        <option value="low" <?php echo $priority_filter === 'low' ? 'selected' : ''; ?>>Low</option>
                        <option value="medium" <?php echo $priority_filter === 'medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="high" <?php echo $priority_filter === 'high' ? 'selected' : ''; ?>>High</option>
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
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <?php if (empty($complaints)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-600 text-lg">No complaints found</p>
                    <p class="text-gray-500">Try adjusting your filters</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Complaint</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($complaints as $complaint): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($complaint['title']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo substr(htmlspecialchars($complaint['description']), 0, 100) . '...'; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($complaint['full_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($complaint['email']); ?></div>
                                        <div class="text-xs text-blue-600 capitalize"><?php echo $complaint['role']; ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="capitalize text-sm text-gray-900"><?php echo $complaint['category']; ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            <?php 
                                            echo $complaint['priority'] === 'high' ? 'bg-red-100 text-red-800' : 
                                                ($complaint['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'); 
                                            ?>">
                                            <?php echo ucfirst($complaint['priority']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo getStatusBadge($complaint['status']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php echo formatDate($complaint['created_at']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <button onclick="openStatusModal(<?php echo $complaint['id']; ?>, '<?php echo $complaint['status']; ?>')" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i> Update
                                        </button>
                                        <a href="complaint.php?id=<?php echo $complaint['id']; ?>" 
                                           class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Update Complaint Status</h3>
                <form method="POST">
                    <input type="hidden" id="modal_complaint_id" name="complaint_id">
                    <input type="hidden" name="update_status" value="1">
                    
                    <div class="mb-4">
                        <label for="modal_status" class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                        <select id="modal_status" name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="withdrawn">Withdrawn</option>
                        </select>
                    </div>
                    
                    <div class="mb-6">
                        <label for="modal_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea id="modal_notes" name="notes" rows="3" 
                                  placeholder="Add any notes about this status change..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeStatusModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openStatusModal(complaintId, currentStatus) {
    document.getElementById('modal_complaint_id').value = complaintId;
    document.getElementById('modal_status').value = currentStatus;
    document.getElementById('modal_notes').value = '';
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>