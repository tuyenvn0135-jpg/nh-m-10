<?php
// 1. ÉP HOSTING PHẢI HIỆN LỖI (Không cho phép ra trang trắng hay cắt cụt trang nữa)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$msg = "";
$username = "";
$email = "";
$fullname = "";
$phone = "";
$address = "";

// 2. XỬ LÝ LOGIC KHI SUBMIT FORM
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        require_once('../config.php');

        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $raw_password = isset($_POST['password']) ? $_POST['password'] : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $address = isset($_POST['address']) ? trim($_POST['address']) : '';

        // Định dạng kiểm tra bảo mật
        $password_regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
        $phone_regex = '/^(03|05|07|08|09)[0-9]{8}$/';

        if (empty($username) || empty($raw_password) || empty($fullname) || empty($email)) {
            $msg = "❌ Vui lòng điền đầy đủ các trường bắt buộc (*)!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = "❌ Định dạng email không hợp lệ!";
        } elseif (!preg_match($password_regex, $raw_password)) {
            $msg = "❌ Mật khẩu quá yếu! Yêu cầu ít nhất 8 ký tự, có 1 chữ HOA, 1 chữ thường và 1 chữ số.";
        } elseif (!empty($phone) && !preg_match($phone_regex, $phone)) {
            $msg = "❌ Số điện thoại không đúng định dạng VN (Phải có 10 số, bắt đầu bằng 03, 05, 07, 08, 09)!";
        } else {
            // Kiểm tra trùng lặp
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                $msg = "❌ Tên đăng nhập hoặc Email đã tồn tại!";
            } else {
                $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);
                $ins = $conn->prepare("INSERT INTO users (username, password, email, fullname, phone, address, role) VALUES (?, ?, ?, ?, ?, ?, 0)");
                $ins->bind_param("ssssss", $username, $hashed_password, $email, $fullname, $phone, $address);
                
                if ($ins->execute()) {
                    $msg = "✔️ Đăng ký thành công! <a href='login.php' class='text-blue-600 underline font-bold'>Đăng nhập ngay</a>";
                    $username = $email = $fullname = $phone = $address = ""; // Xóa form
                } else {
                    $msg = "❌ Lỗi kết nối database, vui lòng thử lại.";
                }
            }
        }
    } catch (Exception $e) {
        $msg = "⚠️ Hệ thống gặp lỗi: " . $e->getMessage();
    }
}

// Cho dù đoạn code trên có lỗi hay không, phần HTML phía dưới VẪN PHẢI CHẠY KHÔNG BỊ CẮT KHÚC
include_once 'header.php';
?>

<div class="container mx-auto px-4 py-12 max-w-md">
    <div class="bg-white p-8 rounded-xl shadow-sm border">
        <h2 class="text-2xl font-black text-center text-gray-800 mb-6">TẠO TÀI KHOẢN CẦN THỦ</h2>
        
        <?php if(!empty($msg)): ?>
            <div class="text-xs mb-4 p-3 rounded bg-blue-50 text-blue-900 leading-relaxed"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4 text-xs font-medium">
            <div>
                <label class="block text-gray-500 mb-1">Tên đăng nhập *</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm">
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Mật khẩu *</label>
                <input type="password" name="password" required class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm">
                <p class="text-[10px] text-gray-400 mt-1">Yêu cầu: Tối thiểu 8 ký tự, có 1 chữ hoa, 1 chữ thường và 1 chữ số.</p>
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Họ và tên *</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm">
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Email *</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm">
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Số điện thoại</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="Ví dụ: 0912345678" class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm">
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Địa chỉ giao đồ câu mặc định</label>
                <textarea name="address" rows="2" class="w-full border p-2.5 rounded focus:ring-1 focus:ring-[#0A2540] outline-none text-sm"><?php echo htmlspecialchars($address); ?></textarea>
            </div>
            
            <button type="submit" class="w-full bg-[#0A2540] hover:bg-slate-800 text-white font-bold py-3 rounded uppercase tracking-wider text-xs block content-center">ĐĂNG KÝ THÀNH VIÊN</button>
        </form>
        <p class="text-center text-[11px] text-gray-400 mt-4">Đã có tài khoản? <a href="login.php" class="text-[#FF9F1C] font-bold underline">Đăng nhập</a></p>
    </div>
</div>

<?php include_once 'footer.php'; ?>