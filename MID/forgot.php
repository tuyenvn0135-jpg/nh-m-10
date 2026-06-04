<?php
require_once('../config.php');
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $token = bin2hex(random_bytes(16));
        $stmt_update = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt_update->bind_param("ss", $token, $email);
        $stmt_update->execute();
        
        $msg = "📧 Hệ thống giả định: Mã link reset mật khẩu của bạn là: <br><a href='MID/forgot.php?token=$token' class='text-blue-600 underline font-mono font-bold'>MID/forgot.php?token=$token</a>";
    } else { $msg = "❌ Không tìm thấy tài khoản liên kết với Email này."; }
}

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_pass'])) {
        $new_p = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
        $stmt->bind_param("ss", $new_p, $token);
        $stmt->execute();
        die("<script>alert('Đổi mật khẩu thành công bằng Token!'); window.location.href='MID/login.php';</script>");
    }
}
include 'MID/header.php';
?>
<div class="container mx-auto px-4 py-12 max-w-md">
    <div class="bg-white p-6 rounded-xl border shadow-sm text-xs font-medium">
        <h2 class="text-base font-black text-gray-900 border-b pb-3 mb-4 uppercase">🔑 Khôi Phục Mật Khẩu</h2>
        <?php if(!empty($msg)) echo "<div class='p-3 bg-blue-50 text-blue-900 rounded mb-4'>$msg</div>"; ?>
        
        <?php if(!isset($_GET['token'])): ?>
            <form action="" method="POST" class="space-y-4">
                <input type="email" name="email" required placeholder="Nhập email chính chủ của bạn" class="w-full border p-2 text-sm rounded outline-none">
                <button type="submit" class="w-full bg-[#0A2540] text-white font-bold py-2 rounded uppercase">Gửi link xác thực</button>
            </form>
        <?php else: ?>
            <form action="" method="POST" class="space-y-4">
                <input type="password" name="new_pass" required placeholder="Nhập mật khẩu mới thay thế" class="w-full border p-2 text-sm rounded outline-none">
                <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 rounded uppercase">Đặt lại mật khẩu</button>
            </form>
        <?php endif; ?>
    </div>
</div>