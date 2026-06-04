<?php
require_once('../config.php');
if (!isset($_SESSION)) { session_start(); }

// Kiểm tra quyền truy cập Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    die("Từ chối truy cập.");
}

// Xử lý cập nhật trạng thái đơn hàng khi Admin thay đổi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = trim($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    header("Location: toroDMIN/admin_orders.php");
    exit;
}

// Lấy danh sách đơn hàng kèm tên người đặt
$orders = $conn->query("SELECT o.*, u.fullname FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>KingFisher Admin - Quản Lý Đơn Hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 flex min-h-screen font-['Inter']">

    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between hidden md:flex">
        <div>
            <div class="p-5 bg-slate-950 text-white font-black text-xl tracking-wider border-b border-slate-800">
                👑 KINGFISHER
            </div>
            <nav class="p-4 space-y-2 text-sm font-medium">
               <a href="<?php echo $base_url; ?>toroDMIN/admin.php" class="block bg-slate-800 text-white px-4 py-3 rounded transition">📦 Quản lý Sản phẩm</a>
<a href="<?php echo $base_url; ?>toroDMIN/admin_orders.php" class="block hover:bg-slate-800 hover:text-white px-4 py-3 rounded transition">📋 Quản lý Đơn hàng</a>
<a href="<?php echo $base_url; ?>toroDMIN/admin_users.php" class="block hover:bg-slate-800 hover:text-white px-4 py-3 rounded transition">👥 Quản lý Thành viên</a>
<a href="<?php echo $base_url; ?>index.php" class="block text-orange-400 hover:underline px-4 py-3 pt-6">← Quay lại giao diện chính</a>
            </nav>
        </div>
    </aside>

    <main class="flex-1 p-8 text-xs">
        <div class="mb-6">
            <h1 class="text-2xl font-black text-gray-800">📋 QUẢN LÝ TIẾN TRÌNH ĐƠN HÀNG</h1>
            <p class="text-gray-500 mt-1">Cập nhật trạng thái đóng gói ống nhựa PVC bảo vệ cần và lộ trình giao hàng cồng kềnh.</p>
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold tracking-wider border-b">
                        <tr>
                            <th class="p-4">Mã Đơn</th>
                            <th class="p-4">Tên Cần Thủ</th>
                            <th class="p-4">Ngày Đặt Hoạt Động</th>
                            <th class="p-4">Địa Chỉ Giao Hàng</th>
                            <th class="p-4">Tổng Giá Trị</th>
                            <th class="p-4">Trạng Thái Xử Lý</th>
                            <th class="p-4 text-center">Cập Nhật</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium text-gray-600">
                        <?php if ($orders && $orders->num_rows > 0): ?>
                            <?php while($row = $orders->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-bold text-blue-600">#<?php echo $row['id']; ?></td>
                                <td class="p-4 font-bold text-gray-900"><?php echo htmlspecialchars($row['fullname']); ?></td>
                                <td class="p-4 text-gray-400"><?php echo date('d/m/Y H:i', strtotime($row['order_date'])); ?></td>
                                <td class="p-4 max-w-xs truncate"><?php echo htmlspecialchars($row['shipping_address']); ?></td>
                                <td class="p-4 font-bold text-slate-900"><?php echo number_format($row['total_price'], 0, ',', '.'); ?>đ</td>
                                <td class="p-4">
                                    <?php 
                                    $status_class = "bg-gray-100 text-gray-800";
                                    if($row['status'] == 'Đang đóng gói PVC') $status_class = "bg-orange-100 text-orange-800";
                                    if($row['status'] == 'Đang giao') $status_class = "bg-blue-100 text-blue-800";
                                    if($row['status'] == 'Đã giao') $status_class = "bg-green-100 text-green-800";
                                    if($row['status'] == 'Đã hủy') $status_class = "bg-red-100 text-red-800";
                                    ?>
                                    <span class="<?php echo $status_class; ?> px-2.5 py-1 rounded-full font-bold text-[10px]">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <form action="toroDMIN/admin_orders.php" method="POST" class="flex items-center justify-center space-x-1">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <select name="status" class="border p-1 rounded bg-gray-50 text-[11px] outline-none">
                                            <option value="Chờ xử lý" <?php if($row['status'] == 'Chờ xử lý') echo 'selected'; ?>>Chờ xử lý</option>
                                            <option value="Đang đóng gói PVC" <?php if($row['status'] == 'Đang đóng gói PVC') echo 'selected'; ?>>Đang đóng gói PVC</option>
                                            <option value="Đang giao" <?php if($row['status'] == 'Đang giao') echo 'selected'; ?>>Đang giao</option>
                                            <option value="Đã giao" <?php if($row['status'] == 'Đã giao') echo 'selected'; ?>>Đã giao</option>
                                            <option value="Đã hủy" <?php if($row['status'] == 'Đã hủy') echo 'selected'; ?>>Đã hủy</option>
                                        </select>
                                        <button type="submit" name="update_status" class="bg-slate-800 text-white px-2 py-1 rounded font-bold hover:bg-slate-700">Lưu</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="p-4 text-center text-gray-400">Hệ thống chưa ghi nhận đơn hàng nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>