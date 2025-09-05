<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login_form.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$purchased_courses_query = mysqli_query($conn, "
    SELECT p.* FROM `products` p
    INNER JOIN `orders` o ON p.id = o.product_id
    WHERE o.user_id = '$user_id'
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Panel - M & S Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .sidebar-link { transition: background-color 0.2s, color 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background-color: #4f46e5; color: white; }
        .course-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .course-card:hover { transform: translateY(-8px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2); }
    </style>
</head>
<body class="bg-gray-900 text-gray-200">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-800 text-white flex-none flex flex-col shadow-2xl">
            <div class="p-6 text-2xl font-bold border-b border-gray-700">M&S <span class="text-indigo-400">Student</span></div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="student_panel.php" class="sidebar-link active flex items-center px-4 py-2 rounded-lg"><i class="fas fa-book-open mr-3 w-5 text-center"></i>My Courses</a>
                <a href="index.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-home mr-3 w-5 text-center"></i>View Site</a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                 <a href="logout.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>Logout</a>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-10">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-white">Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
                <p class="text-gray-400">Continue your learning journey. Your purchased courses are listed below.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                if($purchased_courses_query && mysqli_num_rows($purchased_courses_query) > 0){
                    while($course = mysqli_fetch_assoc($purchased_courses_query)):
                        $course_id = $course['id'];
                        
                        $total_assignments_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM `assignments` WHERE course_id = '$course_id'");
                        $total_assignments = mysqli_fetch_assoc($total_assignments_query)['total'];

                        $completed_assignments_query = mysqli_query($conn, "SELECT COUNT(DISTINCT s.assignment_id) as completed FROM `submissions` s JOIN `assignments` a ON s.assignment_id = a.id WHERE a.course_id = '$course_id' AND s.user_id = '$user_id'");
                        $completed_assignments = mysqli_fetch_assoc($completed_assignments_query)['completed'];
                        
                        $progress = ($total_assignments > 0) ? ($completed_assignments / $total_assignments) * 100 : 0;
                ?>
                <div class="course-card bg-gray-800 rounded-lg overflow-hidden border border-gray-700 flex flex-col">
                    <img src="uploaded_img/<?php echo $course['image']; ?>" alt="<?php echo htmlspecialchars($course['name']); ?>" class="w-full h-48 object-cover">
                    <div class="p-6 flex-grow flex flex-col">
                        <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($course['name']); ?></h3>
                        <p class="text-gray-400 text-sm mb-4 flex-grow"><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...</p>
                        
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-400 mb-1">
                                <span>Progress</span>
                                <span><?php echo round($progress); ?>%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2.5">
                                <div class="bg-indigo-500 h-2.5 rounded-full" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1"><?php echo $completed_assignments; ?> of <?php echo $total_assignments; ?> assignments completed.</p>
                        </div>

                        <a href="course_view.php?course_id=<?php echo $course['id']; ?>" class="mt-auto text-center w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition">Start Learning</a>
                    </div>
                </div>
                <?php
                    endwhile;
                } else {
                    echo "<div class='col-span-full text-center py-16 bg-gray-800 rounded-lg'><p class='text-gray-400'>You have not purchased any courses yet.</p><a href='index.php' class='mt-4 inline-block text-indigo-400 hover:text-indigo-300'>Explore Courses &rarr;</a></div>";
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>
