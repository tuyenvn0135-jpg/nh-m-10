<?php 
require_once('../config.php');
include_once 'header.php';

// Tiếp nhận tham số tìm kiếm và bộ lọc từ URL
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$brand_filter = isset($_GET['brand']) ? trim($_GET['brand']) : '';
$price_filter = isset($_GET['price']) ? trim($_GET['price']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
$cat_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Xây dựng câu lệnh SQL kết hợp đa dạng bộ lọc
$sql = "SELECT * FROM products WHERE 1=1";

if(!empty($search)){
    $sql .= " AND (product_name LIKE '%" . $conn->real_escape_string($search) . "%' OR brand LIKE '%" . $conn->real_escape_string($search) . "%')";
}
if(!empty($brand_filter)){
    $sql .= " AND brand = '" . $conn->real_escape_string($brand_filter) . "'";
}
if($cat_id > 0){
    $sql .= " AND category_id = " . $cat_id;
}
if(!empty($status_filter)){
    if($status_filter == 'new') $sql .= " AND id DESC"; 
    if($status_filter == 'sale') $sql .= " AND old_price > price"; 
    if($status_filter == 'hot') $sql .= " AND views > 50"; 
}
if(!empty($price_filter)){
    if($price_filter == 'under_1m') $sql .= " AND price < 1000000";
    if($price_filter == '1m_3m') $sql .= " AND price BETWEEN 1000000 AND 3000000";
    if($price_filter == '3m_10m') $sql .= " AND price BETWEEN 3000000 AND 10000000";
    if($price_filter == 'over_10m') $sql .= " AND price > 10000000";
}

$sql .= " ORDER BY id DESC";
$products = $conn->query($sql);
?>

<div class="container mx-auto px-4 py-10 max-w-7xl">

    <div class="mb-10" data-aos="fade-down">
        <h2 class="text-center text-xs font-black text-gray-400 uppercase tracking-widest mb-6">🎯 PHÂN LOẠI THIẾT BỊ ĐỒ CÂU CHUYÊN NGHIỆP</h2>
        <div class="grid grid-cols-3 sm:flex sm:flex-wrap justify-center gap-3 md:gap-4 text-xs font-bold">
            
            <a href="categories.php" class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border bg-white hover:border-[#0A2540] hover:shadow-md transition <?php echo ($cat_id == 0) ? 'border-2 border-[#0A2540] bg-blue-50/10' : ''; ?>">
                <span class="text-xl">📦</span>
                <span class="text-gray-700 text-center text-[11px] whitespace-nowrap px-2">Tất cả đồ câu</span>
            </a>
            
            <?php 
            $dummy_categories = [
                ['id' => 1, 'name' => 'Cần Câu Máy', 'icon' => '🎣'],
                ['id' => 2, 'name' => 'Máy Câu Khủng', 'icon' => '⚙️'],
                ['id' => 3, 'name' => 'Dây Câu Siêu Bền', 'icon' => '🧵'],
                ['id' => 4, 'name' => 'Mồi Lure Cao Cấp', 'icon' => '🐟'],
                ['id' => 5, 'name' => 'Thời Trang / Thùng', 'icon' => '🎒'],
                ['id' => 6, 'name' => 'Phụ Kiện Thẻo / Lưỡi', 'icon' => '✂️']
            ];
            
            foreach($dummy_categories as $cat):
                $is_selected = ($cat['id'] == $cat_id);
            ?>
                <a href="categories.php?category_id=<?php echo $cat['id']; ?>&brand=<?php echo urlencode($brand_filter); ?>&price=<?php echo urlencode($price_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                   class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border bg-white hover:border-[#0A2540] hover:shadow-md transition <?php echo $is_selected ? 'border-2 border-[#0A2540] bg-blue-50/20' : ''; ?>">
                    <span class="text-xl"><?php echo $cat['icon']; ?></span>
                    <span class="text-gray-700 text-center text-[11px] whitespace-nowrap px-2"><?php echo $cat['name']; ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <form action="" method="GET" class="bg-white p-4 rounded-xl border shadow-sm mb-8 grid grid-cols-1 md:grid-cols-4 gap-4" data-aos="fade-down">
        <div class="md:col-span-2">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Nhập tên sản phẩm, mã cần, công nghệ (VD: Twinpower, Stella...)" class="w-full border p-2.5 rounded-lg text-sm outline-none focus:border-blue-500">
        </div>
        <div>
            <select name="status" class="w-full border p-2.5 rounded-lg text-sm outline-none bg-gray-50 text-gray-700">
                <option value="">-- Tiêu điểm sản phẩm --</option>
                <option value="new" <?php if($status_filter=='new') echo 'selected'; ?>>🆕 Sản phẩm mới về</option>
                <option value="sale" <?php if($status_filter=='sale') echo 'selected'; ?>>🔥 Đang chương trình Sale</option>
                <option value="hot" <?php if($status_filter=='hot') echo 'selected'; ?>>👑 Hàng bán chạy / Đáng mua</option>
            </select>
        </div>
        <button type="submit" class="w-full bg-[#0A2540] hover:bg-blue-900 text-white font-bold p-2.5 rounded-lg text-sm transition uppercase tracking-wider">Tìm kiếm bộ lọc</button>
    </form>

    <div class="flex flex-col lg:flex-row gap-8">
        
        <aside class="w-full lg:w-1/4 bg-white p-6 rounded-xl shadow-sm h-fit border border-gray-100 text-xs font-semibold">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">⚙️ BỘ LỌC ĐA DẠNG</h3>
                <?php if(!empty($brand_filter) || !empty($price_filter) || !empty($search) || !empty($status_filter) || $cat_id > 0): ?>
                    <a href="categories.php" class="text-red-500 font-normal underline text-[11px]">Xóa bộ lọc</a>
                <?php endif; ?>
            </div>
            
            <form action="" method="GET" class="space-y-6">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="category_id" value="<?php echo $cat_id; ?>">
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">

                <div>
                    <label class="block uppercase text-gray-400 mb-2 tracking-wider">Thương hiệu Quốc tế</label>
                    <select name="brand" class="w-full border border-gray-300 rounded p-2 text-sm font-normal outline-none focus:border-blue-500 bg-white text-gray-700">
                        <option value="">-- Chọn thương hiệu --</option>
                        <option value="Shimano" <?php if($brand_filter=='Shimano') echo 'selected'; ?>>Shimano (Nhật Bản)</option>
                        <option value="Daiwa" <?php if($brand_filter=='Daiwa') echo 'selected'; ?>>Daiwa (Nhật Bản)</option>
                        <option value="Megabass" <?php if($brand_filter=='Megabass') echo 'selected'; ?>>Megabass (Premium Lure)</option>
                        <option value="Major Craft" <?php if($brand_filter=='Major Craft') echo 'selected'; ?>>Major Craft (Japan)</option>
                        <option value="YGK" <?php if($brand_filter=='YGK') echo 'selected'; ?>>YGK / G-Soul (Dây câu)</option>
                    </select>
                </div>

                <div>
                    <label class="block uppercase text-gray-400 mb-2 tracking-wider">Khoảng giá ngân sách</label>
                    <select name="price" class="w-full border border-gray-300 rounded p-2 text-sm font-normal outline-none focus:border-blue-500 bg-white text-gray-700">
                        <option value="">-- Chọn phân khúc giá --</option>
                        <option value="under_1m" <?php if($price_filter=='under_1m') echo 'selected'; ?>>Phân khúc bình dân (< 1 Triệu)</option>
                        <option value="1m_3m" <?php if($price_filter=='1m_3m') echo 'selected'; ?>>Phân khúc tầm trung (1Tr - 3Tr)</option>
                        <option value="3m_10m" <?php if($price_filter=='3m_10m') echo 'selected'; ?>>Phân khúc cận cao cấp (3Tr - 10Tr)</option>
                        <option value="over_10m" <?php if($price_filter=='over_10m') echo 'selected'; ?>>Phân khúc Ultra High-End (> 10Tr)</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-[#0A2540] hover:bg-slate-800 text-white font-bold py-2.5 rounded transition uppercase tracking-wider text-[11px] shadow-sm">Áp dụng bộ lọc</button>
            </form>
        </aside>

        <div class="flex-1">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php if($products && $products->num_rows > 0): ?>
                    <?php while($row = $products->fetch_assoc()): ?>
                        <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between text-xs font-medium" data-aos="fade-up">
                            <a href="detail.php?id=<?php echo $row['id']; ?>" class="block relative">
                                <span class="absolute top-2 left-2 bg-red-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded uppercase shadow-sm">Japan Spec</span>
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" class="w-full h-44 object-cover">
                                <div class="p-4">
                                    <span class="text-[10px] uppercase font-bold text-blue-600 block mb-1"><?php echo htmlspecialchars($row['brand']); ?></span>
                                    <h3 class="font-bold text-sm text-gray-900 line-clamp-2 min-h-[40px]"><?php echo htmlspecialchars($row['product_name']); ?></h3>
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="text-sm font-black text-red-600"><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</div>
                                    </div>
                                </div>
                            </a>
                            <div class="p-4 pt-0">
                                <a href="cart.php?action=add&id=<?php echo $row['id']; ?>" class="w-full block text-center bg-[#0A2540] text-white py-2 rounded text-[11px] font-bold uppercase hover:bg-blue-900 transition">🛒 Thêm giỏ hàng</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-4 text-center py-24 bg-white rounded-xl border border-dashed w-full">
                        <span class="text-3xl block mb-2">🔍</span>
                        <p class="text-gray-400 text-sm">Kho hàng chưa có thiết bị câu nào khớp với bộ lọc bạn chọn.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php include_once 'footer.php'; ?>