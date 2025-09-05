<?php
@include 'config.php';

$message = [];

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = md5($_POST['password']);
    $cpass = md5($_POST['cpassword']);
    $user_type = $_POST['user_type']; // Get user type from the form

    $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email'");

    if (mysqli_num_rows($select) > 0) {
        $message[] = 'User with this email already exists!';
    } else {
        if ($pass != $cpass) {
            $message[] = 'Passwords do not match!';
        } else {
            $insert = mysqli_query($conn, "INSERT INTO `user_form`(name, email, password, user_type) VALUES('$name','$email','$pass','$user_type')");
            if ($insert) {
                $message[] = 'Registered successfully! Please login now.';
                header('location:login_form.php');
                exit();
            } else {
                $message[] = 'Registration failed!';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - M & S Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #0a0a0a;
        }
        .form-container {
            background: #111827;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="form-container rounded-2xl shadow-2xl p-8 md:p-10">
                <h3 class="text-3xl font-bold text-center text-white mb-6">Register Now</h3>
                <?php
                if (!empty($message)) {
                    foreach ($message as $msg) {
                        echo '<div class="mb-4 p-3 rounded-lg bg-red-500/20 text-red-300 text-center">' . htmlspecialchars($msg) . '</div>';
                    }
                }
                ?>
                <form action="" method="post" class="space-y-6">
                    <input type="text" name="name" required placeholder="Enter your name" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="email" name="email" required placeholder="Enter your email" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="password" name="password" required placeholder="Enter your password" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="password" name="cpassword" required placeholder="Confirm your password" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <select name="user_type" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="user">Student</option>
                        <option value="instructor">Instructor</option>
                    </select>
                    <button type="submit" name="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition-all duration-300 shadow-lg">Register</button>
                    <p class="text-center text-gray-400">Already have an account? <a href="login_form.php" class="text-indigo-400 hover:underline">Login now</a></p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
