<?php
// Compute base URL for the project (e.g. "/yoshan2")
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'University Complaint System'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .hover-scale { transition: transform 0.2s; }
        .hover-scale:hover { transform: scale(1.02); }
        .glass { backdrop-filter: blur(10px); background-color: rgba(255, 255, 255, 0.9); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="<?php echo $base_url; ?>/" class="flex items-center space-x-2">
                        <i class="fas fa-university text-blue-600 text-2xl"></i>
                        <span class="font-bold text-xl text-gray-900">UCS</span>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-6">
                    <?php if (isLoggedIn()): ?>
                        <?php if (getUserRole() === 'admin'): ?>
                            <a href="<?php echo $base_url; ?>/admin/dashboard.php" class="text-gray-700 hover:text-blue-600 transition-colors">Dashboard</a>
                            <a href="<?php echo $base_url; ?>/admin/complaints.php" class="text-gray-700 hover:text-blue-600 transition-colors">Complaints</a>
                            <a href="<?php echo $base_url; ?>/admin/users.php" class="text-gray-700 hover:text-blue-600 transition-colors">Users</a>
                        <?php else: ?>
                            <a href="<?php echo $base_url; ?>/<?php echo getUserRole(); ?>/dashboard.php" class="text-gray-700 hover:text-blue-600 transition-colors">Dashboard</a>
                            <a href="<?php echo $base_url; ?>/<?php echo getUserRole(); ?>/complaints.php" class="text-gray-700 hover:text-blue-600 transition-colors">My Complaints</a>
                            <a href="<?php echo $base_url; ?>/<?php echo getUserRole(); ?>/submit.php" class="text-gray-700 hover:text-blue-600 transition-colors">Submit Complaint</a>
                        <?php endif; ?>

                        <a href="<?php echo $base_url; ?>/faq.php" class="text-gray-700 hover:text-blue-600 transition-colors">FAQ</a>
                        
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 transition-colors">
                                <i class="fas fa-user-circle"></i>
                                <span><?php echo $_SESSION['full_name'] ?? ''; ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-1">
                                    <a href="<?php echo $base_url; ?>/<?php echo getUserRole(); ?>/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <a href="<?php echo $base_url; ?>/<?php echo getUserRole(); ?>/notifications.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Notifications</a>
                                    <hr class="my-1">
                                    <a href="<?php echo $base_url; ?>/auth/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $base_url; ?>/" class="text-gray-700 hover:text-blue-600 transition-colors">Home</a>
                        <a href="<?php echo $base_url; ?>/about.php" class="text-gray-700 hover:text-blue-600 transition-colors">About</a>
                        <a href="<?php echo $base_url; ?>/faq.php" class="text-gray-700 hover:text-blue-600 transition-colors">FAQ</a>
                        <a href="<?php echo $base_url; ?>/contact.php" class="text-gray-700 hover:text-blue-600 transition-colors">Contact</a>
                        <a href="<?php echo $base_url; ?>/auth/login.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Login</a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <?php if (isLoggedIn()): ?>
                    <?php if (getUserRole() === 'admin'): ?>
                        <a href="<?php echo $base_url; ?>/admin/dashboard.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded">Dashboard</a>
                        <a href="<?php echo $base_url; ?>/admin/complaints.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded">Complaints</a>
                        <a href="<?php echo $base_url; ?>/admin/users.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded">Users</a>
                    <?php else: ?>
                        <a href="<?php echo $base_url; ?>/<?php echo getUserRole(); ?>/dashboard.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded">Dashboard</a>
                        <a href="<?php echo $base_url; ?>/<?php echo getUserRole(); ?>/complaints.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded">My Complaints</a>
                        <a href="<?php echo $base_url; ?>/<?php echo getUserRole(); ?>/submit.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded">Submit Complaint</a>
                    <?php endif; ?>
                    <a href="<?php echo $base_url; ?>/auth/logout.php" class="block px-3 py-2 text-red-600 hover:bg-red-50 rounded">Logout</a>
                <?php else: ?>
                    <a href="<?php echo $base_url; ?>/" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded">Home</a>
                    <a href="<?php echo $base_url; ?>/about.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded">About</a>
                    <a href="<?php echo $base_url; ?>/contact.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded">Contact</a>
                    <a href="<?php echo $base_url; ?>/auth/login.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
<!-- header.php ends here; footer.php must close body/html -->