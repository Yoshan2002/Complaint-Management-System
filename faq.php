<?php
require_once 'config/config.php';

// compute project-aware base URL so links stay inside the project
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$page_title = 'FAQ - University Complaint System';
include 'includes/header.php';
?>

<div class="fade-in">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Frequently Asked Questions</h1>
            <p class="text-xl text-blue-100">Find answers to common questions about the complaint system</p>
        </div>
    </section>

    <!-- FAQ Content -->
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-8">
                <!-- General Questions -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">General Questions</h2>
                    
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">What is the University Complaint System?</h3>
                            <p class="text-gray-600">The University Complaint System is a digital platform that allows students and staff to submit, track, and resolve various campus-related issues efficiently. It provides a transparent and organized way to handle complaints ranging from maintenance issues to academic concerns.</p>
                        </div>

                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Who can use this system?</h3>
                            <p class="text-gray-600">The system is available to all university students and staff members. New users need to register with their university email address and wait for admin approval before they can start submitting complaints.</p>
                        </div>

                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Is my information secure?</h3>
                            <p class="text-gray-600">Yes, we take data security seriously. All personal information is encrypted and stored securely. Only authorized personnel can access complaint details, and your privacy is protected throughout the process.</p>
                        </div>
                    </div>
                </div>

                <!-- Account & Registration -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Account & Registration</h2>
                    
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">How do I create an account?</h3>
                            <p class="text-gray-600">Click on "Register" from the homepage, fill in your details with your university email address, select your role (student or staff), and submit. You'll need to wait for admin approval before you can log in.</p>
                        </div>

                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Why do I need admin approval?</h3>
                            <p class="text-gray-600">Admin approval ensures that only legitimate university members can access the system, maintaining security and preventing spam or fraudulent complaints.</p>
                        </div>

                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">I forgot my password. What should I do?</h3>
                            <p class="text-gray-600">Currently, password reset functionality is being developed. Please contact the system administrator at admin@university.edu for password reset assistance.</p>
                        </div>
                    </div>
                </div>

                <!-- Submitting Complaints -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Submitting Complaints</h2>
                    
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">What types of complaints can I submit?</h3>
                            <p class="text-gray-600">You can submit complaints in four main categories:</p>
                            <ul class="list-disc list-inside mt-2 text-gray-600 space-y-1">
                                <li><strong>Maintenance:</strong> Broken equipment, facility repairs, cleaning issues</li>
                                <li><strong>Academic:</strong> Course-related problems, faculty issues, academic services</li>
                                <li><strong>Facility:</strong> Library, cafeteria, parking, accessibility issues</li>
                                <li><strong>Other:</strong> Any other campus-related concerns</li>
                            </ul>
                        </div>

                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Can I upload photos with my complaint?</h3>
                            <p class="text-gray-600">Yes! You can upload photos to help illustrate your complaint. This is especially helpful for maintenance issues. Supported formats are JPG, PNG, and GIF with a maximum file size of 5MB.</p>
                        </div>

                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">How should I write an effective complaint?</h3>
                            <p class="text-gray-600">Be specific and detailed. Include:</p>
                            <ul class="list-disc list-inside mt-2 text-gray-600 space-y-1">
                                <li>Exact location of the issue</li>
                                <li>When the problem occurred</li>
                                <li>What exactly is wrong</li>
                                <li>How it affects you or others</li>
                                <li>Any previous attempts to resolve it</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tracking & Status -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Tracking & Status</h2>
                    
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">What do the different status levels mean?</h3>
                            <div class="space-y-2 mt-2">
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                    <span class="text-gray-600">Your complaint has been submitted and is awaiting review</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">In Progress</span>
                                    <span class="text-gray-600">Your complaint is being actively worked on</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Resolved</span>
                                    <span class="text-gray-600">Your complaint has been resolved</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Withdrawn</span>
                                    <span class="text-gray-600">The complaint was withdrawn by the submitter</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">How will I know when my complaint status changes?</h3>
                            <p class="text-gray-600">You'll receive notifications both in the system and via email when your complaint status is updated. Check your notifications page regularly for updates.</p>
                        </div>

                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Can I edit or withdraw my complaint?</h3>
                            <p class="text-gray-600">You can edit or withdraw your complaint only while it's in "Pending" status. Once it moves to "In Progress," you'll need to contact the administrator for any changes.</p>
                        </div>
                    </div>
                </div>

                <!-- Technical Support -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Technical Support</h2>
                    
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">I'm having trouble logging in. What should I do?</h3>
                            <p class="text-gray-600">First, make sure your account has been approved by an administrator. If you're still having issues, check that you're using the correct email and password. Contact support if problems persist.</p>
                        </div>

                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">The system is running slowly. Is this normal?</h3>
                            <p class="text-gray-600">Occasional slowness may occur during peak usage times. If the system is consistently slow, please report this as a technical issue through the contact page.</p>
                        </div>

                        <div class="pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Who should I contact for additional help?</h3>
                            <p class="text-gray-600">For technical issues or questions not covered here, contact our support team:</p>
                            <ul class="list-disc list-inside mt-2 text-gray-600 space-y-1">
                                <li>Email: support@university.edu</li>
                                <li>Phone: +1 (555) 123-4567</li>
                                <li>Office Hours: Monday-Friday, 8:00 AM - 5:00 PM</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>