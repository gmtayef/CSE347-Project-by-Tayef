<?php
@include 'config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Our Instructors - M & S Learning</title>
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
        .instructor-card {
            background: #111827;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .instructor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.3), 0 10px 10px -5px rgba(79, 70, 229, 0.2);
        }
        .header-bg {
            background: linear-gradient(180deg, rgba(10,10,10,1) 0%, rgba(17,24,39,0) 100%), 
                        url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80') no-repeat center center;
            background-size: cover;
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
                        <li><a href="instructors.php" class="text-white font-semibold">Instructors</a></li>
                        <li><a href="about.php" class="hover:text-indigo-300 transition-colors duration-300">About</a></li>
                        <li><a href="contact.php" class="hover:text-indigo-300 transition-colors duration-300">Contact</a></li>
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
            <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter">Meet Our Expert Instructors</h1>
            <p class="text-lg text-gray-300 mt-4 max-w-3xl mx-auto">Learn from the best. Our instructors are industry leaders and passionate educators dedicated to your success.</p>
        </div>
    </div>

    <main class="container mx-auto p-6 md:p-12 -mt-16">
        <?php if (isset($_SESSION['admin_name'])): ?>
        <div class="text-center mb-12">
            <a href="manage_instructors.php" class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-700 transition-all duration-300 shadow-lg"><i class="fas fa-plus mr-2"></i>Manage Instructors</a>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php
            $select_instructors = mysqli_query($conn, "SELECT * FROM `instructors`");
            if(mysqli_num_rows($select_instructors) > 0){
                while($instructor = mysqli_fetch_assoc($select_instructors)):
            ?>
            <div class="instructor-card text-center p-6 rounded-lg relative overflow-hidden">
                <img class="w-40 h-40 rounded-full mx-auto mb-4 object-cover border-4 border-indigo-500 shadow-md" src="instructor_img/<?php echo $instructor['image']; ?>" alt="<?php echo htmlspecialchars($instructor['name']); ?>">
                <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($instructor['name']); ?></h3>
                <p class="text-indigo-400 mb-2"><?php echo htmlspecialchars($instructor['expertise']); ?></p>
                <p class="text-xs text-gray-500">ID: <?php echo htmlspecialchars($instructor['instructor_id']); ?></p>
            </div>
            <?php 
                endwhile;
            } else {
                echo "<p class='col-span-full text-center text-gray-500 text-lg'>No instructors have been added yet.</p>";
            }
            ?>
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
