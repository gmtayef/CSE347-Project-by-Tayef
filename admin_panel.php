<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

$message = [];
$message_type = 'error';

$instructors_query = mysqli_query($conn, "SELECT name FROM `instructors`");
$instructors = mysqli_fetch_all($instructors_query, MYSQLI_ASSOC);


if (isset($_POST['add_product'])) {
    $p_name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $p_price = (float)$_POST['p_price'];
    $p_description = mysqli_real_escape_string($conn, $_POST['p_description']);
    $p_instructor = mysqli_real_escape_string($conn, $_POST['p_instructor']);
    $p_video_url = mysqli_real_escape_string($conn, $_POST['p_video_url']);
    $p_start_time = date('Y-m-d H:i:s', strtotime($_POST['p_start_time']));
    $p_end_time = date('Y-m-d H:i:s', strtotime($_POST['p_end_time']));
    $p_image = $_FILES['p_image']['name'];
    $p_image_tmp_name = $_FILES['p_image']['tmp_name'];
    $p_image_folder = 'uploaded_img/' . $p_image;

    if(empty($p_name) || empty($p_price) || empty($p_description) || empty($p_instructor) || empty($_POST['p_start_time']) || empty($_POST['p_end_time']) || empty($p_image)){
        $message[] = 'Please fill out all fields, including assigning an instructor.';
    } elseif (strtotime($p_start_time) >= strtotime($p_end_time)) {
        $message[] = 'Error: The course end time must be after the start time.';
    } else {
        $conflict_query = "SELECT * FROM `products` WHERE ('$p_start_time' < end_time AND '$p_end_time' > start_time)";
        $conflict_result = mysqli_query($conn, $conflict_query);

        if (mysqli_num_rows($conflict_result) > 0) {
            $message[] = 'Error: Course schedule conflicts with an existing course.';
        } else {
            $insert_query = mysqli_query($conn, "INSERT INTO `products`(name, price, image, description, start_time, end_time, instructor_name, video_url) VALUES('$p_name', '$p_price', '$p_image', '$p_description', '$p_start_time', '$p_end_time', '$p_instructor', '$p_video_url')");
            if ($insert_query) {
                move_uploaded_file($p_image_tmp_name, $p_image_folder);
                $message[] = 'Course added successfully!';
                $message_type = 'success';
            } else {
                $message[] = 'Could not add the course. DB Error: ' . mysqli_error($conn);
            }
        }
    }
}

if (isset($_POST['update_product'])) {
    $update_p_id = (int)$_POST['update_p_id'];
    $update_p_name = mysqli_real_escape_string($conn, $_POST['update_p_name']);
    $update_p_price = (float)$_POST['update_p_price'];
    $update_p_description = mysqli_real_escape_string($conn, $_POST['update_p_description']);
    $update_p_instructor = mysqli_real_escape_string($conn, $_POST['update_p_instructor']);
    $update_p_video_url = mysqli_real_escape_string($conn, $_POST['update_p_video_url']);
    $update_p_start_time = date('Y-m-d H:i:s', strtotime($_POST['update_p_start_time']));
    $update_p_end_time = date('Y-m-d H:i:s', strtotime($_POST['update_p_end_time']));

    if (strtotime($update_p_start_time) >= strtotime($update_p_end_time)) {
        $message[] = 'Error: The end time must be after the start time.';
    } else {
        $conflict_query = "SELECT * FROM `products` WHERE ('$update_p_start_time' < end_time AND '$update_p_end_time' > start_time) AND id != '$update_p_id'";
        $conflict_result = mysqli_query($conn, $conflict_query);

        if (mysqli_num_rows($conflict_result) > 0) {
            $message[] = 'Error: The updated schedule conflicts with another course.';
        } else {
            $update_sql = "UPDATE `products` SET name = '$update_p_name', price = '$update_p_price', description = '$update_p_description', start_time = '$update_p_start_time', end_time = '$update_p_end_time', instructor_name = '$update_p_instructor', video_url = '$update_p_video_url' WHERE id = '$update_p_id'";
            $update_query = mysqli_query($conn, $update_sql);
            if ($update_query) {
                header('location:admin_panel.php?message=updated');
                exit();
            } else {
                $message[] = 'Could not update the course. DB Error: ' . mysqli_error($conn);
            }
        }
    }
}

