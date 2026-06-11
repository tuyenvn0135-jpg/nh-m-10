<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Dùng đường dẫn tuyệt đối chống lỗi open_basedir trên InfinityFree
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

if (!isset($_SESSION)) { session_start(); }

// Kiểm tra quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    die("Từ chối truy cập.");
}

$msg = "";

// 1. Xử lý THÊM MỚI mã khuyến mãi (CREATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_coupon'])) {
    $code = strtoupper(trim($_POST['code']));
    $discount_amount = intval($_POST['discount_amount']);
    $min_order_value = intval($_POST['min_order_value']);
    $expiry_date = $_POST['expiry_date'];

    $stmt = $conn->prepare("INSERT INTO coupons (code, discount_amount, min_order_value, expiry_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siis", $code, $discount_amount, $min_order_value, $expiry_date);
    if ($stmt->execute()) {
        $msg = "✅ Thêm mã khuyến mãi mới thành công!";
    } else {
        $msg = "❌ Lỗi: Mã này có thể đã tồn tại.";
    }
}

// 2. Xử lý XÓA mã khuyến mãi (DELETE)
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM coupons WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    header("Location: admin_promotions.php");
    exit;
}

// 3. LẤY DANH SÁCH mã khuyến mãi (READ)
$coupons = $conn->query("SELECT * FROM coupons ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>KingFisher Admin - Quản Lý Khuyến Mãi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 flex min-h-screen font-['Inter'] text-xs font-medium">

    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between hidden md:flex">
        <div>
            <div class="p-5 bg-slate-950 text-white font-black text-xl tracking-wider border-b border-slate-800">👑 KINGFISHER</div>
            <nav class="p-4 space-y-2 text-sm font-medium">
                <a href="admin.php" class="block hover:bg-slate-800 hover:text-white px-4 py-3 rounded transition">📦 Quản lý Sản phẩm</a>
                <a href="admin_orders.php" class="block hover:bg-slate-800 hover:text-white px-4 py-3 rounded transition">📋 Quản lý Đơn hàng</a>
                <a href="admin_users.php" class="block hover:bg-slate-800 hover:text-white px-4 py-3 rounded transition">👥 Quản lý Thành viên</a>
                <a href="admin_promotions.php" class="block bg-slate-800 text-white px-4 py-3 rounded transition">🎁 Quản lý Khuyến mãi</a>
                <a href="../index.php" class="block text-orange-400 hover:underline px-4 py-3 pt-6">← Quay lại giao diện chính</a>
            </nav>
        </div>
    </aside>

    <main class="flex-1 p-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="bg-white p-6 rounded-xl border shadow-sm h-fit">
            <h2 class="text-base font-black text-gray-800 mb-4 uppercase">Tạo mã khuyến mãi</h2>
            <?php if(!empty($msg)) echo "<div class='p-3 bg-blue-50 text-blue-700 font-bold rounded-lg mb-4'>$msg</div>"; ?>
            
            <form action="admin_promotions.php" method="POST" class="space-y-4">
                <input type="hidden" name="add_coupon" value="1">
                <div>
                    <label class="block text-gray-500 mb-1">Mã Code (Viết liền không dấu)</label>
                    <input type="text" name="code" required placeholder="Ví dụ: MOICAUVIP" class="w-full border p-2.5 rounded text-sm outline-none uppercase focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Số tiền giảm (VND)</label>
                    <input type="number" name="discount_amount" required placeholder="Ví dụ: 50000" class="w-full border p-2.5 rounded text-sm outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Đơn tối thiểu áp dụng (VND)</label>
                    <input type="number" name="min_order_value" value="0" class="w-full border p-2.5 rounded text-sm outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Ngày hết hạn</label>
                    <input type="date" name="expiry_date" required class="w-full border p-2.5 rounded text-sm outline-none focus:border-blue-500">
                </div>
                <button type="submit" class="w-full bg-[#0A2540] text-white py-3 rounded-lg font-bold tracking-wider hover:bg-slate-800 transition">PHÁT HÀNH MÃ</button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl border shadow-sm overflow-hidden h-fit">
            <div class="p-4 border-b bg-gray-50"><h2 class="text-base font-black text-gray-800 uppercase">Danh sách mã đang chạy</h2></div>
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 uppercase font-bold tracking-wider border-b">
                    <tr>
                        <th class="p-4">Mã Code</th>
                        <th class="p-4">Mức Giảm</th>
                        <th class="p-4">Đơn Tối Thiểu</th>
                        <th class="p-4">Hạn Dùng</th>
                        <th class="p-4 text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    <?php while($row = $coupons->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-mono font-bold text-blue-600 text-sm"><?php echo $row['code']; ?></td>
                        <td class="p-4 text-green-600 font-bold"><?php echo number_format($row['discount_amount']); ?>đ</td>
                        <td class="p-4"><?php echo number_format($row['min_order_value']); ?>đ</td>
                        <td class="p-4 text-gray-400"><?php echo $row['expiry_date']; ?></td>
                        <td class="p-4 text-center">
                            <a href="admin_promotions.php?delete_id=<?php echo $row['id']; ?>" class="text-red-600 hover:underline font-bold" onclick="return confirm('Xóa vĩnh viễn mã khuyến mãi này?')">Xóa bỏ</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>