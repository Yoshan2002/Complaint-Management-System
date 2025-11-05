<?php
require_once 'config/config.php';

// compute project-aware base URL so links stay inside the project
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$success = '';
$error = '';

if ($_POST) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Here you would typically send an email using PHPMailer
        // For now, we'll just show a success message
        $success = 'Thank you for your message! We will get back to you soon.';
        $_POST = []; // Clear form
    }
}

$page_title = 'Contact Us - University Complaint System';
include 'includes/header.php';
?>

<div class="fade-in">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Contact Us</h1>
            <p class="text-xl text-blue-100">Get in touch with our support team</p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h2>
                    
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
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" required
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                       class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" required
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                       class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <select id="subject" name="subject" required
                                    class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <option value="">Select a subject</option>
                                <option value="Technical Support" <?php echo ($_POST['subject'] ?? '') === 'Technical Support' ? 'selected' : ''; ?>>Technical Support</option>
                                <option value="Account Issues" <?php echo ($_POST['subject'] ?? '') === 'Account Issues' ? 'selected' : ''; ?>>Account Issues</option>
                                <option value="General Inquiry" <?php echo ($_POST['subject'] ?? '') === 'General Inquiry' ? 'selected' : ''; ?>>General Inquiry</option>
                                <option value="Feedback" <?php echo ($_POST['subject'] ?? '') === 'Feedback' ? 'selected' : ''; ?>>Feedback</option>
                                <option value="Other" <?php echo ($_POST['subject'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea id="message" name="message" required rows="6"
                                      placeholder="Please describe your inquiry or issue in detail..."
                                      class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-paper-plane mr-2"></i>Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="space-y-8">
                    <!-- Contact Details -->
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Contact Information</h2>
                        
                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <div class="bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-envelope text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Email</h3>
                                    <p class="text-gray-600">support@university.edu</p>
                                    <p class="text-sm text-gray-500">We typically respond within 24 hours</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="bg-green-100 p-3 rounded-full">
                                    <i class="fas fa-phone text-green-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Phone</h3>
                                    <p class="text-gray-600">+1 (555) 123-4567</p>
                                    <p class="text-sm text-gray-500">Monday - Friday, 8:00 AM - 5:00 PM</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="bg-purple-100 p-3 rounded-full">
                                    <i class="fas fa-map-marker-alt text-purple-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Office Location</h3>
                                    <p class="text-gray-600">
                                        Administration Building, Room 205<br>
                                        University Campus<br>
                                        City, State 12345
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="bg-orange-100 p-3 rounded-full">
                                    <i class="fas fa-clock text-orange-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Office Hours</h3>
                                    <p class="text-gray-600">
                                        Monday - Friday: 8:00 AM - 5:00 PM<br>
                                        Saturday: 9:00 AM - 1:00 PM<br>
                                        Sunday: Closed
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="bg-red-50 border border-red-200 rounded-xl p-8">
                        <h3 class="text-xl font-bold text-red-900 mb-4">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Emergency Issues
                        </h3>
                        <p class="text-red-800 mb-4">
                            For urgent safety or security issues that require immediate attention, please contact:
                        </p>
                        <div class="space-y-2">
                            <p class="text-red-900 font-semibold">
                                <i class="fas fa-phone mr-2"></i>Emergency Hotline: +1 (555) 911-HELP
                            </p>
                            <p class="text-red-800 text-sm">Available 24/7 for emergency situations</p>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Quick Links</h3>
                        <div class="space-y-3">
                            <a href="<?php echo $base_url; ?>/faq.php" class="block text-blue-600 hover:text-blue-700 transition-colors">
                                <i class="fas fa-question-circle mr-2"></i>Frequently Asked Questions
                            </a>
                            <a href="<?php echo $base_url; ?>/about.php" class="block text-blue-600 hover:text-blue-700 transition-colors">
                                <i class="fas fa-info-circle mr-2"></i>About the System
                            </a>
                            <?php if (!isLoggedIn()): ?>
                                <a href="<?php echo $base_url; ?>/auth/register.php" class="block text-blue-600 hover:text-blue-700 transition-colors">
                                    <i class="fas fa-user-plus mr-2"></i>Create Account
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>