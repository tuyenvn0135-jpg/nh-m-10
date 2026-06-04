<?php
require_once('../config.php');
if(!isset($_SESSION)) { session_start(); }
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            header("Location: ../index.php");
exit;
        } else {
            $error = "❌ Sai mật khẩu đăng nhập, vui lòng kiểm tra lại.";
        }
    } else {
        $error = "❌ Tài khoản người dùng không tồn tại trên hệ thống.";
    }
}
include_once 'header.php';
?>

<div class="container mx-auto px-4 py-20 max-w-sm">
    <div class="bg-white p-8 rounded-xl shadow-sm border">
        <h2 class="text-2xl font-black text-center text-gray-800 mb-6">ĐĂNG NHẬP HỆ THỐNG</h2>
        
        <?php if(!empty($error)) echo "<div class='text-xs mb-4 p-3 rounded bg-red-50 text-red-700'>$error</div>"; ?>

        <form action="<?php echo $base_url; ?>MID/login.php" method="POST" class="space-y-4 text-xs font-medium">
            <div>
                <label class="block text-gray-500 mb-1">Tên tài khoản</label>
                <input type="text" name="username" required class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm">
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Mật khẩu bảo mật</label>
                <input type="password" name="password" required class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm">
            </div>
            <button type="submit" class="w-full bg-[#0A2540] hover:bg-slate-800 text-white font-bold py-3 rounded uppercase tracking-wider text-xs">Vào Hệ Thống</button>
        </form>
        <p class="text-center text-[11px] text-gray-400 mt-4">Chưa có tài khoản? <a href="register.php" class="text-[#FF9F1C] font-bold underline">Đăng ký ngay</a></p>
    </div>
</div>

<?php include_once 'footer.php'; ?>