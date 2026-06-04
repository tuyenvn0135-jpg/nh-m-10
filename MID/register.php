<?php
require_once('../config.php');
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = trim($_POST['email']);
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Kiểm tra trùng lặp tài khoản
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $msg = "❌ Tên đăng nhập hoặc Email đã tồn tại trên hệ thống!";
    } else {
        // Chèn thành viên mới vào DB
        $ins = $conn->prepare("INSERT INTO users (username, password, email, fullname, phone, address, role) VALUES (?, ?, ?, ?, ?, ?, 0)");
        $ins->bind_param("ssssss", $username, $password, $email, $fullname, $phone, $address);
        if ($ins->execute()) {
            $msg = "✔️ Đăng ký tài khoản thành công! <a href='login.php' class='text-blue-600 underline font-bold'>Đăng nhập ngay</a>";
        } else {
            $msg = "❌ Đã có lỗi xảy ra vui lòng thử lại.";
        }
    }
}
include_once 'header.php';
?>

<div class="container mx-auto px-4 py-12 max-w-md">
    <div class="bg-white p-8 rounded-xl shadow-sm border">
        <h2 class="text-2xl font-black text-center text-gray-800 mb-6">TẠO TÀI KHOẢN CẦN THỦ</h2>
        
        <?php if(!empty($msg)) echo "<div class='text-xs mb-4 p-3 rounded bg-blue-50 text-blue-900'>$msg</div>"; ?>

        <form action="<?php echo $base_url; ?>MID/register.php" method="POST" class="space-y-4 text-xs font-medium">
            <div>
                <label class="block text-gray-500 mb-1">Tên đăng nhập *</label>
                <input type="text" name="username" required class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm">
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Mật khẩu *</label>
                <input type="password" name="password" required class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm">
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Họ và tên *</label>
                <input type="text" name="fullname" required class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm">
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Email *</label>
                <input type="email" name="email" required class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm">
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Số điện thoại</label>
                <input type="text" name="phone" class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm">
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Địa chỉ giao đồ câu mặc định</label>
                <textarea name="address" rows="2" class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm"></textarea>
            </div>
            <button type="submit" class="w-full bg-[#0A2540] hover:bg-slate-800 text-white font-bold py-3 rounded uppercase tracking-wider text-xs">Đăng Ký Thành Viên</button>
        </form>
        <p class="text-center text-[11px] text-gray-400 mt-4">Đã có tài khoản? <a href="login.php" class="text-[#FF9F1C] font-bold underline">Đăng nhập</a></p>
    </div>
</div>

<?php include_once 'footer.php'; ?>