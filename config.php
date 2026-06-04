<?php
$host = "localhost";
$user = "root";
$pass = "1234";
$dbname = "kingfisher_data";

// Kết nối ban đầu để kiểm tra/tạo database
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Kết nối database thất bại: " . $conn->connect_error);
}

// Tự động chọn database nếu đã tồn tại
$conn->select_db($dbname);
$conn->set_charset("utf8mb4");
$base_url = "http://localhost/kingfisher/";
?>