if(isset($_GET['delete'])){
   $delete_id = (int)$_GET['delete'];
   mysqli_query($conn, "DELETE FROM `products` WHERE id = $delete_id");
   header('location:admin_panel.php?message=deleted');
   exit();
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - M & S Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .edit-form-container { display: none; }
        .sidebar-link { transition: background-color 0.2s, color 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background-color: #4f46e5; color: white; }
    </style>
</head>
<body class="bg-gray-900 text-gray-200">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-800 text-white flex flex-col shadow-2xl">
            <div class="p-6 text-2xl font-bold border-b border-gray-700">M&S <span class="text-indigo-400">Admin</span></div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="admin_panel.php" class="sidebar-link active flex items-center px-4 py-2 rounded-lg"><i class="fas fa-chalkboard-teacher mr-3 w-5 text-center"></i>Courses</a>
                <a href="manage_instructors.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-users mr-3 w-5 text-center"></i>Instructors</a>
                <a href="manage_assignments.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-tasks mr-3 w-5 text-center"></i>Assignments</a>
                <a href="manage_feedback.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-comment-dots mr-3 w-5 text-center"></i>Feedback</a>
                <a href="index.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-home mr-3 w-5 text-center"></i>View Site</a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                 <a href="logout.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>Logout</a>
            </div>
        </aside>
        <main class="flex-1 p-6 md:p-10">
            <h1 class="text-3xl font-bold text-white mb-6">Course Management</h1>

            <?php
            if (!empty($message)) {
                $bg_color = ($message_type === 'success') ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300';
                $border_color = ($message_type === 'success') ? 'border-green-500' : 'border-red-500';
                foreach ($message as $msg) {
                    echo '<div class="mb-6 p-4 rounded-lg border ' . $border_color . ' ' . $bg_color . '">' . htmlspecialchars($msg) . '</div>';
                }
            }
            ?>

            <section class="edit-form-container bg-gray-800 rounded-lg shadow-xl p-8 mb-8 border border-gray-700">
                <?php
                if(isset($_GET['edit'])){
                    $edit_id = (int)$_GET['edit'];
                    $edit_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = $edit_id");
                    if($edit_query && mysqli_num_rows($edit_query) > 0){
                        $fetch_edit = mysqli_fetch_assoc($edit_query);
                ?>
                <h2 class="text-2xl font-bold text-white mb-6">Edit Course</h2>
                <form action="" method="post" class="space-y-6">
                    <input type="hidden" name="update_p_id" value="<?php echo $fetch_edit['id']; ?>">
                    <input type="text" name="update_p_name" value="<?php echo htmlspecialchars($fetch_edit['name']); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    <input type="number" step="0.01" name="update_p_price" min="0" value="<?php echo htmlspecialchars($fetch_edit['price']); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    <textarea name="update_p_description" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required><?php echo htmlspecialchars($fetch_edit['description']); ?></textarea>
                    <input type="text" name="update_p_video_url" value="<?php echo htmlspecialchars($fetch_edit['video_url']); ?>" placeholder="Enter YouTube embed URL" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Assign Instructor</label>
                        <select name="update_p_instructor" class="w-full mt-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                            <option value="">Select an instructor</option>
                            <?php foreach($instructors as $inst): ?>
                                <option value="<?php echo htmlspecialchars($inst['name']); ?>" <?php if($fetch_edit['instructor_name'] == $inst['name']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($inst['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-400">Start Time</label>
                            <input type="datetime-local" name="update_p_start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($fetch_edit['start_time'])); ?>" class="w-full mt-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400">End Time</label>
                            <input type="datetime-local" name="update_p_end_time" value="<?php echo date('Y-m-d\TH:i', strtotime($fetch_edit['end_time'])); ?>" class="w-full mt-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button type="submit" name="update_product" class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-700 transition">Update Course</button>
                        <a href="admin_panel.php" class="text-gray-400 hover:text-white">Cancel</a>
                    </div>
                </form>
                <?php
                    }
                    echo "<script>document.querySelector('.edit-form-container').style.display = 'block';</script>";
                }
                ?>
            </section>

            <div class="bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 mb-8 border border-gray-700">
                <h2 class="text-2xl font-bold text-white mb-6">Add a New Course</h2>
                <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                    <input type="text" name="p_name" placeholder="Enter course name" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    <input type="number" step="0.01" name="p_price" min="0" placeholder="Enter course price" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    <textarea name="p_description" placeholder="Enter course description" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required></textarea>
                     <input type="text" name="p_video_url" placeholder="Enter YouTube embed URL" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Assign Instructor</label>
                        <select name="p_instructor" class="w-full mt-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                            <option value="">Select an instructor</option>
                            <?php foreach($instructors as $inst): ?>
                                <option value="<?php echo htmlspecialchars($inst['name']); ?>"><?php echo htmlspecialchars($inst['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-400">Start Time</label>
                            <input type="datetime-local" name="p_start_time" class="w-full mt-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400">End Time</label>
                            <input type="datetime-local" name="p_end_time" class="w-full mt-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Course Image</label>
                        <input type="file" name="p_image" accept="image/png, image/jpg, image/jpeg" class="w-full mt-1 text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-500/20 file:text-indigo-300 hover:file:bg-indigo-500/30" required>
                    </div>
                    <button type="submit" name="add_product" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition">Add Course</button>
                </form>
            </div>

            <div class="bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 border border-gray-700">
                <h2 class="text-2xl font-bold text-white mb-6">Manage Courses</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-400">
                        <thead class="text-xs text-gray-300 uppercase bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3">Course</th>
                                <th class="px-6 py-3">Instructor</th>
                                <th class="px-6 py-3">Price</th>
                                <th class="px-6 py-3">Schedule Start</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $select_products = mysqli_query($conn, "SELECT * FROM `products`");
                            if(mysqli_num_rows($select_products) > 0){
                                while($row = mysqli_fetch_assoc($select_products)):
                            ?>
                            <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                                <td class="px-6 py-4 font-medium text-white flex items-center">
                                    <img src="uploaded_img/<?php echo $row['image']; ?>" class="h-10 w-10 rounded-full mr-4 object-cover">
                                    <span><?php echo htmlspecialchars($row['name']); ?></span>
                                </td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['instructor_name']); ?></td>
                                <td class="px-6 py-4">$<?php echo htmlspecialchars($row['price']); ?>/-</td>
                                <td class="px-6 py-4"><?php echo date('M d, Y - h:i A', strtotime($row['start_time'])); ?></td>
                                <td class="px-6 py-4 text-lg">
                                    <a href="admin_panel.php?edit=<?php echo $row['id']; ?>" class="text-indigo-400 hover:text-indigo-300 mr-4" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="admin_panel.php?delete=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-400" title="Delete" onclick="return confirm('Delete this course?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            } else {
                                echo '<tr><td colspan="5" class="text-center py-10">No courses added yet.</td></tr>';
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
