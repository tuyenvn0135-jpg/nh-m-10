<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__DIR__) . '/config.php';

if (!isset($_SESSION)) { 
    session_start(); 
}

$error_msg = "";
$success_msg = "";
$step = 1; // Bước 1: Xác minh thông tin, Bước 2: Nhập mật khẩu mới

// Xử lý khi bấm nút ở các bước
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // GIAI ĐOẠN 1: XÁC MINH BAO GỒM USERNAME + EMAIL + PHONE
    if (isset($_POST['action_verify'])) {
        $username = trim($_POST['username']);
        $email    = trim($_POST['email']);
        $phone    = trim($_POST['phone']);

        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND email = ? AND phone = ?");
        $stmt->bind_param("sss", $username, $email, $phone);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $user_found = $res->fetch_assoc();
            $_SESSION['reset_user_id'] = $user_found['id']; // Lưu tạm id vào session bảo mật
            $step = 2; // Chuyển sang giao diện đổi mật khẩu
        } else {
            $error_msg = "❌ Thông tin tài khoản, email hoặc số điện thoại không khớp với hệ thống!";
        }
    }

    // GIAI ĐOẠN 2: THỰC HIỆN ĐỔI MẬT KHẨU MỚI
    if (isset($_POST['action_reset'])) {
        $new_pass = trim($_POST['new_password']);
        $confirm_pass = trim($_POST['confirm_password']);

        if (empty($new_pass) || strlen($new_pass) < 6) {
            $error_msg = "❌ Mật khẩu mới phải có độ dài từ 6 ký tự trở lên.";
            $step = 2;
        } elseif ($new_pass !== $confirm_pass) {
            $error_msg = "❌ Xác nhận mật khẩu mới không trùng khớp.";
            $step = 2;
        } elseif (isset($_SESSION['reset_user_id'])) {
            $user_id = $_SESSION['reset_user_id'];
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);

            $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt_update->bind_param("si", $new_hash, $user_id);
            
            if ($stmt_update->execute()) {
                unset($_SESSION['reset_user_id']); // Xóa session tạm
                header("Location: login.php?reset_success=1"); // Điều hướng về login kèm thông báo thành công
                exit;
            } else {
                $error_msg = "❌ Có lỗi xảy ra trong quá trình cập nhật cơ sở dữ liệu.";
                $step = 2;
            }
        } else {
            $error_msg = "❌ Phiên làm việc đã hết hạn. Vui lòng xác minh lại.";
            $step = 1;
        }
    }
}

include_once 'header.php';
?>

<div class="container mx-auto px-4 py-16 flex justify-center items-center">
    <div class="bg-white p-8 rounded-xl border shadow-sm w-full max-w-md text-xs font-medium">
        
        <?php if($step == 1): ?>
            <h1 class="text-xl font-black text-[#0A2540] text-center uppercase tracking-wide mb-2">Khôi Phục Mật Khẩu</h1>
            <p class="text-gray-400 text-center mb-6 text-[11px]">Vui lòng điền chính xác thông tin đăng ký để đặt lại mật khẩu của bạn.</p>
            
            <?php if(!empty($error_msg)) echo "<div class='p-3 bg-red-50 text-red-700 font-bold rounded-lg mb-4 border border-red-100'>$error_msg</div>"; ?>

            <form action="forgot-password.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-500 mb-1">Tên tài khoản cần lấy lại *</label>
                    <input type="text" name="username" required placeholder="Nhập tên đăng nhập của bạn..." class="w-full border p-2.5 rounded text-sm outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Địa chỉ Email đăng ký *</label>
                    <input type="email" name="email" required placeholder="example@gmail.com" class="w-full border p-2.5 rounded text-sm outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Số điện thoại đăng ký *</label>
                    <input type="text" name="phone" required placeholder="Nhập số điện thoại..." class="w-full border p-2.5 rounded text-sm outline-none focus:border-blue-500">
                </div>

                <button type="submit" name="action_verify" class="w-full bg-[#0A2540] hover:bg-slate-800 text-white font-black py-3 rounded-lg text-xs uppercase tracking-wider transition shadow-sm">🔍 Xác Minh Tài Khoản</button>
            </form>

        <?php static_get_last: else: ?>
            <h1 class="text-xl font-black text-green-600 text-center uppercase tracking-wide mb-2">Xác Minh Thành Công 🎉</h1>
            <p class="text-gray-400 text-center mb-6 text-[11px]">Hệ thống đã nhận diện được tài khoản. Hãy tạo mật khẩu mới an toàn hơn.</p>
            
            <?php if(!empty($error_msg)) echo "<div class='p-3 bg-red-50 text-red-700 font-bold rounded-lg mb-4 border border-red-100'>$error_msg</div>"; ?>

            <form action="forgot-password.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-500 mb-1">Mật khẩu mới (Tối thiểu 6 ký tự)</label>
                    <input type="password" name="new_password" required placeholder="••••••••" class="w-full border p-2.5 rounded text-sm outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Nhập lại mật khẩu mới để xác nhận</label>
                    <input type="password" name="confirm_password" required placeholder="••••••••" class="w-full border p-2.5 rounded text-sm outline-none focus:border-blue-500">
                </div>

                <button type="submit" name="action_reset" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-3 rounded-lg text-xs uppercase tracking-wider transition shadow-sm">🔑 Cập Nhật Mật Khẩu Mới</button>
            </form>
        <?php endif; ?>

        <div class="text-center mt-6 text-gray-500 border-t pt-4">
            Quay lại trang <a href="login.php" class="text-blue-600 hover:underline font-bold">Đăng Nhập</a>
        </div>
    </div>
</div>