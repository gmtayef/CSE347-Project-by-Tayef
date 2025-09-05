<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

$message = [];
$message_type = 'error';

if (isset($_POST['add_instructor'])) {
    $name = mysqli_real_escape_string($conn, $_POST['i_name']);
    $expertise = mysqli_real_escape_string($conn, $_POST['i_expertise']);
    $email = mysqli_real_escape_string($conn, $_POST['i_email']);
    $pass = md5($_POST['i_password']);
    $image = $_FILES['i_image']['name'];
    $image_tmp_name = $_FILES['i_image']['tmp_name'];
    $image_folder = 'instructor_img/' . $image;

    if(empty($name) || empty($expertise) || empty($email) || empty($_POST['i_password']) || empty($image)){
        $message[] = 'Please fill out all fields.';
    } else {
        $check_email_query = mysqli_query($conn, "SELECT * FROM `instructors` WHERE email = '$email'");
        if(mysqli_num_rows($check_email_query) > 0){
            $message[] = 'An instructor with this email already exists.';
        } else {
            $insert_query = mysqli_query($conn, "INSERT INTO `instructors`(name, expertise, email, password, image) VALUES('$name', '$expertise', '$email', '$pass', '$image')");
            if ($insert_query) {
                move_uploaded_file($image_tmp_name, $image_folder);
                $message[] = 'Instructor added successfully!';
                $message_type = 'success';
            } else {
                $message[] = 'Could not add the instructor.';
            }
        }
    }
}

if (isset($_POST['update_instructor'])) {
    $update_i_id = (int)$_POST['update_i_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_i_name']);
    $update_expertise = mysqli_real_escape_string($conn, $_POST['update_i_expertise']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_i_email']);
    
    $update_query_sql = "UPDATE `instructors` SET name = '$update_name', expertise = '$update_expertise', email = '$update_email' WHERE id = '$update_i_id'";
    
    if(!empty($_POST['update_i_password'])){
        $update_pass = md5($_POST['update_i_password']);
        $update_query_sql = "UPDATE `instructors` SET name = '$update_name', expertise = '$update_expertise', email = '$update_email', password = '$update_pass' WHERE id = '$update_i_id'";
    }

    $update_query = mysqli_query($conn, $update_query_sql);
    if($update_query){
       $message[] = 'Instructor updated successfully!';
       $message_type = 'success';
    } else {
       $message[] = 'Could not update instructor.';
    }
}

if(isset($_GET['delete'])){
   $delete_id = (int)$_GET['delete'];
   mysqli_query($conn, "DELETE FROM `instructors` WHERE id = $delete_id");
   header('location:manage_instructors.php');
   exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Instructors - Admin Panel</title>
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
                <a href="admin_panel.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-chalkboard-teacher mr-3 w-5 text-center"></i>Courses</a>
                <a href="manage_instructors.php" class="sidebar-link active flex items-center px-4 py-2 rounded-lg"><i class="fas fa-users mr-3 w-5 text-center"></i>Instructors</a>
                <a href="manage_assignments.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-tasks mr-3 w-5 text-center"></i>Assignments</a>
                <a href="manage_feedback.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-comment-dots mr-3 w-5 text-center"></i>Feedback</a>
                <a href="index.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-home mr-3 w-5 text-center"></i>View Site</a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                 <a href="logout.php" class="sidebar-link flex items-center px-4 py-2 rounded-lg"><i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>Logout</a>
            </div>
        </aside>
        <main class="flex-1 p-6 md:p-10">
            <h1 class="text-3xl font-bold text-white mb-6">Instructor Management</h1>

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
                    $edit_query = mysqli_query($conn, "SELECT * FROM `instructors` WHERE id = $edit_id");
                    if($edit_query && mysqli_num_rows($edit_query) > 0){
                        $fetch_edit = mysqli_fetch_assoc($edit_query);
                ?>
                <h2 class="text-2xl font-bold text-white mb-6">Edit Instructor</h2>
                <form action="" method="post" class="space-y-6">
                    <input type="hidden" name="update_i_id" value="<?php echo $fetch_edit['id']; ?>">
                    <input type="text" name="update_i_name" value="<?php echo htmlspecialchars($fetch_edit['name']); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    <input type="text" name="update_i_expertise" value="<?php echo htmlspecialchars($fetch_edit['expertise']); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    <input type="email" name="update_i_email" value="<?php echo htmlspecialchars($fetch_edit['email']); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    <input type="password" name="update_i_password" placeholder="Enter new password (optional)" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <button type="submit" name="update_instructor" class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-700 transition">Update Instructor</button>
                        <a href="manage_instructors.php" class="text-gray-400 hover:text-white">Cancel</a>
                    </div>
                </form>
                <?php
                    }
                    echo "<script>document.querySelector('.edit-form-container').style.display = 'block';</script>";
                }
                ?>
            </section>

            <div class="bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 mb-8 border border-gray-700">
                <h2 class="text-2xl font-bold text-white mb-6">Add a New Instructor</h2>
                <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                    <input type="text" name="i_name" placeholder="Enter instructor's name" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    <input type="text" name="i_expertise" placeholder="Enter area of expertise" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    <input type="email" name="i_email" placeholder="Enter instructor's email" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    <input type="password" name="i_password" placeholder="Enter a password" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" required>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Instructor Image</label>
                        <input type="file" name="i_image" accept="image/png, image/jpg, image/jpeg" class="w-full mt-1 text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-500/20 file:text-indigo-300 hover:file:bg-indigo-500/30" required>
                    </div>
                    <button type="submit" name="add_instructor" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition">Add Instructor</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
