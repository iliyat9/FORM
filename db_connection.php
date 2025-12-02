<?php
$servername = "localhost";
$username = "root"; // نام کاربری mysql شما
$password = ""; // رمز عبور mysql شما
$dbname = "school"; // نام دیتابیس

// ایجاد اتصال به دیتابیس
$conn = new mysqli($servername, $username, $password, $dbname);

// چک کردن اتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
