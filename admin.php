<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

$message = [];
$message_type = 'error';

if (isset($_POST['add_product'])) {
    $p_name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $p_price = mysqli_real_escape_string($conn, $_POST['p_price']);
    $p_description = mysqli_real_escape_string($conn, $_POST['p_description']);
    $p_start_time = $_POST['p_start_time'];
    $p_end_time = $_POST['p_end_time'];
    
    $p_image = $_FILES['p_image']['name'];
    $p_image_tmp_name = $_FILES['p_image']['tmp_name'];
    $p_image_folder = 'uploaded_img/' . $p_image;

    if(strtotime($p_start_time) >= strtotime($p_end_time)){
        $message[] = 'Error: The course end time must be after the start time.';
    } else {
        $conflict_query = "SELECT * FROM `products` WHERE ('$p_start_time' < end_time AND '$p_end_time' > start_time)";
        $conflict_result = mysqli_query($conn, $conflict_query);

        if (mysqli_num_rows($conflict_result) > 0) {
            $message[] = 'Error: Course schedule conflicts with an existing course.';
        } else {
            $insert_query = mysqli_query($conn, "INSERT INTO `products`(name, price, image, description, start_time, end_time) VALUES('$p_name', '$p_price', '$p_image', '$p_description', '$p_start_time', '$p_end_time')");

            if ($insert_query) {
                move_uploaded_file($p_image_tmp_name, $p_image_folder);
                $message[] = 'Course added successfully with schedule.';
                $message_type = 'success';
            } else {
                $message[] = 'Could not add the course.';
            }
        }
    }
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_query = mysqli_query($conn, "DELETE FROM `products` WHERE id = $delete_id ") or die('query failed');
   if($delete_query){
      $message[] = 'Course has been deleted successfully.';
      $message_type = 'success';
   }else{
      $message[] = 'Course could not be deleted.';
   };
};

$total_courses = mysqli_query($conn, "SELECT COUNT(*) as count FROM `products`");
$total_courses_count = mysqli_fetch_assoc($total_courses)['count'];
$total_instructors = mysqli_query($conn, "SELECT COUNT(*) as count FROM `instructors`");
$total_instructors_count = mysqli_fetch_assoc($total_instructors)['count'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - M & S Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-6 text-2xl font-bold border-b border-gray-700">
                M & S <span class="text-indigo-400">Admin</span>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="#" class="flex items-center px-4 py-2 rounded-lg bg-gray-700 text-white"><i class="fas fa-tachometer-alt mr-3"></i>Dashboard</a>
                <a href="add_instructor.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-user-plus mr-3"></i>Instructors</a>
                <a href="index.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-home mr-3"></i>View Site</a>
                <a href="logout.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-sign-out-alt mr-3"></i>Logout</a>
            </nav>
        </aside>

        <main class="flex-1 p-6 md:p-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>

            <?php
            if (!empty($message)) {
                $bg_color = ($message_type === 'success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                foreach ($message as $msg) {
                    echo '<div class="mb-6 p-4 rounded-lg ' . $bg_color . '">' . htmlspecialchars($msg) . '</div>';
                }
            }
            ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-gray-500 text-sm font-medium">Total Courses</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $total_courses_count; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-gray-500 text-sm font-medium">Total Instructors</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $total_instructors_count; ?></p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 md:p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Add a New Course</h2>
                <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                    <input type="text" name="p_name" placeholder="Enter course name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    <input type="number" name="p_price" min="0" placeholder="Enter course price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    <textarea name="p_description" placeholder="Enter course description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required></textarea>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="p_start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="datetime-local" id="p_start_time" name="p_start_time" class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label for="p_end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="datetime-local" id="p_end_time" name="p_end_time" class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                    </div>

                    <div>
                        <label for="p_image" class="block text-sm font-medium text-gray-700">Course Image</label>
                        <input type="file" id="p_image" name="p_image" accept="image/png, image/jpg, image/jpeg" class="w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                    </div>

                    <button type="submit" name="add_product" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition">Add Course</button>
                </form>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Manage Courses</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Course</th>
                                <th scope="col" class="px-6 py-3">Price</th>
                                <th scope="col" class="px-6 py-3">Schedule Start</th>
                                <th scope="col" class="px-6 py-3">Schedule End</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $select_products = mysqli_query($conn, "SELECT * FROM `products`");
                            if(mysqli_num_rows($select_products) > 0){
                               while($row = mysqli_fetch_assoc($select_products)){
                            ?>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900 flex items-center">
                                    <img src="uploaded_img/<?php echo $row['image']; ?>" class="h-10 w-10 rounded-full mr-4 object-cover" alt="">
                                    <span><?php echo $row['name']; ?></span>
                                </td>
                                <td class="px-6 py-4">$<?php echo $row['price']; ?>/-</td>
                                <td class="px-6 py-4"><?php echo date('M d, Y - h:i A', strtotime($row['start_time'])); ?></td>
                                <td class="px-6 py-4"><?php echo date('M d, Y - h:i A', strtotime($row['end_time'])); ?></td>
                                <td class="px-6 py-4">
                                    <a href="admin.php?edit=<?php echo $row['id']; ?>" class="font-medium text-indigo-600 hover:underline mr-4"><i class="fas fa-edit"></i></a>
                                    <a href="admin.php?delete=<?php echo $row['id']; ?>" class="font-medium text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this course?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php
                               };
                            } else {
                               echo "<tr><td colspan='5' class='text-center py-4'>No courses added yet.</td></tr>";
                            };
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
