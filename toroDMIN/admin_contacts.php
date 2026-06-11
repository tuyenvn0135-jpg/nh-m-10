<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    require_once('../config.php');
    if (!isset($_SESSION)) { session_start(); }

    // KIỂM TRA PHÂN QUYỀN
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
        die("<div style='padding:50px; text-align:center; font-family:sans-serif;'>
                <h2 style='color:red; font-size:24px; font-weight:800;'>⛔ TRUY CẬP BỊ TỪ CHỐI</h2>
                <a href='../index.php'>Quay lại Trang chủ</a>
             </div>");
    }

    // XỬ LÝ XÓA LỜI NHẮN LIÊN HỆ
    if (isset($_GET['delete_id'])) {
        $del_id = intval($_GET['delete_id']);
        $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        
        header("Location: admin_contacts.php");
        exit;
    }

    // Lấy toàn bộ danh sách lời nhắn từ bảng contacts xếp mới nhất lên đầu
    $contacts = $conn->query("SELECT * FROM contacts ORDER BY id DESC");
    $total_contacts = $contacts ? $contacts->num_rows : 0;

} catch (Exception $e) {
    echo "<div style='color:red; background:#fff; padding:25px; border:3px solid red; font-family:monospace; z-index:9999; position:relative;'>";
    echo "<h2>⚠️ LỖI DATABASE TABLE CONTACTS VUI LÒNG KIỂM TRA:</h2>";
    echo "<b>Chi tiết:</b> " . $e->getMessage() . "<br><br>";
    echo "<i>Mẹo: Nếu báo bảng 'contacts' chưa tồn tại, hãy chạy câu lệnh SQL tạo bảng trong phpMyAdmin nhé!</i>";
    echo "</div>";
    die();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KingFisher Admin - Hộp Thư Liên Hệ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 flex min-h-screen">

    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between hidden md:flex shrink-0">
        <div>
            <div class="p-5 bg-slate-950 text-white font-black text-xl tracking-wider border-b border-slate-800">
                👑 KINGFISHER <span class="text-xs text-orange-400 block font-normal">Hệ thống Quản trị v1.0</span>
            </div>
            <nav class="p-4 space-y-2 text-sm font-medium">
                <a href="admin.php" class="block hover:bg-slate-800 hover:text-white px-4 py-3 rounded transition">📦 Quản lý Sản phẩm</a>
                <a href="admin_orders.php" class="block hover:bg-slate-800 hover:text-white px-4 py-3 rounded transition">📋 Quản lý Đơn hàng</a>
                <a href="admin_users.php" class="block hover:bg-slate-800 hover:text-white px-4 py-3 rounded transition">👥 Quản lý Thành viên</a>
                <a href="admin_contacts.php" class="block bg-slate-800 text-white px-4 py-3 rounded transition flex items-center gap-2">💬 Quản lý Liên Hệ</a>
                
                <a href="../index.php" class="block text-orange-400 hover:underline px-4 py-3 pt-6">← Quay lại giao diện chính</a>
            </nav>
        </div>
        <div class="p-4 bg-slate-950 text-xs text-center border-t border-slate-800">
            Quản trị viên: <strong class="text-white"><?php echo htmlspecialchars($_SESSION['fullname'] ?? 'Admin'); ?></strong>
            <a href="../MID/logout.php" class="block text-red-400 hover:underline mt-1">Đăng xuất</a>
        </div>
    </aside>

    <main class="flex-1 p-6 md:p-8 overflow-hidden">
        <div class="mb-8">
            <h1 class="text-2xl font-black text-gray-800 uppercase">Hộp thư góp ý & Liên hệ</h1>
            <p class="text-xs text-gray-500 mt-1">Nơi tiếp nhận các câu hỏi thắc mắc kỹ thuật, yêu cầu bảo hành từ form liên hệ ngoài trang chủ.</p>
        </div>

        <div class="bg-white p-5 rounded-xl border shadow-sm max-w-xs mb-8">
            <div class="text-xs font-bold text-gray-400 uppercase">Tổng số tin nhắn nhận được</div>
            <div class="text-2xl font-black text-blue-600 mt-1"><?php echo $total_contacts; ?> tin nhắn</div>
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-medium text-gray-600 border-collapse">
                    <thead class="bg-gray-50 text-gray-500 uppercase border-b font-bold tracking-wider">
                        <tr>
                            <th class="p-4 w-12 text-center">ID</th>
                            <th class="p-4 w-48">Thông tin khách hàng</th>
                            <th class="p-4">Nội dung lời nhắn / Câu hỏi</th>
                            <th class="p-4 w-24 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php 
                        if ($total_contacts > 0) {
                            while($row = $contacts->fetch_assoc()) {
                        ?>
                        <tr class="hover:bg-gray-50 transition items-start">
                            <td class="p-4 text-center font-bold text-gray-400">
                                #<?php echo $row['id']; ?>
                            </td>
                            <td class="p-4 space-y-1">
                                <div class="font-bold text-gray-900 text-sm"><?php echo htmlspecialchars($row['fullname']); ?></div>
                                <div class="text-gray-500 font-normal">✉️ <?php echo htmlspecialchars($row['email']); ?></div>
                                <?php if(!empty($row['phone'])): ?>
                                    <div class="text-blue-600 font-mono text-[11px]">📞 <?php echo htmlspecialchars($row['phone']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-gray-700 leading-relaxed font-normal whitespace-pre-line text-xs max-w-md">
                                <div class="bg-slate-50 p-3 rounded-lg border border-gray-100 shadow-inner">
                                    <?php echo htmlspecialchars($row['message']); ?>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <a href="admin_contacts.php?delete_id=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Bạn có chắc muốn xóa lời nhắn này sau khi đã xử lý liên hệ xong không?')" 
                                   class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-2 rounded-lg font-bold transition inline-block">
                                   🗑️ Xóa
                                </a>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                        ?>
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-400 text-sm">📥 Hiện tại chưa có lời nhắn góp ý nào từ khách hàng.</td>
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