<?php
// 1. ÉP HỆ THỐNG HIỂN THỊ LỖI
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__DIR__) . '/config.php';

if (!isset($_SESSION)) { 
    session_start(); 
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

$user_id = $_SESSION['user_id'];
$error_msg = "";
$success_msg = "";
$user_info = [];
$orders = [];

try {
    // 2. XỬ LÝ CẬP NHẬT THÔNG TIN (POST)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
        $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone    = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $address  = isset($_POST['address']) ? trim($_POST['address']) : '';
        $old_pass = isset($_POST['old_password']) ? trim($_POST['old_password']) : '';
        $new_pass = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';

        // Cập nhật thông tin cơ bản
        $stmt_update = $conn->prepare("UPDATE users SET fullname = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        if ($stmt_update) {
            $stmt_update->bind_param("ssssi", $fullname, $email, $phone, $address, $user_id);
            $stmt_update->execute();
            $_SESSION['fullname'] = $fullname;
            $success_msg = "✅ Cập nhật thông tin cá nhân thành công!";
        }

        // Đổi mật khẩu nếu có nhập
        if (!empty($old_pass) && !empty($new_pass)) {
            $stmt_pass = $conn->prepare("SELECT password FROM users WHERE id = ?");
            if ($stmt_pass) {
                $stmt_pass->bind_param("i", $user_id);
                $stmt_pass->execute();
                $res_p = $stmt_pass->get_result();
                $curr_user = $res_p ? $res_p->fetch_assoc() : null;

                if ($curr_user && password_verify($old_pass, $curr_user['password'])) {
                    $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                    $stmt_change = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    if ($stmt_change) {
                        $stmt_change->bind_param("si", $new_hash, $user_id);
                        $stmt_change->execute();
                        $success_msg = "✅ Đã đổi mật khẩu thành công!";
                    }
                } else {
                    $error_msg = "❌ Mật khẩu cũ không chính xác!";
                }
            }
        }
    }

    // 3. LẤY THÔNG TIN USER HIỆN TẠI
    $stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
    if ($stmt_user) {
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $res_user = $stmt_user->get_result();
        if ($res_user) {
            $user_info = $res_user->fetch_assoc();
        }
    }

    // 4. LẤY LỊCH SỬ ĐƠN HÀNG (Bọc riêng để nếu lỗi bảng orders thì form thông tin vẫn chạy)
    try {
        $orders_res = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC");
        if ($orders_res) {
            while ($row = $orders_res->fetch_assoc()) {
                $orders[] = $row;
            }
        }
    } catch (Throwable $e) {
        $error_msg = "⚠️ Hệ thống chưa thể tải lịch sử đơn hàng (Lỗi: " . $e->getMessage() . ")";
    }

} catch (Throwable $e) {
    // Bẫy lỗi nghiêm trọng nhất và in thẳng ra màn hình chặn lỗi 500 bừa bãi
    die("<div style='padding:25px; background:#fee2e2; color:#991b1b; font-family:sans-serif; border-radius:8px; margin:30px; border:1px solid #fca5a5;'>
            <h3 style='margin-top:0;'>🚫 Phát hiện lỗi hệ thống (PHP Crash):</h3>
            <p><b>Nội dung lỗi:</b> " . $e->getMessage() . "</p>
            <p><b>Tại file:</b> " . $e->getFile() . " (Dòng số " . $e->getLine() . ")</p>
            <p>👉 Hãy chụp lại màn hình này gửi cho mình để biết chính xác database của bạn đang thiếu trường nào nhé!</p>
         </div>");
}

// Gọi file header
include_once 'header.php';
?>

<div class="container mx-auto px-4 py-10 max-w-6xl">
    <div class="mb-8">
        <h1 class="text-2xl font-black text-gray-900 uppercase">Trang Cá Nhân Cần Thú</h1>
        <p class="text-xs text-gray-500 mt-1">Quản lý thông tin tài khoản, địa chỉ giao hàng và theo dõi trạng thái đơn hàng mua thiết bị câu cá.</p>
    </div>

    <?php if(!empty($error_msg)) echo "<div class='p-3 text-xs bg-red-50 text-red-700 font-bold rounded-lg mb-6 border border-red-100'>$error_msg</div>"; ?>
    <?php if(!empty($success_msg)) echo "<div class='p-3 text-xs bg-green-50 text-green-700 font-bold rounded-lg mb-6 border border-green-100'>$success_msg</div>"; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-xs font-medium">
        
        <div class="lg:col-span-1 bg-white p-6 rounded-xl border shadow-sm h-fit">
            <h2 class="text-sm font-black text-gray-800 border-b pb-3 mb-4 uppercase flex items-center gap-2">👤 Thông tin tài khoản</h2>
            
            <form action="profile.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-500 mb-1">Tên đăng nhập (Không thể sửa)</label>
                    <input type="text" value="<?php echo htmlspecialchars($user_info['username'] ?? ''); ?>" disabled class="w-full border p-2.5 rounded bg-gray-100 text-gray-500 outline-none cursor-not-allowed font-semibold">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Họ và tên của bạn *</label>
                    <input type="text" name="fullname" required value="<?php echo htmlspecialchars($user_info['fullname'] ?? ''); ?>" class="w-full border p-2.5 text-sm rounded outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Địa chỉ Email *</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>" class="w-full border p-2.5 text-sm rounded outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Số điện thoại nhận hàng *</label>
                    <input type="text" name="phone" required value="<?php echo htmlspecialchars($user_info['phone'] ?? ''); ?>" class="w-full border p-2.5 text-sm rounded outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Địa chỉ giao hàng mặc định</label>
                    <textarea name="address" rows="2" class="w-full border p-2.5 text-sm rounded outline-none focus:border-blue-500"><?php echo htmlspecialchars($user_info['address'] ?? ''); ?></textarea>
                </div>

                <div class="border-t pt-4 mt-4 bg-gray-50/50 p-3 rounded-lg space-y-3">
                    <span class="block font