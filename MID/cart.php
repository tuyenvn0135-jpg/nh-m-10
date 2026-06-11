<?php
require_once('../config.php');
if (!isset($_SESSION)) { session_start(); }

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($action == 'add' && $id > 0) {
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
    header("Location: cart.php"); exit; // SỬA: Giữ nguyên ở thư mục hiện tại
}

if ($action == 'delete' && $id > 0) {
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php"); exit; // SỬA: Bỏ chữ MID/ ở đây để không bị nhảy trang 404
}

include 'header.php';
?>
<div class="container mx-auto px-4 py-8" data-aos="fade-up">
    <h1 class="text-xl font-black text-gray-900 mb-6">🛒 GIỎ HÀNG CỦA BẠN</h1>
    
    <?php if(!empty($_SESSION['cart'])): ?>
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden text-xs">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b font-bold text-gray-500 uppercase">
                    <tr>
                        <th class="p-4">Sản phẩm</th>
                        <th class="p-4">Giá</th>
                        <th class="p-4">Số lượng</th>
                        <th class="p-4">Tổng</th>
                        <th class="p-4 text-center">Xóa</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php 
                    $total_cart = 0;
                    foreach($_SESSION['cart'] as $p_id => $qty): 
                        $res = $conn->query("SELECT * FROM products WHERE id = $p_id");
                        $prod = $res->fetch_assoc();
                        if(!$prod) continue;
                        $subtotal = $prod['price'] * $qty;
                        $total_cart += $subtotal;
                    ?>
                    <tr>
                        <td class="p-4 flex items-center gap-3">
                            <img src="<?php echo htmlspecialchars($prod['image']); ?>" class="w-10 h-10 object-cover rounded border">
                            <span class="font-bold text-gray-900"><?php echo htmlspecialchars($prod['product_name']); ?></span>
                        </td>
                        <td class="p-4 font-semibold"><?php echo number_format($prod['price'], 0, ',', '.'); ?>đ</td>
                        <td class="p-4 font-mono"><?php echo $qty; ?></td>
                        <td class="p-4 font-bold text-red-600"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                        <td class="p-4 text-center">
                            <a href="cart.php?action=delete&id=<?php echo $p_id; ?>" class="text-red-500 hover:underline">Gỡ khỏi giỏ</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="p-4 bg-gray-50 flex justify-between items-center border-t">
                <div class="text-sm font-black">Tổng tiền thanh toán: <span class="text-red-600 text-lg"><?php echo number_format($total_cart, 0, ',', '.'); ?> đ</span></div>
                <a href="checkout.php" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-6 py-2.5 rounded-lg text-xs uppercase tracking-wider transition">Tiến hành đặt hàng →</a>
            </div>
        </div>
    <?php else: ?>
        <p class="text-gray-500 text-center py-12 text-sm bg-white rounded-xl border">Giỏ hàng trống rỗng. Hãy chọn mua thiết bị câu cá cao cấp!</p>
    <?php endif; ?>
</div>
<?php include_once 'footer.php'; ?>