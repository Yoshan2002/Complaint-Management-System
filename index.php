<?php
require_once 'config/config.php';

// compute project-aware base URL so links stay inside the project
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$page_title = 'Home - University Complaint System';
include 'includes/header.php';
?>

<div class="fade-in">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    University Complaint System
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100">
                    Your voice matters. Submit, track, and resolve campus issues efficiently.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo $base_url; ?>/<?php echo getUserRole(); ?>/dashboard.php" 
                           class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all hover-scale">
                            Go to Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $base_url; ?>/auth/register.php" 
                           class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all hover-scale">
                            Register Now
                        </a>
                        <a href="<?php echo $base_url; ?>/auth/login.php" 
                           class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-all hover-scale">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600">Simple steps to get your issues resolved</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-8 bg-white rounded-xl shadow-lg hover-scale">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-edit text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Submit Complaint</h3>
                    <p class="text-gray-600">
                        Easily submit your complaint with detailed description and photos if needed.
                    </p>
                </div>
                
                <div class="text-center p-8 bg-white rounded-xl shadow-lg hover-scale">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-cogs text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Track Progress</h3>
                    <p class="text-gray-600">
                        Monitor your complaint status in real-time and receive updates.
                    </p>
                </div>
                
                <div class="text-center p-8 bg-white rounded-xl shadow-lg hover-scale">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-check-circle text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Get Resolution</h3>
                    <p class="text-gray-600">
                        Receive timely resolution and feedback on your complaint.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <?php
                $database = new Database();
                $db = $database->getConnection();
                
                // Get statistics
                $total_complaints = $db->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
                $resolved_complaints = $db->query("SELECT COUNT(*) FROM complaints WHERE status = 'resolved'")->fetchColumn();
                $active_users = $db->query("SELECT COUNT(*) FROM users WHERE status = 'approved'")->fetchColumn();
                $resolution_rate = $total_complaints > 0 ? round(($resolved_complaints / $total_complaints) * 100) : 0;
                ?>
                
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2"><?php echo $total_complaints; ?></div>
                    <div class="text-gray-700">Total Complaints</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2"><?php echo $resolved_complaints; ?></div>
                    <div class="text-gray-700">Resolved</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2"><?php echo $active_users; ?></div>
                    <div class="text-gray-700">Active Users</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600 mb-2"><?php echo $resolution_rate; ?>%</div>
                    <div class="text-gray-700">Success Rate</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Ready to Make a Difference?
            </h2>
            <p class="text-xl text-gray-600 mb-8">
                Join hundreds of students and staff in making our university better.
            </p>
            <?php if (!isLoggedIn()): ?>
                <a href="<?php echo $base_url; ?>/auth/register.php" 
                   class="bg-blue-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-blue-700 transition-all hover-scale">
                    Get Started Today
                </a>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>