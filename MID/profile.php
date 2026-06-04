<?php
require_once('../config.php');
if (!isset($_SESSION)) { 
    session_start(); 
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) { 
    header("Location: MID/login.php"); 
    exit; 
}

$user_id = $_SESSION['user_id'];
$error_msg = "";
$success_msg = "";

// 1. XỬ LÝ CẬP NHẬT THÔNG TIN CÁ NHÂN & ĐỔI MẬT KHẨU
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $old_pass = trim($_POST['old_password']);
    $new_pass = trim($_POST['new_password']);

    // Cập nhật thông tin cơ bản trước
    $stmt_update = $conn->prepare("UPDATE users SET fullname = ?, email = ?, phone = ?, address = ? WHERE id = ?");
    $stmt_update->bind_param("ssssi", $fullname, $email, $phone, $address, $user_id);
    
    if ($stmt_update->execute()) {
        $_SESSION['fullname'] = $fullname; // Cập nhật lại tên hiển thị trên header
        $success_msg = "✅ Cập nhật thông tin cá nhân thành công!";
    } else {
        $error_msg = "❌ Không thể cập nhật thông tin.";
    }

    // Nếu người dùng muốn đổi mật khẩu (khi nhập mật khẩu cũ)
    if (!empty($old_pass) && !empty($new_pass)) {
        $stmt_pass = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_pass->bind_param("i", $user_id);
        $stmt_pass->execute();
        $curr_user = $stmt_pass->get_result()->fetch_assoc();

        if (password_verify($old_pass, $curr_user['password'])) {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt_change = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt_change->bind_param("si", $new_hash, $user_id);
            $stmt_change->execute();
            $success_msg = "✅ Đã cập nhật thông tin và thay đổi mật khẩu thành công!";
        } else {
            $error_msg = "❌ Mật khẩu cũ không chính xác. Không thể đổi mật khẩu.";
        }
    }
}

// 2. LẤY THÔNG TIN USER HIỆN TẠI ĐỂ HIỂN THỊ LÊN FORM
$stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_info = $stmt_user->get_result()->fetch_assoc();

// 3. LẤY LỊCH SỬ ĐƠN HÀNG CỦA USER NÀY
$orders_res = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC");

include_once 'MID/header.php';
?>

