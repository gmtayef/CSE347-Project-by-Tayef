<?php
@include 'config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us - M & S Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/b67581ec1b.js" crossorigin="anonymous"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

    <nav class="bg-white shadow-md sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="py-4 flex justify-between items-center">
                <a href="index.php" class="text-2xl font-bold">M & S <span class="text-indigo-600">Learning</span></a>
                <ul class="hidden md:flex items-center space-x-8">
                    <li><a href="index.php" class="text-gray-600 hover:text-indigo-600 transition">Home</a></li>
                    <li><a href="course-page.php" class="text-gray-600 hover:text-indigo-600 transition">Courses</a></li>
                    <li><a href="about.php" class="text-indigo-600 font-semibold">About Us</a></li>
                    <li><a href="contact.php" class="text-gray-600 hover:text-indigo-600 transition">Contact</a></li>
                </ul>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['admin_name'])): ?>
                        <a href="admin_panel.php" class="text-gray-600 hover:text-indigo-600 transition" title="Go to Admin Panel"><i class="fas fa-user-shield mr-1"></i> Profile</a>
                        <a href="logout.php" class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded-lg transition">Logout</a>
                    <?php elseif (isset($_SESSION['user_name'])): ?>
                        <a href="student_panel.php" class="text-gray-600 hover:text-indigo-600 transition" title="Go to Student Panel"><i class="fas fa-user-graduate mr-1"></i> Profile</a>
                        <a href="logout.php" class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded-lg transition">Logout</a>
                    <?php else: ?>
                        <a href="login_form.php" class="text-gray-600 hover:text-indigo-600 transition">Login</a>
                        <a href="register_form.php" class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded-lg transition">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <header class="relative bg-cover bg-center h-96" style="background-image: url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="relative z-10 container mx-auto px-4 h-full flex flex-col justify-center items-center text-center text-white">
            <h1 class="text-5xl font-extrabold mb-2">About M & S Learning</h1>
            <p class="text-xl text-gray-200">Our mission is to make quality education accessible to everyone, everywhere.</p>
        </div>
    </header>

    <main class="container mx-auto p-6 md:p-12">
        <section class="bg-white rounded-lg shadow-md p-8 mb-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Our Story</h2>
                    <p class="text-gray-600 leading-relaxed">
                        M & S Learning was founded with a simple yet powerful idea: education should have no boundaries. We started as a small team of passionate educators and technologists who wanted to create a platform that could empower individuals to achieve their personal and professional goals. Today, we are proud to have helped thousands of students from all walks of life unlock their potential through our diverse range of online courses.
                    </p>
                </div>
                <div>
                    <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Team working together" class="rounded-lg shadow-lg">
                </div>
            </div>
        </section>

        <section class="text-center mb-12">
             <h2 class="text-3xl font-bold text-gray-800 mb-8">Our Core Values</h2>
             <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <i class="fas fa-lightbulb text-4xl text-indigo-500 mb-4"></i>
                    <h3 class="text-xl font-bold mb-2">Innovation</h3>
                    <p class="text-gray-600">We constantly strive to find better ways to teach and learn.</p>
                </div>
                 <div class="bg-white p-6 rounded-lg shadow-md">
                    <i class="fas fa-users text-4xl text-indigo-500 mb-4"></i>
                    <h3 class="text-xl font-bold mb-2">Community</h3>
                    <p class="text-gray-600">We foster a supportive network of learners and instructors.</p>
                </div>
                 <div class="bg-white p-6 rounded-lg shadow-md">
                    <i class="fas fa-check-circle text-4xl text-indigo-500 mb-4"></i>
                    <h3 class="text-xl font-bold mb-2">Excellence</h3>
                    <p class="text-gray-600">We are committed to the highest standards of quality in education.</p>
                </div>
             </div>
        </section>

        <section id="instructors">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Meet Our Instructors</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <?php
                $select_instructors = mysqli_query($conn, "SELECT * FROM `instructors` LIMIT 4");
                if (mysqli_num_rows($select_instructors) > 0) {
                    while ($instructor = mysqli_fetch_assoc($select_instructors)) {
                ?>
                <div class="bg-white text-center rounded-lg shadow-md p-6 transform hover:-translate-y-2 transition-transform">
                    <img class="w-32 h-32 rounded-full mx-auto mb-4 object-cover" src="instructor_img/<?php echo $instructor['image']; ?>" alt="<?php echo htmlspecialchars($instructor['name']); ?>">
                    <h3 class="text-xl font-bold"><?php echo htmlspecialchars($instructor['name']); ?></h3>
                    <p class="text-indigo-500"><?php echo htmlspecialchars($instructor['expertise']); ?></p>
                </div>
                <?php
                    }
                } else {
                    echo "<p class='col-span-full text-center text-gray-500'>Our team of instructors is growing. Check back soon!</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <footer class="bg-gray-900 text-white mt-12">
        <div class="container mx-auto px-4 py-12 text-center">
            <p>&copy; <?php echo date("Y"); ?> M & S Learning. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>
