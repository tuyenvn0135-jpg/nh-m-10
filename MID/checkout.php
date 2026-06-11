<?php
// 1. Ép hệ thống bật hiển thị lỗi ở mức cao nhất
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ép PHP Mysqli phải ném ra ngoại lệ (Exception) nếu câu lệnh SQL bị lỗi bảng/cột
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 

try {
    require_once('../config.php');

    if (!isset($_SESSION)) { 
        session_start(); 
    }

    // KIỂM TRA ĐĂNG NHẬP
    if (!isset($_SESSION['user_id'])) {
        die("<script>alert('Vui lòng đăng nhập trước khi thanh toán.'); window.location.href='../login.php';</script>");
    }

    // 2. TÍNH TỔNG TIỀN HÀNG BAN ĐẦU
    $total_cart_money = 0;
    $items_to_buy = [];

    if (isset($_GET['buy_now'])) {
        $p_id = intval($_GET['buy_now']);
        $res = $conn->query("SELECT * FROM products WHERE id = $p_id");
        $prod = $res->fetch_assoc();
        if($prod) {
            $total_cart_money = $prod['price'];
            $items_to_buy[$p_id] = 1;
        }
    } else {
        foreach(($_SESSION['cart'] ?? []) as $p_id => $qty) {
            $res = $conn->query("SELECT * FROM products WHERE id = $p_id");
            $prod = $res->fetch_assoc();
            if($prod) {
                $total_cart_money += ($prod['price'] * $qty);
                $items_to_buy[$p_id] = $qty;
            }
        }
    }

    // 3. XỬ LÝ MÃ KHUYẾN MÃI
    $discount = 0;
    $coupon_code = "";
    $msg_coupon = "";
    $msg_type = "";

    if (isset($_POST['coupon_code'])) {
        $coupon_code = strtoupper(trim($_POST['coupon_code']));
        
        if (!empty($coupon_code)) {
            $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND status = 1 AND expiry_date >= CURDATE()");
            $stmt->bind_param("s", $coupon_code);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res) {
                if ($total_cart_money >= $res['min_order_value']) {
                    $discount = $res['discount_amount'];
                    $msg_coupon = "🎉 Áp dụng mã thành công! Bạn được giảm " . number_format($discount) . "đ";
                    $msg_type = "success";
                } else {
                    $msg_coupon = "❌ Đơn hàng chưa đạt mức tối thiểu " . number_format($res['min_order_value']) . "đ để dùng mã này.";
                    $msg_type = "error";
                    $coupon_code = ""; 
                }
            } else {
                if (isset($_POST['apply_coupon'])) { 
                    $msg_coupon = "❌ Mã giảm giá không chính xác hoặc đã hết hạn.";
                    $msg_type = "error";
                }
                $coupon_code = "";
            }
        }
    }

    // Số tiền thực tế cuối cùng khách phải trả
    $final_total = $total_cart_money - $discount;
    if ($final_total < 0) { 
        $final_total = 0; 
    }

    // 4. XỬ LÝ ĐẶT HÀNG (Khi khách bấm Xác nhận hoàn tất)
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order']) && $total_cart_money > 0) {
        $address = trim($_POST['shipping_address']);
        $method = trim($_POST['payment_method']);
        $user_id = $_SESSION['user_id'];
        $status = 'Chờ xử lý';
        $p_status = ($method == 'ONLINE') ? 'Đã thanh toán (Chờ kiểm tra QR)' : 'Chưa thanh toán';

        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, shipping_address, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idssss", $user_id, $final_total, $status, $address, $method, $p_status);
        
        if ($stmt->execute()) {
            if (!isset($_GET['buy_now'])) { 
                $_SESSION['cart'] = []; 
            }
            echo "<script>alert('Đặt hàng KingFisher thành công! Hệ thống đã ghi nhận đơn hàng của bạn.'); window.location.href='../index.php';</script>";
            exit;
        }
    }

} catch (Exception $e) {
    echo "<div style='color:red; background:#fff; padding:25px; border:3px solid red; font-family:monospace; font-size:14px; margin:20px; z-index:9999; position:relative;'>";
    echo "<h2 style='margin-top:0;'>⚠️ PHÁT HIỆN LỖI CODE/DATABASE:</h2>";
    echo "<b>Nội dung lỗi:</b> <span style='color:black; background:#ffcccc; padding:2px 5px;'>" . $e->getMessage() . "</span><br><br>";
    echo "<b>Vị trí file:</b> " . $e->getFile() . "<br><br>";
    echo "<b>Dòng bị lỗi:</b> <span style='font-size:18px; font-weight:bold;'>Dòng số " . $e->getLine() . "</span><br><br>";
    echo "</div>";
    die();
}

include 'header.php';
?>

