<?php
@include 'config.php';
session_start();

$message = [];
$message_type = ''; 

if(isset($_POST['submit_message'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $msg = mysqli_real_escape_string($conn, $_POST['message']);

    if(empty($name) || empty($email) || empty($msg)){
        $message[] = 'Please fill out all fields.';
        $message_type = 'error';
    } else {
      
        
        $message[] = 'Thank you for your message! We will get back to you shortly.';
        $message_type = 'success';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - M & S Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #0a0a0a; 
            color: #e5e7eb;
        }
        .glass-nav { 
            background: rgba(17, 24, 39, 0.5); 
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .header-bg {
            background: linear-gradient(180deg, rgba(10,10,10,1) 0%, rgba(17,24,39,0) 100%), 
                        url('https://images.unsplash.com/photo-1586769852836-bc069f19e1b6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') no-repeat center center;
            background-size: cover;
        }
        .map-container iframe {
            filter: invert(90%) hue-rotate(200deg) brightness(1.1) contrast(0.9);
        }
    </style>
</head>
<body class="antialiased">

    <div class="header-bg">
        <header class="relative">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="py-4 flex justify-between items-center glass-nav rounded-full mt-4 px-6 shadow-lg">
                    <a href="index.php" class="text-3xl font-bold tracking-wider">M&S <span class="text-indigo-400">Learning</span></a>
                    <ul class="hidden md:flex items-center space-x-8 text-lg">
                        <li><a href="index.php" class="hover:text-indigo-300 transition-colors duration-300">Home</a></li>
                        <li><a href="course-page.php" class="hover:text-indigo-300 transition-colors duration-300">Courses</a></li>
                        <li><a href="instructors.php" class="hover:text-indigo-300 transition-colors duration-300">Instructors</a></li>
                        <li><a href="about.php" class="hover:text-indigo-300 transition-colors duration-300">About</a></li>
                        <li><a href="contact.php" class="text-white font-semibold">Contact</a></li>
                    </ul>
                    <div class="flex items-center space-x-4">
                        <?php if (isset($_SESSION['admin_name'])): ?>
                            <a href="admin_panel.php" class="text-lg hover:text-indigo-300 transition-colors duration-300"><i class="fas fa-user-shield mr-2"></i>Admin Panel</a>
                            <a href="logout.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-5 rounded-full transition-all duration-300">Logout</a>
                        <?php elseif (isset($_SESSION['instructor_name'])): ?>
                            <a href="instructor_panel.php" class="text-lg hover:text-indigo-300 transition-colors duration-300"><i class="fas fa-chalkboard-teacher mr-2"></i>Instructor Panel</a>
                            <a href="logout.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-5 rounded-full transition-all duration-300">Logout</a>
                        <?php elseif (isset($_SESSION['user_name'])): ?>
                            <a href="student_panel.php" class="text-lg hover:text-indigo-300 transition-colors duration-300"><i class="fas fa-user-graduate mr-2"></i>Student Panel</a>
                            <a href="logout.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-5 rounded-full transition-all duration-300">Logout</a>
                        <?php else: ?>
                            <a href="login_form.php" class="text-lg hover:text-indigo-300 transition-colors duration-300">Login</a>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>
        </header>
        <div class="text-center py-24 md:py-32">
            <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter">Get In Touch</h1>
            <p class="text-lg text-gray-300 mt-4 max-w-3xl mx-auto">We're here to help and answer any question you might have. We look forward to hearing from you.</p>
        </div>
    </div>

    <main class="container mx-auto p-6 md:p-12 -mt-16">
        <div class="bg-gray-800 rounded-lg shadow-2xl p-8 md:p-12 border border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-6">Contact Information</h2>
                    <div class="space-y-6 text-gray-300">
                        <div class="flex items-start space-x-4">
                            <i class="fas fa-map-marker-alt text-2xl text-indigo-400 mt-1"></i>
                            <div>
                                <h3 class="font-semibold text-white">Our Address</h3>
                                <p>Aftabnagar, Dhaka, Bangladesh</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <i class="fas fa-envelope text-2xl text-indigo-400 mt-1"></i>
                            <div>
                                <h3 class="font-semibold text-white">Email Us</h3>
                                <p>contact@mslearning.com</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <i class="fas fa-phone text-2xl text-indigo-400 mt-1"></i>
                            <div>
                                <h3 class="font-semibold text-white">Call Us</h3>
                                <p>+880 1234 567890</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 rounded-lg overflow-hidden map-container">
                         <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.4062059082057!2d90.42292431106968!3d23.76854528799015!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c7892dcf0001%3A0x853ad729be4edc71!2sEast%20West%20University!5e0!3m2!1sen!2sbd!4v1692635379631!5m2!1sen!2sbd" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-white mb-6">Send Us a Message</h2>
                    <?php
                    if (!empty($message)) {
                        $bg_color = ($message_type === 'success') ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300';
                        $border_color = ($message_type === 'success') ? 'border-green-500' : 'border-red-500';
                        foreach ($message as $msg) {
                            echo '<div class="mb-6 p-4 rounded-lg border ' . $border_color . ' ' . $bg_color . '">' . htmlspecialchars($msg) . '</div>';
                        }
                    }
                    ?>
                    <form action="" method="post" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-400">Full Name</label>
                            <input type="text" name="name" id="name" class="w-full mt-1 px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-400">Email Address</label>
                            <input type="email" name="email" id="email" class="w-full mt-1 px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-400">Message</label>
                            <textarea name="message" id="message" rows="5" class="w-full mt-1 px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
                        </div>
                        <button type="submit" name="submit_message" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition-all duration-300 shadow-lg">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 mt-16">
        <div class="container mx-auto px-6 py-12 text-center">
            <p>&copy; <?php echo date("Y"); ?> M & S Learning. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>
