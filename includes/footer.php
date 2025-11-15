<?php
// Use global BASE_PATH if defined, else fall back to request-derived path
$root = defined('BASE_PATH') ? BASE_PATH : rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>
<!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-university text-2xl text-blue-400"></i>
                        <span class="font-bold text-xl">University CS</span>
                    </div>
                    <p class="text-gray-300">
                        Making campus life better through efficient complaint management and resolution.
                    </p>
                </div>
                
                <div>
                    <h3 class="font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="<?php echo $root; ?>/" class="hover:text-white transition-colors">Home</a></li>
                        <li><a href="<?php echo $root; ?>/about.php" class="hover:text-white transition-colors">About</a></li>
                        <li><a href="<?php echo $root; ?>/faq.php" class="hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="<?php echo $root; ?>/contact.php" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold mb-4">Services</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li>Maintenance Requests</li>
                        <li>Academic Support</li>
                        <li>Facility Issues</li>
                        <li>General Complaints</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold mb-4">Contact Info</h3>
                    <div class="space-y-2 text-gray-300">
                        <p><i class="fas fa-envelope mr-2"></i> support@university.edu</p>
                        <p><i class="fas fa-phone mr-2"></i> +1 (555) 123-4567</p>
                        <p><i class="fas fa-map-marker-alt mr-2"></i> University Campus</p>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; 2025 University Complaint System. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>