<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login_form.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $course_id = $_POST['course_id'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = 'txn_' . uniqid(); // Simulate a transaction ID

    $insert_purchase = "INSERT INTO `purchases` (user_id, course_id, payment_method, transaction_id) VALUES ('$user_id', '$course_id', '$payment_method', '$transaction_id')";
    
    if(mysqli_query($conn, $insert_purchase)) {
        // Redirect to student panel with a success message
        header('location:student_panel.php?purchase=success');
        exit();
    } else {
        // Handle error
        header('location:student_panel.php?purchase=failed');
        exit();
    }
} else {
    header('location:student_panel.php');
    exit();
}
?>
