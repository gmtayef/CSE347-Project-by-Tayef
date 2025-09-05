<?php

include 'config.php';
session_start();

$message = [];

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = md5($_POST['password']);

  
    $select_users = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email' AND password = '$pass'");

    if (mysqli_num_rows($select_users) > 0) {
        $row = mysqli_fetch_assoc($select_users);
        if ($row['user_type'] == 'admin') {
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_id'] = $row['id'];
            header('location:admin_panel.php');
            exit();
        } elseif ($row['user_type'] == 'user') {
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_id'] = $row['id'];
            header('location:index.php');
            exit();
        }
    } else {
    
        $select_instructors = mysqli_query($conn, "SELECT * FROM `instructors` WHERE email = '$email' AND password = '$pass'");
        
        if (mysqli_num_rows($select_instructors) > 0) {
            $row = mysqli_fetch_assoc($select_instructors);
            $_SESSION['instructor_name'] = $row['name'];
            $_SESSION['instructor_id'] = $row['id'];
            header('location:instructor_panel.php');
            exit();
        } else {
            $message[] = 'Incorrect email or password!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - M & S Learning</title>
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
                <h3 class="text-3xl font-bold text-center text-white mb-6">Login Now</h3>
                <?php
                if (!empty($message)) {
                    foreach ($message as $msg) {
                        echo '<div class="mb-4 p-3 rounded-lg bg-red-500/20 text-red-300 text-center">' . htmlspecialchars($msg) . '</div>';
                    }
                }
                ?>
                <form action="" method="post" class="space-y-6">
                    <input type="email" name="email" required placeholder="Enter your email" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="password" name="password" required placeholder="Enter your password" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" name="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition-all duration-300 shadow-lg">Login</button>
                    <p class="text-center text-gray-400">Don't have an account? <a href="register_form.php" class="text-indigo-400 hover:underline">Register now</a></p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
