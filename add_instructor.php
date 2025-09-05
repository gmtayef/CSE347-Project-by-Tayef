<?php
@include 'config.php';

session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

$message = '';
$message_type = '';

if (isset($_POST['add_instructor'])) {
    $i_id = mysqli_real_escape_string($conn, $_POST['i_id']);
    $i_name = mysqli_real_escape_string($conn, $_POST['i_name']);
    $i_email = mysqli_real_escape_string($conn, $_POST['i_email']);
    $i_expertise = mysqli_real_escape_string($conn, $_POST['i_expertise']);
    
    $i_image = $_FILES['i_image']['name'];
    $i_image_tmp_name = $_FILES['i_image']['tmp_name'];
    $i_image_size = $_FILES['i_image']['size'];

    $instructor_image_folder = 'instructor_img/';
    if (!is_dir($instructor_image_folder)) {
        mkdir($instructor_image_folder, 0777, true);
    }
    $i_image_folder = $instructor_image_folder . $i_image;

    if (empty($i_id) || empty($i_name) || empty($i_email) || empty($i_expertise) || empty($i_image)) {
        $message = 'Please fill out all fields.';
        $message_type = 'error';
    } elseif ($i_image_size > 2000000) {
        $message = 'Image size is too large.';
        $message_type = 'error';
    } else {
        $check_id_query = mysqli_query($conn, "SELECT * FROM `instructors` WHERE instructor_id = '$i_id'");
        if (mysqli_num_rows($check_id_query) > 0) {
            $message = 'An instructor with this ID already exists.';
            $message_type = 'error';
        } else {
            $insert_query = mysqli_query($conn, "INSERT INTO `instructors`(instructor_id, name, email, expertise, image) VALUES('$i_id', '$i_name', '$i_email', '$i_expertise', '$i_image')");
            if ($insert_query) {
                move_uploaded_file($i_image_tmp_name, $i_image_folder);
                $message = 'Instructor added successfully!';
                $message_type = 'success';
            } else {
                $message = 'Could not add the instructor.';
                $message_type = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Instructor - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex items-center justify-center">
    <div class="max-w-4xl w-full bg-white p-8 rounded-2xl shadow-lg">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Add New Instructor</h1>
            <p class="text-gray-500">Fill in the details below to add a new instructor to the platform.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="i_id" class="block text-sm font-medium text-gray-700">Instructor ID</label>
                        <input type="text" id="i_id" name="i_id" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label for="i_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" id="i_name" name="i_name" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label for="i_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" id="i_email" name="i_email" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label for="i_expertise" class="block text-sm font-medium text-gray-700">Area of Expertise</label>
                        <input type="text" id="i_expertise" name="i_expertise" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label for="i_image" class="block text-sm font-medium text-gray-700">Profile Photo</label>
                        <input type="file" id="i_image" name="i_image" accept="image/png, image/jpg, image/jpeg" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" onchange="previewImage(event)" required>
                    </div>
                    <button type="submit" name="add_instructor" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-md hover:bg-indigo-700 transition">Add Instructor</button>
                </form>
            </div>

            <div class="bg-gray-50 p-8 md:p-12 flex flex-col items-center justify-center">
                <div class="w-48 h-48 rounded-full bg-gray-200 mb-6 flex items-center justify-center overflow-hidden shadow-inner">
                    <img id="imagePreview" src="https://placehold.co/192x192/EFEFEF/AFAFAF?text=Photo" alt="Image Preview" class="w-full h-full object-cover">
                </div>
                <h3 class="text-lg font-semibold text-gray-700">Photo Preview</h3>
                <p class="text-gray-500 text-center mt-2 text-sm">The selected instructor's profile photo will appear here. Recommended size: 400x400px.</p>
            </div>

        </div>
    </div>
    <div class="text-center mt-6">
        <a href="admin.php" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">← Back to Admin Dashboard</a>
    </div>
</div>

<script>
    const previewImage = event => {
        const imagePreview = document.getElementById('imagePreview');
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
</body>
</html>
