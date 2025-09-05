
<?php
$conn = mysqli_connect('localhost','root','','course1_db');

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