<div class="container mx-auto px-4 py-8 max-w-xl" data-aos="zoom-in">

    <div class="bg-white p-6 rounded-xl border shadow-sm text-xs font-medium space-y-6">
        <h2 class="text-lg font-black text-gray-900 border-b pb-3 uppercase">Thông tin giao nhận & Thanh toán</h2>
        
        <div class="bg-gray-50 p-4 rounded-xl border">
            <?php if (!empty($msg_coupon)): ?>
                <div class="text-xs font-bold mb-2 <?php echo $msg_type == 'success' ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo $msg_coupon; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="flex gap-2">
                <input type="text" name="coupon_code" value="<?php echo htmlspecialchars($coupon_code); ?>" placeholder="Nhập mã giảm giá..." class="border p-2 rounded text-xs outline-none w-full max-w-[200px] bg-white uppercase">
                <button type="submit" name="apply_coupon" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded font-bold text-xs transition">Áp dụng</button>
            </form>
        </div>

        <div class="space-y-2 border-b pb-4 text-xs font-medium text-gray-600">
            <div class="flex justify-between">
                <span>Tạm tính (Tiền hàng):</span>
                <span class="font-mono text-gray-900 font-bold"><?php echo number_format($total_cart_money); ?>đ</span>
            </div>
            
            <?php if ($discount > 0): ?>
            <div class="flex justify-between text-green-600 font-bold">
                <span>Giảm giá (Khuyến mãi):</span>
                <span class="font-mono">-<?php echo number_format($discount); ?>đ</span>
            </div>
            <?php endif; ?>
            
            <div class="flex justify-between text-sm font-black text-gray-900 border-t pt-2">
                <span>TỔNG THANH TOÁN:</span>
                <span class="text-red-600 font-mono text-base"><?php echo number_format($final_total); ?>đ</span>
            </div>
        </div>
        
        <form action="" method="POST" class="space-y-4">
            <input type="hidden" name="coupon_code" value="<?php echo htmlspecialchars($coupon_code); ?>">
            <input type="hidden" name="place_order" value="1">

            <div>
                <label class="block text-gray-500 mb-1">Địa chỉ nhận hàng chính xác *</label>
                <textarea name="shipping_address" required rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện..." class="w-full border p-2 text-sm rounded outline-none focus:border-blue-500"></textarea>
            </div>
            
            <div>
                <label class="block text-gray-500 mb-2">Phương thức thanh toán</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 border p-3 rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="COD" id="payment_cod" checked onclick="toggleQR()">
                        <span>📦 Thanh toán tiền mặt khi giao hàng (COD)</span>
                    </label>
                    <label class="flex items-center gap-2 border p-3 rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="ONLINE" id="payment_online" onclick="toggleQR()">
                        <span>💳 Thanh toán Online (Quét mã VietQR tự động)</span>
                    </label>
                </div>
            </div>

            <?php
            $NHAN_HANG   = "MB";          
            $STK         = "0123456789";  
            $TEN_CHU_TK  = "TRAN VAN TUYEN"; 
            // Tạo nội dung chuyển khoản tạm thời bằng ID khách hàng để bạn dễ đối soát hàng về
            $ma_tam      = "KF-USER" . $_SESSION['user_id'] . "-" . rand(100,999);

            $link_vietqr = "https://img.vietqr.io/image/{$NHAN_HANG}-{$STK}-qr_only.jpg?amount={$final_total}&addInfo=" . urlencode("Thanh toan don hang {$ma_tam}") . "&accountName=" . urlencode($TEN_CHU_TK);
            ?>
            <div id="qr_container" class="hidden border-t pt-4 text-center space-y-3 transition-all">
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-wide">⚡ Quét mã VietQR để thanh toán</h3>
                <p class="text-gray-400 text-[11px]">Mở App ngân hàng, quét mã phía dưới để thanh toán số tiền sau khi giảm.</p>
                
                <div class="bg-gray-50 p-3 rounded-lg border inline-block">
                    <img src="<?php echo $link_vietqr; ?>" alt="Mã QR Thanh Toán" class="w-48 h-48 mx-auto object-contain">
                </div>

                <div class="text-left bg-slate-50 p-2.5 rounded space-y-1 text-gray-600 font-mono text-[11px] max-w-xs mx-auto border border-dashed">
                    <div>• Ngân hàng: <span class="font-bold text-gray-900"><?php echo $NHAN_HANG; ?></span></div>
                    <div>• Số tài khoản: <span class="font-bold text-gray-900"><?php echo $STK; ?></span></div>
                    <div>• Số tiền: <span class="font-bold text-red-600"><?php echo number_format($final_total); ?>đ</span></div>
                    <div>• Nội dung: <span class="font-bold text-blue-600">Thanh toan don hang <?php echo $ma_tam; ?></span></div>
                </div>
                <p class="text-green-600 font-bold text-[11px] animate-pulse">👉 Sau khi chuyển khoản xong, vui lòng bấm nút màu xanh phía dưới để hoàn tất!</p>
            </div>
            
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-3 rounded-lg text-xs uppercase tracking-wider transition">Xác nhận hoàn tất đơn hàng</button>
        </form>
    </div>

</div>

<script>
function toggleQR() {
    const onlineRadio = document.getElementById('payment_online');
    const qrContainer = document.getElementById('qr_container');
    if (onlineRadio && onlineRadio.checked) {
        qrContainer.classList.remove('hidden');
    } else {
        qrContainer.classList.add('hidden');
    }
}
// Chạy kiểm tra ngay khi vừa load xong trang phòng trường hợp khách reload
document.addEventListener('DOMContentLoaded', toggleQR);
</script>

<?php include_once 'footer.php'; ?>