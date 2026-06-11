\<?php
// 1. Ép hệ thống bật hiển thị lỗi ở mức cao nhất để diệt tận gốc lỗi 500 trắng trang
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ép Mysqli ném ngoại lệ nếu câu lệnh SQL tương tác bảng/cột bị sai
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$success_msg = "";
$error_msg = "";
$fullname = $email = $phone = $message = "";

try {
    require_once('../config.php');

    // Xử lý khi khách hàng bấm nút Gửi lời nhắn
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';

        // Kiểm tra tính hợp lệ của dữ liệu đầu vào
        if (empty($fullname) || empty($email) || empty($message)) {
            $error_msg = "❌ Vui lòng điền đầy đủ các trường bắt buộc (Họ tên, Email và Lời nhắn).";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = "❌ Định dạng email không hợp lệ, vui lòng kiểm tra lại.";
        } else {
            // Chèn dữ liệu vào bảng contacts bằng SQL Prepared Statement
            $stmt = $conn->prepare("INSERT INTO contacts (fullname, email, phone, message) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $email, $phone, $message);
            
            if ($stmt->execute()) {
                $success_msg = "🎉 Cảm ơn bạn! Lời nhắn của bạn đã được gửi thành công. Ban quản trị KingFisher sẽ phản hồi sớm nhất qua Email/SĐT.";
                // Xóa trắng dữ liệu sau khi gửi thành công
                $fullname = $email = $phone = $message = "";
            } else {
                $error_msg = "❌ Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.";
            }
        }
    }
} catch (Exception $e) {
    // NẾU CÓ LỖI DATABASE HOẶC ĐƯỜNG DẪN, HỘP THOẠI MÀU ĐỎ SẼ HIỆN RA NGAY
    echo "<div style='color:red; background:#fff; padding:25px; border:3px solid red; font-family:monospace; font-size:14px; margin:20px; z-index:9999; position:relative;'>";
    echo "<h2 style='margin-top:0;'>⚠️ PHÁT HIỆN LỖI CODE / DATABASE (CONTACT):</h2>";
    echo "<b>Nội dung lỗi:</b> <span style='color:black; background:#ffcccc; padding:2px 5px;'>" . $e->getMessage() . "</span><br><br>";
    echo "<b>Vị trí file:</b> " . $e->getFile() . "<br><br>";
    echo "<b>Dòng bị lỗi:</b> <span style='font-size:18px; font-weight:bold;'>Dòng số " . $e->getLine() . "</span><br><br>";
    echo "<i>👉 Bạn hãy copy dòng chữ báo lỗi màu đỏ này gửi lại để mình chỉ cách xử lý database nhé!</i>";
    echo "</div>";
    die();
}

include_once 'header.php';
?>

<div class="container mx-auto px-4 py-12 max-w-6xl">
    <div class="text-center mb-12" data-aos="fade-down">
        <span class="bg-blue-50 text-[#0A2540] text-[10px] font-black px-3 py-1 uppercase rounded-full tracking-widest border border-blue-100">Contact Us</span>
        <h1 class="text-2xl font-black text-gray-900 uppercase mt-3">Liên Hệ Với Chúng Tôi</h1>
        <p class="text-xs text-gray-400 mt-1 max-w-lg mx-auto">Bạn có thắc mắc về kỹ thuật cần câu, chính sách bảo hành hay muốn đóng góp ý kiến? Hãy để lại lời nhắn dưới đây.</p>
    </div>

    <?php if(!empty($error_msg)) echo "<div class='p-3 text-xs bg-red-50 text-red-700 font-bold rounded-lg mb-8 border border-red-100 max-w-4xl mx-auto'>$error_msg</div>"; ?>
    <?php if(!empty($success_msg)) echo "<div class='p-3 text-xs bg-green-50 text-green-700 font-bold rounded-lg mb-8 border border-green-100 max-w-4xl mx-auto'>$success_msg</div>"; ?>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 bg-white p-6 md:p-8 rounded-xl border shadow-sm text-xs font-medium">
        
        <div class="lg:col-span-2 space-y-6 lg:border-r lg:pr-8 border-gray-100">
            <div>
                <h3 class="text-sm font-black text-[#0A2540] uppercase tracking-wider mb-4">👑 KINGFISHER SHOWROOM</h3>
                <p class="text-gray-500 leading-relaxed font-normal">Hệ thống phân phối dụng cụ câu cá giải đấu cấp cao, chuyên nhập khẩu các dòng sản phẩm giới hạn từ Nhật Bản và Mỹ.</p>
            </div>

            <div class="space-y-4 text-gray-700">
                <div class="flex items-start gap-3">
                    <span class="text-base">📍</span>
                    <div>
                        <div class="font-bold">Địa chỉ Showroom:</div>
                        <div class="text-gray-500 font-normal mt-0.5">367 Đường Bờ Biển, Phường 36, TP. Vũng Tàu</div>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="text-base">📞</span>
                    <div>
                        <div class="font-bold">Hotline hỗ trợ kỹ thuật:</div>
                        <div class="text-gray-500 font-mono mt-0.5">1900.XXXX (8:00 - 21:00)</div>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="text-base">✉️</span>
                    <div>
                        <div class="font-bold">Email tiếp nhận thông tin:</div>
                        <div class="text-gray-500 font-normal mt-0.5">contact@kingfisher.vn</div>
                    </div>
                </div>
            </div>

            <div class="w-full h-44 rounded-lg overflow-hidden border border-gray-200 shadow-inner bg-gray-50">
                <iframe 
                    src="https://maps.google.com/maps?q=123+Đường+Bờ+Biển,+Phường+5,+Thành+phố+Vũng+Tàu&t=&z=14&ie=UTF8&iwloc=&output=embed" 
                    class="w-full h-full border-0" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

        <div class="lg:col-span-3">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4">✍️ GỬI LỜI NHẮN CHO CHÚNG TÔI</h3>
            
            <form action="" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-500 mb-1.5 font-bold">Họ và tên của bạn <span class="text-red-500">*</span></label>
                        <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required placeholder="Nhập họ và tên..." class="w-full border border-gray-200 p-2.5 rounded-lg text-sm outline-none font-normal focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-500 mb-1.5 font-bold">Số điện thoại liên hệ</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="Nhập số điện thoại (nếu có)..." class="w-full border border-gray-200 p-2.5 rounded-lg text-sm outline-none font-normal focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-500 mb-1.5 font-bold">Địa chỉ Email nhận phản hồi <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="VD: canthu@gmail.com" class="w-full border border-gray-200 p-2.5 rounded-lg text-sm outline-none font-normal focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-gray-500 mb-1.5 font-bold">Nội dung lời nhắn / Câu hỏi thắc mắc <span class="text-red-500">*</span></label>
                    <textarea name="message" rows="5" required placeholder="Nhập chi tiết câu hỏi hoặc vấn đề bạn cần KingFisher hỗ trợ giải đáp..." class="w-full border border-gray-200 p-2.5 rounded-lg text-sm outline-none font-normal focus:border-blue-500 leading-relaxed"><?php echo htmlspecialchars($message); ?></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto bg-[#0A2540] hover:bg-blue-900 text-white font-bold px-8 py-3 rounded-lg uppercase tracking-wider text-[11px] transition shadow-sm">
                        🚀 Gửi Thông Tin Liên Hệ
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php include_once 'footer.php'; ?>