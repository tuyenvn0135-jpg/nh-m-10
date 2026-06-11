<?php
require_once('../config.php');
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) { die("Từ chối truy cập."); }

// Truy vấn tổng doanh thu từ các đơn đã hoặc đang giao (trừ đơn hủy)
$res_total = $conn->query("SELECT SUM(total_price) as revenue FROM orders WHERE status != 'Đã hủy'");
$row_total = $res_total->fetch_assoc();
$total_revenue = $row_total['revenue'] ?? 0;

$res_count = $conn->query("SELECT COUNT(id) as total_orders FROM orders");
$total_orders = $res_count->fetch_assoc()['total_orders'] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo Cáo Doanh Thu Doanh Nghiệp KingFisher</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Thư viện vẽ biểu đồ -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 flex min-h-screen text-xs font-medium">

    <!-- Sidebar Trái -->
    <aside class="w-64 bg-slate-900 text-slate-300 p-4 space-y-2">
        <div class="p-3 text-white font-black text-lg border-b border-slate-800 mb-4">👑 ADMIN</div>
        <a href="toroDMIN/admin.php" class="block hover:bg-slate-800 p-3 rounded">📦 Quản lý Sản phẩm</a>
        <a href="toroDMIN/admin_orders.php" class="block hover:bg-slate-800 p-3 rounded">📋 Quản lý Đơn hàng</a>
        <a href="toroDMIN/admin_report.php" class="block bg-slate-800 text-white p-3 rounded">📊 Báo Cáo Doanh Thu</a>
        <a href="index.php" class="block text-orange-400 p-3">← Quay lại trang chủ</a>
    </aside>

    <!-- Khu vực hiển thị Báo Cáo -->
    <main class="flex-1 p-8">
        <h1 class="text-2xl font-black text-gray-800 mb-6">📊 BÁO CÁO KẾT QUẢ HOẠT ĐỘNG KINH DOANH</h1>
        
        <!-- Thẻ thống kê nhanh -->
        <div class="grid grid-cols-2 gap-6 mb-8 text-sm">
            <div class="bg-white p-5 rounded-xl border shadow-sm">
                <div class="text-gray-400 font-bold uppercase text-xs">Tổng doanh thu hệ thống</div>
                <div class="text-2xl font-black text-green-600 mt-1"><?php echo number_format($total_revenue, 0, ',', '.'); ?> đ</div>
            </div>
            <div class="bg-white p-5 rounded-xl border shadow-sm">
                <div class="text-gray-400 font-bold uppercase text-xs">Tổng lượng đơn phát sinh</div>
                <div class="text-2xl font-black text-gray-900 mt-1"><?php echo $total_orders; ?> đơn hàng</div>
            </div>
        </div>

        <!-- Vùng dựng biểu đồ trực quan -->
        <div class="bg-white p-6 rounded-xl border shadow-sm max-w-2xl">
            <h3 class="text-sm font-black text-gray-800 mb-4 uppercase">Biểu đồ tăng trưởng doanh số (Năm 2026)</h3>
            <canvas id="revenueChart"></canvas>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar', // Biểu đồ dạng cột
            data: {
                labels: ['Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6 (Hiện tại)'],
                datasets: [{
                    label: 'Doanh số bán đồ câu (VNĐ)',
                    data: [12000000, 18500000, 24000000, <?php echo $total_revenue; ?>],
                    backgroundColor: '#0A2540',
                    borderColor: '#3b82f6',
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</body>
</html>