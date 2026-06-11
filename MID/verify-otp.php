<?php
session_start();
require_once('../config.php');
$msg = "";

if (!isset($_SESSION['verify_email'])) {
    header("Location: register.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_otp = trim($_POST['otp']);
    $email = $_SESSION['verify_email'];

    // Kiểm tra mã OTP khớp với database không
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND otp_code = ?");
    $stmt->bind_param("ss", $email, $user_otp);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        // Khớp mã -> Kích hoạt tài khoản thành công (Cập nhật is_verified = 1)
        $update = $conn->prepare("UPDATE users SET is_verified = 1, otp_code = NULL WHERE email = ?");
        $update->bind_param("s", $email);
        $update->execute();

        unset($_SESSION['verify_email']);
        $msg = "✔️ Xác thực thành công! <a href='login.php' class='font-bold underline text-blue-600'>Đăng nhập ngay</a>";
    } else {
        $msg = "❌ Mã OTP không chính xác, vui lòng kiểm tra lại hòm thư!";
    }
}
?>

<?php include_once 'header.php'; ?>
<div class="container mx-auto px-4 py-12 max-w-md">
    <div class="bg-white p-8 rounded-xl shadow-sm border text-center">
        <h2 class="text-xl font-black mb-4">NHẬP MÃ XÁC MINH OTP</h2>
        <p class="text-xs text-gray-500 mb-4">Hệ thống đã gửi một mã OTP 6 số đến email: <b><?php echo htmlspecialchars($_SESSION['verify_email'] ?? ''); ?></b></p>
        
        <?php if(!empty($msg)) echo "<p class='text-xs mb-4 text-red-600'>$msg</p>"; ?>

        <form action="" method="POST" class="space-y-4">
            <input type="text" name="otp" maxlength="6" placeholder="------" required class="w-full border p-3 rounded text-center text-2xl tracking-[10px] font-bold outline-none focus:border-blue-900">
            <button type="submit" class="w-full bg-[#0A2540] text-white font-bold py-3 rounded text-xs uppercase">Xác Nhận Kích Hoạt</button>
        </form>
    </div>
</div>
<?php include_once 'footer.php'; ?>