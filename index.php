<?php 
require_once 'config.php';
include_once 'MID/header.php';

// Trang chủ chỉ cần lấy ra 8 sản phẩm mới nhất để làm nổi bật (Feature Products)
$products = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 8");
?>

<div class="relative bg-slate-900 h-[350px] md:h-[450px] flex items-center justify-center text-center bg-cover bg-center" style="background-image: linear-gradient(rgba(10,37,64,0.65), rgba(10,37,64,0.65)), url('anh/z7870881359151_c71894ff4289305e2f4867dadf4c89db.jpg');">
    <div class="px-4">
        <span class="bg-[#FF9F1C] text-black text-xs font-bold px-3 py-1 uppercase rounded-full tracking-widest">Premium Fishing Gear</span>
        <h1 class="text-3xl md:text-4xl font-black text-white mt-3" data-aos="fade-right">KINGFISHER - ĐẲNG CẤP CẦN THỦ</h1>
        <p class="text-gray-300 max-w-xl mx-auto text-xs md:text-sm font-light mt-2 mb-6">Phân phối thiết bị câu cá High-end nhập khẩu chính hãng từ Nhật Bản, Mỹ và Đức.</p>
        <a href="MID/categories.php" class="bg-amber-500 text-black font-bold px-6 py-3 rounded text-xs uppercase transition tracking-wider inline-block hover:bg-amber-600" data-aos="zoom-in">Khám Phá Cửa Hàng Ngay</a>
    </div>
</div>

<div class="container mx-auto px-4 py-16 max-w-7xl">
    <div class="text-center mb-10" data-aos="fade-up">
        <h2 class="text-xl font-black text-gray-900 uppercase">Sản Phẩm Mới Về</h2>
        <p class="text-xs text-gray-400 mt-1">Cập nhật những mẫu cần câu, máy câu công nghệ mới nhất thị trường.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php if($products && $products->num_rows > 0): ?>
            <?php while($row = $products->fetch_assoc()): ?>
                <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between text-xs font-medium" data-aos="fade-up">
                    <a href="MID/detail.php?id=<?php echo $row['id']; ?>" class="block">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" class="w-full h-44 object-cover">
                        <div class="p-4">
                            <span class="text-[10px] uppercase font-bold text-gray-400 block mb-1"><?php echo htmlspecialchars($row['brand']); ?></span>
                            <h3 class="font-bold text-sm text-gray-900 line-clamp-2 min-h-[40px]"><?php echo htmlspecialchars($row['product_name']); ?></h3>
                            <div class="text-sm font-black text-red-600 mt-2"><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</div>
                        </div>
                    </a>
                    <div class="p-4 pt-0">
                        <a href="MID/cart.php?action=add&id=<?php echo $row['id']; ?>" class="w-full block text-center bg-[#0A2540] text-white py-2 rounded text-[11px] font-bold uppercase hover:bg-blue-900 transition">🛒 Thêm giỏ hàng</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <div class="text-center mt-12" data-aos="zoom-in">
        <a href="MID/categories.php" class="inline-block border-2 border-[#0A2540] text-[#0A2540] hover:bg-[#0A2540] hover:text-white font-bold px-8 py-3 rounded-lg text-xs uppercase tracking-widest transition">
            Xem Tất Cả Sản Phẩm ➡️
        </a>
    </div>
</div>

<?php include_once 'MID/footer.php'; ?>