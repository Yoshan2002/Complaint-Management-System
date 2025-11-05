<?php
require_once 'config/config.php';

// compute project-aware base URL so links stay inside the project
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$page_title = 'About - University Complaint System';
include 'includes/header.php';
?>

<div class="fade-in">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold mb-4">About Our System</h1>
            <p class="text-xl text-blue-100">Streamlining campus life through efficient complaint management</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="prose max-w-none">
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h2>
                    <p class="text-gray-600 mb-6">
                        The University Complaint System is designed to provide a streamlined, efficient platform for students and staff to report issues, track their resolution progress, and maintain communication with the administration. Our goal is to create a more responsive and transparent environment that enhances the university experience for everyone.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-eye text-blue-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Transparency</h3>
                        <p class="text-gray-600">
                            Every complaint is tracked from submission to resolution, providing complete visibility into the process.
                        </p>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-bolt text-green-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Efficiency</h3>
                        <p class="text-gray-600">
                            Automated workflows and notifications ensure quick response times and proper escalation.
                        </p>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <div class="bg-purple-100 w-12 h-12 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-users text-purple-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Accessibility</h3>
                        <p class="text-gray-600">
                            Easy-to-use interface that works on all devices, making it simple for everyone to report issues.
                        </p>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <div class="bg-orange-100 w-12 h-12 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Analytics</h3>
                        <p class="text-gray-600">
                            Data-driven insights help identify trends and improve campus services continuously.
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">How It Works</h2>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-blue-100 w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <span class="text-blue-600 font-bold text-sm">1</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Registration & Approval</h3>
                                <p class="text-gray-600">Students and staff register with their university email. Admin approval ensures security and authenticity.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-green-100 w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <span class="text-green-600 font-bold text-sm">2</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Submit Complaints</h3>
                                <p class="text-gray-600">Users can easily submit detailed complaints with photos, categorization, and priority levels.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-purple-100 w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <span class="text-purple-600 font-bold text-sm">3</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Track Progress</h3>
                                <p class="text-gray-600">Real-time status updates keep users informed throughout the resolution process.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-orange-100 w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <span class="text-orange-600 font-bold text-sm">4</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Resolution & Feedback</h3>
                                <p class="text-gray-600">Completed resolutions include detailed notes and the ability to provide feedback.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>