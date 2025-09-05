<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

$message = [];
$message_type = 'error';

$courses_query = mysqli_query($conn, "SELECT id, name FROM `products`");


if(isset($_POST['add_assignment'])){
    $course_id = (int)$_POST['course_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $due_date = date('Y-m-d H:i:s', strtotime($_POST['due_date']));

    if(empty($course_id) || empty($title) || empty($_POST['due_date'])){
        $message[] = 'Please fill out all fields.';
    } else {
        $insert_assignment = mysqli_query($conn, "INSERT INTO `assignments`(course_id, title, due_date) VALUES('$course_id', '$title', '$due_date')");
        if($insert_assignment){
            $message[] = 'Assignment added successfully!';
            $message_type = 'success';
        } else {
            $message[] = 'Could not add assignment.';
        }
    }
}


if(isset($_POST['grade_submission'])){
    $submission_id = (int)$_POST['submission_id'];
    $grade = mysqli_real_escape_string($conn, $_POST['grade']);
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);

    $update_grade = mysqli_query($conn, "UPDATE `submissions` SET grade = '$grade', comments = '$comments' WHERE id = '$submission_id'");
    if($update_grade){
        $message[] = 'Grade submitted successfully!';
        $message_type = 'success';
    } else {
        $message[] = 'Could not submit grade.';
    }
}


$submissions_query = mysqli_query($conn, "
    SELECT s.*, a.title as assignment_title, p.name as course_name 
    FROM `submissions` s
    JOIN `assignments` a ON s.assignment_id = a.id
    JOIN `products` p ON a.course_id = p.id
    ORDER BY s.submitted_at DESC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Assignments - Admin Panel</title>
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
        <aside class="w-64 bg-gray-800 text-white flex flex-col shadow-2xl">
            <div class="p-6 text-2xl font-bold border-b border-gray-700">M&S <span class="text-indigo-400">Admin</span></div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="admin_panel.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-chalkboard-teacher mr-3 w-5 text-center"></i>Courses</a>
                <a href="manage_instructors.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-users mr-3 w-5 text-center"></i>Instructors</a>
                <a href="manage_assignments.php" class="sidebar-link active flex items-center px-4 py-2 rounded-lg"><i class="fas fa-tasks mr-3 w-5 text-center"></i>Assignments</a>
                <a href="manage_feedback.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-comment-dots mr-3 w-5 text-center"></i>Feedback</a>
                <a href="index.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-home mr-3 w-5 text-center"></i>View Site</a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                 <a href="logout.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>Logout</a>
            </div>
        </aside>
        <main class="flex-1 p-6 md:p-10">
            <h1 class="text-3xl font-bold text-white mb-6">Assignment & Grading</h1>

            <?php
            if (!empty($message)) {
                $bg_color = ($message_type === 'success') ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300';
                $border_color = ($message_type === 'success') ? 'border-green-500' : 'border-red-500';
                foreach ($message as $msg) {
                    echo '<div class="mb-6 p-4 rounded-lg border ' . $border_color . ' ' . $bg_color . '">' . htmlspecialchars($msg) . '</div>';
                }
            }
            ?>

            <div class="bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 mb-8 border border-gray-700">
                <h2 class="text-2xl font-bold text-white mb-6">Create New Assignment</h2>
                <form action="" method="post" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Select Course</label>
                        <select name="course_id" class="w-full mt-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                            <option value="">-- Choose a course --</option>
                            <?php while($course = mysqli_fetch_assoc($courses_query)): ?>
                                <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <input type="text" name="title" placeholder="Assignment Title" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Due Date</label>
                        <input type="datetime-local" name="due_date" class="w-full mt-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    </div>
                    <button type="submit" name="add_assignment" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition">Create Assignment</button>
                </form>
            </div>

            <div class="bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 border border-gray-700">
                <h2 class="text-2xl font-bold text-white mb-6">Student Submissions</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-400">
                        <thead class="text-xs text-gray-300 uppercase bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3">Student</th>
                                <th class="px-6 py-3">Course / Assignment</th>
                                <th class="px-6 py-3">Submission</th>
                                <th class="px-6 py-3">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(mysqli_num_rows($submissions_query) > 0){
                                while($row = mysqli_fetch_assoc($submissions_query)):
                            ?>
                            <tr class="border-b border-gray-700">
                                <td class="px-6 py-4 font-medium text-white"><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white"><?php echo htmlspecialchars($row['course_name']); ?></div>
                                    <div class="text-xs text-gray-400"><?php echo htmlspecialchars($row['assignment_title']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="submissions/<?php echo htmlspecialchars($row['submission_file']); ?>" target="_blank" class="text-indigo-400 hover:underline">View File</a>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="" method="post" class="flex items-center gap-2">
                                        <input type="hidden" name="submission_id" value="<?php echo $row['id']; ?>">
                                        <input type="text" name="grade" placeholder="e.g. A+" value="<?php echo htmlspecialchars($row['grade']); ?>" class="w-20 px-2 py-1 bg-gray-700 border border-gray-600 rounded-lg text-sm">
                                        <button type="submit" name="grade_submission" class="bg-green-600 hover:bg-green-700 text-white font-bold px-3 py-1 rounded-lg text-sm">Save</button>
                                    </form>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            } else {
                                echo '<tr><td colspan="4" class="text-center py-10">No assignments submitted yet.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
