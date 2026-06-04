<?php
require_once('../config.php');
if (!isset($_SESSION)) { session_start(); }

// KIỂM TRA PHÂN QUYỀN: Chỉ Admin (role = 2) mới được phép vào
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    die("<div style='padding:50px; text-align:center; font-family:sans-serif;'>
            <h2 style='color:red; font-size:24px; font-weight:800;'>⛔ TRUY CẬP BỊ TỪ CHỐI</h2>
            <p style='color:#555; margin:15px 0;'>Bạn không có quyền quản trị tối cao để điều hành hệ thống.</p>
            <a href='../index.php' style='color:blue; text-decoration:underline;'>Quay lại Trang chủ</a>
         </div>");
}

// Xử lý chức năng XÓA thiết bị đồ câu
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    
    // ĐÃ SỬA: Gọi thẳng sang chính nó
    header("Location: admin.php");
    exit;
}

// Đọc danh sách sản phẩm phục vụ quản lý
$products = $conn->query("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
$total_products = $products ? $products->num_rows : 0;
$total_users = $conn->query("SELECT id FROM users")->num_rows;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KingFisher - Ban Quản Trị Tối Cao</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 flex min-h-screen">

    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between hidden md:flex">
        <div>
            <div class="p-5 bg-slate-950 text-white font-black text-xl tracking-wider border-b border-slate-800">
                👑 KINGFISHER <span class="text-xs text-orange-400 block font-normal">Hệ thống Quản trị v1.0</span>
            </div>
            <nav class="p-4 space-y-2 text-sm font-medium">
                <a href="admin.php" class="block bg-slate-800 text-white px-4 py-3 rounded transition">📦 Quản lý Sản phẩm</a>
                <a href="admin_orders.php" class="block hover:bg-slate-800 hover:text-white px-4 py-3 rounded transition">📋 Quản lý Đơn hàng</a>
                <a href="admin_users.php" class="block hover:bg-slate-800 hover:text-white px-4 py-3 rounded transition">👥 Quản lý Thành viên</a>
                <a href="../index.php" class="block text-orange-400 hover:underline px-4 py-3 pt-6">← Quay lại giao diện chính</a>
            </nav>
        </div>
        <div class="p-4 bg-slate-950 text-xs text-center border-t border-slate-800">
            Quản trị viên: <strong class="text-white"><?php echo htmlspecialchars($_SESSION['fullname']); ?></strong>
            <a href="../MID/logout.php" class="block text-red-400 hover:underline mt-1">Đăng xuất</a>
        </div>
    </aside>

    <main class="flex-1 p-6 md:p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-black text-gray-800">HỆ THỐNG QUẢN LÝ KHO ĐỒ CÂU CAO CẤP</h1>
                <p class="text-xs text-gray-500 mt-1">Cập nhật thông số kỹ thuật, số lượng tồn kho và thiết lập cấu hình sản phẩm High-end.</p>
            </div>
            <a href="admin_add.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded shadow transition uppercase tracking-wider">
                ➕ Thêm sản phẩm mới
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl border shadow-sm">
                <div class="text-xs font-bold text-gray-400 uppercase">Tổng số mặt hàng đồ câu</div>
                <div class="text-2xl font-black text-slate-900 mt-1"><?php echo $total_products; ?> sản phẩm</div>
            </div>
            <div class="bg-white p-5 rounded-xl border shadow-sm">
                <div class="text-xs font-bold text-gray-400 uppercase">Tài khoản cần thủ đăng ký</div>
                <div class="text-2xl font-black text-slate-900 mt-1"><?php echo $total_users; ?> người dùng</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-medium text-gray-600 border-collapse">
                    <thead class="bg-gray-50 text-gray-500 uppercase border-b font-bold tracking-wider">
                        <tr>
                            <th class="p-4">Hình ảnh</th>
                            <th class="p-4">Tên thiết bị</th>
                            <th class="p-4">Hãng sản xuất</th>
                            <th class="p-4">Giá bán công khai</th>
                            <th class="p-4">Tồn kho</th>
                            <th class="p-4 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php 
                        if ($total_products > 0) {
                            while($row = $products->fetch_assoc()) {
                        ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" class="w-12 h-12 object-cover rounded border bg-gray-100" alt="Đồ câu">
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-gray-900 text-sm"><?php echo htmlspecialchars($row['product_name']); ?></div>
                                <div class="text-[11px] text-gray-400 mt-0.5">Phân loại: <?php echo htmlspecialchars($row['category_name'] ?? 'Chưa cấu hình'); ?></div>
                            </td>
                            <td class="p-4">
                                <span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded font-bold uppercase text-[10px]">
                                    <?php echo htmlspecialchars($row['brand']); ?>
                                </span>
                            </td>
                            <td class="p-4 font-bold text-slate-900">
                                <?php echo number_format($row['price'], 0, ',', '.'); ?> đ
                            </td>
                            <td class="p-4">
                                <?php if($row['quantity'] <= 5): ?>
                                    <span class="text-red-600 font-bold">⚠️ Chỉ còn <?php echo $row['quantity']; ?> chiếc</span>
                                <?php else: ?>
                                    <span class="text-gray-600"><?php echo $row['quantity']; ?> chiếc</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center space-x-2">
                                <a href="admin_edit.php?id=<?php echo $row['id']; ?>" class="bg-amber-100 text-amber-800 hover:bg-amber-200 px-2.5 py-1.5 rounded font-bold transition">Sửa</a>
                                <a href="admin.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa thiết bị đồ câu này khỏi cơ sở dữ liệu?')" class="bg-red-100 text-red-800 hover:bg-red-200 px-2.5 py-1.5 rounded font-bold transition">Xóa</a>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                        ?>
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-400">Hệ thống kho đang trống rỗng, hãy nhập thêm sản phẩm đầu tiên.</td>
                        </tr>
                        <?php 
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>