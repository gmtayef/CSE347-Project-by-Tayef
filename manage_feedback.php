<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

if(isset($_GET['delete'])){
   $delete_id = (int)$_GET['delete'];
   mysqli_query($conn, "DELETE FROM `feedback` WHERE id = $delete_id");
   header('location:manage_feedback.php');
   exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Feedback - Admin Panel</title>
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
                <a href="manage_feedback.php" class="sidebar-link active flex items-center px-4 py-2 rounded-lg"><i class="fas fa-comment-dots mr-3 w-5 text-center"></i>Feedback</a>
                <a href="index.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-home mr-3 w-5 text-center"></i>View Site</a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                 <a href="logout.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>Logout</a>
            </div>
        </aside>
        <main class="flex-1 p-6 md:p-10">
            <h1 class="text-3xl font-bold text-white mb-6">User Feedback Management</h1>

            <div class="bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 border border-gray-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-400">
                        <thead class="text-xs text-gray-300 uppercase bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3">User</th>
                                <th class="px-6 py-3">Feedback</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $select_feedback = mysqli_query($conn, "SELECT * FROM `feedback` ORDER BY created_at DESC");
                            if(mysqli_num_rows($select_feedback) > 0){
                                while($row = mysqli_fetch_assoc($select_feedback)):
                            ?>
                            <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white"><?php echo htmlspecialchars($row['user_name']); ?></div>
                                    <div class="text-xs text-indigo-400"><?php echo htmlspecialchars($row['user_type']); ?></div>
                                </td>
                                <td class="px-6 py-4 max-w-md"><?php echo htmlspecialchars($row['feedback_text']); ?></td>
                                <td class="px-6 py-4"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td class="px-6 py-4 text-lg">
                                    <a href="manage_feedback.php?delete=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-400" title="Delete" onclick="return confirm('Delete this feedback?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            } else {
                                echo '<tr><td colspan="4" class="text-center py-10">No feedback submitted yet.</td></tr>';
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
