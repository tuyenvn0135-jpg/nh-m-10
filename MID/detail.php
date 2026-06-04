<?php 
require_once('../config.php');
include_once 'header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if(!$item) {
    echo "<div class='container mx-auto px-4 py-20 text-center text-red-500 font-bold'>Sản phẩm không tồn tại hoặc đã bị gỡ bỏ. <a href='index.php' class='underline text-blue-600'>Quay lại trang chủ</a></div>";
    include_once 'MID/footer.php';
    exit;
}
?>

<div class="container mx-auto px-4 py-10 max-w-5xl">
    <a href="index.php" class="text-xs font-semibold text-gray-500 hover:text-[#0A2540] mb-6 inline-block">← QUAY LẠI DANH SÁCH</a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div>
            <img src="<?php echo $item['image']; ?>" alt="Đồ câu cao cấp" class="w-full h-auto max-h-[400px] object-cover rounded-lg border">
            <div class="mt-4 bg-blue-50 border border-blue-200 text-blue-800 text-xs p-3 rounded flex items-center gap-2">
                📦 <strong>An tâm vận chuyển:</strong> Hỗ trợ đóng ống nhựa PVC siêu dày bảo vệ lõi cần tuyệt đối khỏi gãy hỏng khi giao hàng từ xa.
            </div>
        </div>

        <div class="flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-orange-600 uppercase tracking-widest"><?php echo $item['brand']; ?> Genuine</span>
                <h1 class="text-2xl font-extrabold text-gray-900 mt-1 mb-2 tracking-tight"><?php echo $item['product_name']; ?></h1>
                <div class="text-2xl font-black text-[#0A2540] mb-4"><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</div>
                
                <hr class="my-4">
                
                <h3 class="text-xs font-bold uppercase text-gray-400 mb-2">Thông số kỹ thuật chi tiết</h3>
                <div class="bg-gray-50 p-4 rounded-lg text-xs space-y-2 font-medium text-gray-700">
                    <div>• <span class="text-gray-400">Xuất xứ chính hãng:</span> <?php echo $item['origin']; ?></div>
                    <div>• <span class="text-gray-400">Tình trạng kho:</span> Còn <?php echo $item['quantity']; ?> thiết bị có sẵn</div>
                    <div>• <span class="text-gray-400">Chi tiết cấu hình:</span> <?php echo $item['specifications']; ?></div>
                </div>

                <div class="mt-4">
                    <h3 class="text-xs font-bold uppercase text-gray-400 mb-1">Mô tả sản phẩm</h3>
                    <p class="text-gray-600 text-sm leading-relaxed"><?php echo $item['description']; ?></p>
                </div>
            </div>

            <button class="w-full mt-6 bg-[#FF9F1C] hover:bg-orange-500 text-black font-extrabold text-sm py-4 rounded-lg tracking-wider uppercase transition shadow-md">
                Thêm vào giỏ - Giao hàng PVC bảo vệ
            </button>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>