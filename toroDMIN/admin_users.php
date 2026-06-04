<?php
require_once('../config.php');
if (!isset($_SESSION)) { session_start(); }

// Kiểm tra quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    die("Từ chối truy cập.");
}

// Xử lý thay đổi phân quyền tài khoản (Khách hàng <-> Nhân viên <-> Admin)
if (isset($_GET['change_role_id']) && isset($_GET['new_role'])) {
    $user_id = intval($_GET['change_role_id']);
    $new_role = intval($_GET['new_role']);
    
    // Ngăn chặn việc tự hạ quyền của chính mình
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_role, $user_id);
        $stmt->execute();
    }
    header("Location: toroDMIN/admin_users.php");
    exit;
}

// Lấy danh sách toàn bộ thành viên cần thủ
$users = $conn->query("SELECT id, username, email, fullname, phone, role FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>KingFisher Admin - Quản Lý Thành Viên</title>
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
                <<a href="<?php echo $base_url; ?>toroDMIN/admin.php" class="block bg-slate-800 text-white px-4 py-3 rounded transition">📦 Quản lý Sản phẩm</a>
<a href="<?php echo $base_url; ?>toroDMIN/admin_orders.php" class="block hover:bg-slate-800 hover:text-white px-4 py-3 rounded transition">📋 Quản lý Đơn hàng</a>
<a href="<?php echo $base_url; ?>toroDMIN/admin_users.php" class="block hover:bg-slate-800 hover:text-white px-4 py-3 rounded transition">👥 Quản lý Thành viên</a>
<a href="<?php echo $base_url; ?>index.php" class="block text-orange-400 hover:underline px-4 py-3 pt-6">← Quay lại giao diện chính</a>
            </nav>
        </div>
    </aside>

    <main class="flex-1 p-8 text-xs">
        <div class="mb-6">
            <h1 class="text-2xl font-black text-gray-800">👥 QUẢN LÝ HỘI CẦN THỦ KINGFISHER</h1>
            <p class="text-gray-500 mt-1">Xem danh sách khách hàng, cấp độ hoạt động và điều chỉnh phân cấp quyền hạn hệ thống.</p>
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold tracking-wider border-b">
                        <tr>
                            <th class="p-4">ID</th>
                            <th class="p-4">Họ và Tên</th>
                            <th class="p-4">Tên tài khoản</th>
                            <th class="p-4">Email liên hệ</th>
                            <th class="p-4">Số điện thoại</th>
                            <th class="p-4 text-center">Cấp độ phân quyền</th>
                            <th class="p-4 text-center">Hành động nhanh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium text-gray-600">
                        <?php while($row = $users->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-gray-400">#<?php echo $row['id']; ?></td>
                            <td class="p-4 font-bold text-gray-900"><?php echo htmlspecialchars($row['fullname']); ?></td>
                            <td class="p-4 font-mono text-blue-600"><?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($row['phone'] ?? 'Chưa cập nhật'); ?></td>
                            <td class="p-4 text-center">
                                <?php if($row['role'] == 2): ?>
                                    <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded font-bold uppercase text-[10px]">Quản trị viên (Admin)</span>
                                <?php elseif($row['role'] == 1): ?>
                                    <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded font-bold uppercase text-[10px]">Nhân viên kho</span>
                                <?php else: ?>
                                    <span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded font-bold uppercase text-[10px]">Khách hàng</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center space-x-1">
                                <?php if($row['id'] != $_SESSION['user_id']): ?>
                                    <?php if($row['role'] != 2): ?>
                                        <a href="toroDMIN/admin_users.php?change_role_id=<?php echo $row['id']; ?>&new_role=2" class="bg-red-50 text-red-700 hover:bg-red-100 px-2 py-1 rounded font-bold transition" onclick="return confirm('Cấp quyền Admin tối cao cho tài khoản này?')">Lên Admin</a>
                                    <?php else: ?>
                                        <a href="toroDMIN/admin_users.php?change_role_id=<?php echo $row['id']; ?>&new_role=0" class="bg-slate-100 text-slate-700 hover:bg-slate-200 px-2 py-1 rounded font-bold transition" onclick="return confirm('Hạ quyền Admin của tài khoản này xuống Khách hàng thông thường?')">Gỡ Admin</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Tài khoản của bạn</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>