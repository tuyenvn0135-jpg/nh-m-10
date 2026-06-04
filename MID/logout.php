<?php
// Khởi động session để hệ thống nhận diện phiên đăng nhập hiện tại
if (!isset($_SESSION)) {
    session_start();
}

// Xóa toàn bộ biến session (thông tin user, quyền admin, giỏ hàng...)
$_SESSION = array();

// Hủy hoàn toàn phiên làm việc của Session trên máy chủ
session_destroy();

// Tự động chuyển hướng người dùng quay trở lại trang chủ sau khi thoát thành công
header("Location: ../index.php");
exit;
?>