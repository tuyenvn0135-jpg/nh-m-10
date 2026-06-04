<?php
require_once('../config.php');
if (!isset($_SESSION)) { session_start(); }

if (!isset($_SESSION['user_id'])) {
    // SỬA LINK: Đổi từ MID/login.php thành ../login.php để nhảy ra thư mục gốc
    die("<script>alert('Vui lòng đăng nhập trước khi thanh toán.'); window.location.href='../login.php';</script>");
}

// Tính tổng tiền giỏ hàng
$total_checkout = 0;
$items_to_buy = [];

if (isset($_GET['buy_now'])) {
    $p_id = intval($_GET['buy_now']);
    $res = $conn->query("SELECT * FROM products WHERE id = $p_id");
    $prod = $res->fetch_assoc();
    if($prod) {
        $total_checkout = $prod['price'];
        $items_to_buy[$p_id] = 1;
    }
} else {
    foreach(($_SESSION['cart'] ?? []) as $p_id => $qty) {
        $res = $conn->query("SELECT * FROM products WHERE id = $p_id");
        $prod = $res->fetch_assoc();
        if($prod) {
            $total_checkout += ($prod['price'] * $qty);
            $items_to_buy[$p_id] = $qty;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $total_checkout > 0) {
    $address = trim($_POST['shipping_address']);
    $method = trim($_POST['payment_method']);
    $user_id = $_SESSION['user_id'];
    $status = 'Chờ xử lý';
    $p_status = ($method == 'ONLINE') ? 'Đã thanh toán (Chờ VNPAY)' : 'Chưa thanh toán';

    // Lưu vào bảng đơn hàng
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, shipping_address, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssss", $user_id, $total_checkout, $status, $address, $method, $p_status);
    
    if ($stmt->execute()) {
        // Xóa giỏ hàng sau khi đặt thành công
        if (!isset($_GET['buy_now'])) { $_SESSION['cart'] = []; }
        // SỬA LINK: Đổi từ index.php thành ../index.php để quay về trang chủ ở thư mục gốc
        echo "<script>alert('Đặt hàng KingFisher thành công! Chúng tôi sẽ chuẩn bị đóng ống nhựa PVC bảo vệ cần.'); window.location.href='../index.php';</script>";
        exit;
    }
}

include 'header.php';
?>
<div class="container mx-auto px-4 py-8 max-w-xl" data-aos="zoom-in">
    <div class="bg-white p-6 rounded-xl border shadow-sm text-xs font-medium">
        <h2 class="text-lg font-black text-gray-900 border-b pb-3 mb-4 uppercase">Thông tin giao nhận & Thanh toán</h2>
        <div class="mb-4 p-3 bg-blue-50 text-blue-800 rounded font-bold">Tổng số tiền cần thanh toán: <?php echo number_format($total_checkout, 0, ',', '.'); ?>đ</div>
        
        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-500 mb-1">Địa chỉ nhận hàng chính xác *</label>
                <textarea name="shipping_address" required rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện..." class="w-full border p-2 text-sm rounded outline-none focus:border-blue-500"></textarea>
            </div>
            <div>
                <label class="block text-gray-500 mb-2">Phương thức thanh toán</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 border p-3 rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="COD" checked>
                        <span>📦 Thanh toán tiền mặt khi giao hàng (COD)</span>
                    </label>
                    <label class="flex items-center gap-2 border p-3 rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="ONLINE">
                        <span>💳 Thanh toán Online (Cổng ATM/VNPAY giả định)</span>
                    </label>
                </div>
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-3 rounded-lg text-xs uppercase tracking-wider transition">Xác nhận hoàn tất đơn hàng</button>
        </form>
    </div>
</div>
<?php include_once 'footer.php'; ?>