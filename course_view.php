<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login_form.php');
    exit();
}

if(!isset($_GET['course_id'])){
    header('location:student_panel.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$course_id = (int)$_GET['course_id'];

$message = [];
$message_type = 'error';

$course_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$course_id'");
$course = mysqli_fetch_assoc($course_query);

if(!$course){
    header('location:student_panel.php');
    exit();
}


if(isset($_POST['submit_assignment'])){
    $assignment_id = (int)$_POST['assignment_id'];
    $submission_file = $_FILES['submission_file']['name'];
    $submission_file_tmp_name = $_FILES['submission_file']['tmp_name'];
    $submission_folder = 'submissions/' . basename($submission_file);

    if(empty($submission_file)){
        $message[] = 'Please select a file to upload.';
    } else {
        $check_submission = mysqli_query($conn, "SELECT * FROM `submissions` WHERE assignment_id = '$assignment_id' AND user_id = '$user_id'");
        if(mysqli_num_rows($check_submission) > 0){
            $message[] = 'You have already submitted this assignment.';
        } else {
            if (!is_dir('submissions')) {
                mkdir('submissions', 0777, true);
            }
            if(move_uploaded_file($submission_file_tmp_name, $submission_folder)){
                $insert_submission = mysqli_query($conn, "INSERT INTO `submissions`(assignment_id, user_id, user_name, submission_file) VALUES('$assignment_id', '$user_id', '$user_name', '$submission_file')");
                if($insert_submission){
                    $message[] = 'Assignment submitted successfully!';
                    $message_type = 'success';
                } else {
                    $message[] = 'Could not submit assignment.';
                }
            } else {
                $message[] = 'Failed to upload file.';
            }
        }
    }
}

$assignments_query = mysqli_query($conn, "SELECT * FROM `assignments` WHERE course_id = '$course_id' ORDER BY due_date ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($course['name']); ?> - M & S Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .sidebar-link { transition: background-color 0.2s, color 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background-color: #4f46e5; color: white; }
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
                <h1 class="text-4xl font-bold text-white"><?php echo htmlspecialchars($course['name']); ?></h1>
                <p class="text-gray-400 mt-2"><?php echo htmlspecialchars($course['description']); ?></p>
            </header>

            <?php
            if (!empty($message)) {
                $bg_color = ($message_type === 'success') ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300';
                $border_color = ($message_type === 'success') ? 'border-green-500' : 'border-red-500';
                foreach ($message as $msg) {
                    echo '<div class="mb-6 p-4 rounded-lg border ' . $border_color . ' ' . $bg_color . '">' . htmlspecialchars($msg) . '</div>';
                }
            }
            ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-gray-800 rounded-lg shadow-xl border border-gray-700 overflow-hidden">
                        <div class="aspect-w-16 aspect-h-9">
                            <?php if(!empty($course['video_url'])): ?>
                                <iframe class="w-full h-full min-h-[450px]" src="<?php echo htmlspecialchars($course['video_url']); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            <?php else: ?>
                                <div class="flex items-center justify-center h-full bg-gray-700 min-h-[450px]">
                                    <p class="text-gray-400">No video available for this course.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 rounded-lg shadow-xl p-6 border border-gray-700">
                    <h2 class="text-2xl font-bold text-white mb-4">Assignments</h2>
                    <div class="space-y-4">
                        <?php
                        if($assignments_query && mysqli_num_rows($assignments_query) > 0){
                            while($assignment = mysqli_fetch_assoc($assignments_query)):
                                $assignment_id = $assignment['id'];
                                $submission_query = mysqli_query($conn, "SELECT grade, submitted_at FROM `submissions` WHERE assignment_id = '$assignment_id' AND user_id = '$user_id'");
                                $submission = mysqli_fetch_assoc($submission_query);
                        ?>
                        <div class="bg-gray-700/50 p-4 rounded-lg">
                            <h3 class="font-bold text-white"><?php echo htmlspecialchars($assignment['title']); ?></h3>
                            <p class="text-xs text-gray-400">Due: <?php echo date('M d, Y', strtotime($assignment['due_date'])); ?></p>
                            
                            <?php if($submission): ?>
                                <div class="mt-2 text-sm">
                                    <p class="text-green-400"><i class="fas fa-check-circle mr-2"></i>Submitted on <?php echo date('M d, Y', strtotime($submission['submitted_at'])); ?></p>
                                    <?php if($submission['grade']): ?>
                                        <p class="font-bold text-white mt-1">Grade: <span class="text-yellow-400"><?php echo htmlspecialchars($submission['grade']); ?></span></p>
                                    <?php else: ?>
                                        <p class="text-gray-400 mt-1">Awaiting Grade</p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <form action="" method="post" enctype="multipart/form-data" class="mt-3">
                                    <input type="hidden" name="assignment_id" value="<?php echo $assignment_id; ?>">
                                    <input type="file" name="submission_file" class="w-full text-sm text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-500/20 file:text-indigo-300 hover:file:bg-indigo-500/30">
                                    <button type="submit" name="submit_assignment" class="w-full mt-2 bg-indigo-600 text-white font-bold py-2 px-3 text-sm rounded-lg hover:bg-indigo-700 transition">Submit</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <?php
                            endwhile;
                        } else {
                            echo "<p class='text-gray-500 text-sm'>No assignments for this course yet.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