<div class="container mx-auto px-4 py-10 max-w-6xl">
    <!-- Tiêu đề trang -->
    <div class="mb-8" data-aos="fade-down">
        <h1 class="text-2xl font-black text-gray-900 uppercase">Trang Cá Nhân Cần Thủ</h1>
        <p class="text-xs text-gray-500 mt-1">Quản lý thông tin tài khoản, địa chỉ giao hàng và theo dõi trạng thái đơn hàng mua thiết bị câu cá.</p>
    </div>

    <!-- Thông báo trạng thái -->
    <?php if(!empty($error_msg)) echo "<div class='p-3 text-xs bg-red-50 text-red-700 font-bold rounded-lg mb-6 border border-red-100'>$error_msg</div>"; ?>
    <?php if(!empty($success_msg)) echo "<div class='p-3 text-xs bg-green-50 text-green-700 font-bold rounded-lg mb-6 border border-green-100'>$success_msg</div>"; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-xs font-medium">
        
        <!-- CỘT 1 & 2: FORM THÔNG TIN CÁ NHÂN -->
        <div class="lg:col-span-1 bg-white p-6 rounded-xl border shadow-sm h-fit" data-aos="fade-right">
            <h2 class="text-sm font-black text-gray-800 border-b pb-3 mb-4 uppercase flex items-center gap-2">👤 Thông tin tài khoản</h2>
            
            <form action="MID/profile.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-500 mb-1">Tên đăng nhập (Không thể sửa)</label>
                    <input type="text" value="<?php echo htmlspecialchars($user_info['username']); ?>" disabled class="w-full border p-2.5 rounded bg-gray-100 text-gray-500 outline-none cursor-not-allowed font-semibold">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Họ và tên của bạn *</label>
                    <input type="text" name="fullname" required value="<?php echo htmlspecialchars($user_info['fullname']); ?>" class="w-full border p-2.5 text-sm rounded outline-none focus:border-blue-500">
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

                <!-- Khu vực đổi mật khẩu nếu muốn -->
                <div class="border-t pt-4 mt-4 bg-gray-50/50 p-3 rounded-lg space-y-3">
                    <span class="block font-bold text-gray-700 text-[11px] uppercase tracking-wider">🔒 Đổi mật khẩu bảo mật (Để trống nếu không đổi)</span>
                    <div>
                        <label class="block text-gray-400 mb-1">Mật khẩu cũ hiện tại</label>
                        <input type="password" name="old_password" placeholder="••••••••" class="w-full border p-2 rounded text-sm bg-white outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-1">Mật khẩu mới</label>
                        <input type="password" name="new_password" placeholder="Tối thiểu 6 ký tự" class="w-full border p-2 rounded text-sm bg-white outline-none">
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#0A2540] hover:bg-slate-800 text-white font-black py-3 rounded-lg text-xs uppercase tracking-wider transition shadow-sm">💾 Lưu tất cả thay đổi</button>
            </form>
        </div>

        <!-- CỘT 3: LỊCH SỬ ĐƠN HÀNG ĐÃ MUA -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border shadow-sm" data-aos="fade-left">
            <h2 class="text-sm font-black text-gray-800 border-b pb-3 mb-4 uppercase flex items-center gap-2">📦 Lịch sử mua hàng thiết bị câu</h2>
            
            <?php if($orders_res && $orders_res->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-500 border-b">
                            <tr>
                                <th class="p-3">Mã Đơn</th>
                                <th class="p-3">Ngày đặt</th>
                                <th class="p-3">Địa chỉ nhận</th>
                                <th class="p-3">Tổng tiền</th>
                                <th class="p-3">Thanh toán</th>
                                <th class="p-3 text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-gray-600">
                            <?php while($order = $orders_res->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50/50">
                                    <td class="p-3 font-bold text-gray-900">#KF-<?php echo $order['id']; ?></td>
                                    <td class="p-3 text-gray-400 font-mono"><?php echo date('d/m/Y', strtotime($order['created_at'] ?? date('Y-m-d'))); ?></td>
                                    <td class="p-3 max-w-[180px] truncate"><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                                    <td class="p-3 font-bold text-red-600"><?php echo number_format($order['total_price'], 0, ',', '.'); ?>đ</td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold <?php echo ($order['payment_status'] == 'Chưa thanh toán') ? 'bg-orange-50 text-orange-600' : 'bg-green-50 text-green-600'; ?>">
                                            <?php echo $order['payment_method'] ?? 'COD'; ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <?php 
                                        $status = $order['status'];
                                        $class = "bg-gray-100 text-gray-600";
                                        if($status == 'Chờ xử lý') $class = "bg-amber-50 text-amber-600 border border-amber-100";
                                        if($status == 'Đang giao') $class = "bg-blue-50 text-blue-600 border border-blue-100";
                                        if($status == 'Đã hoàn thành') $class = "bg-green-50 text-green-700 border border-green-200";
                                        if($status == 'Đã hủy') $class = "bg-red-50 text-red-600 border border-red-100";
                                        ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black <?php echo $class; ?>">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-16 border border-dashed rounded-xl">
                    <p class="text-gray-400 text-sm">Bạn chưa thực hiện đơn đặt hàng đồ câu nào trên KingFisher.</p>
                    <a href="index.php" class="inline-block mt-3 bg-[#0A2540] text-white px-4 py-2 rounded-lg text-xs font-bold uppercase hover:bg-blue-900 transition">Sắm đồ câu ngay</a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include_once 'footer.php'; ?>