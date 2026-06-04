<?php
require_once('../config.php');
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) { die("Từ chối truy cập."); }

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = trim($_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $price = doubleval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $image = trim($_POST['image']);
    $brand = trim($_POST['brand']);
    $specifications = trim($_POST['specifications']);
    $origin = trim($_POST['origin']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("INSERT INTO products (product_name, category_id, price, quantity, image, brand, specifications, origin, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sidssssss", $product_name, $category_id, $price, $quantity, $image, $brand, $specifications, $origin, $description);
    
    if ($stmt->execute()) {
        // ĐÃ SỬA: Chuyển hướng thẳng về admin.php cùng cấp
        header("Location: admin.php");
        exit;
    } else {
        $msg = "❌ Lỗi hệ thống, không thể lưu mặt hàng.";
    }
}

$categories = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Đồ Câu Mới</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl border shadow-sm text-xs font-medium">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h2 class="text-xl font-black text-gray-800">➕ THÊM SẢN PHẨM ĐỒ CÂU</h2>
            <a href="admin.php" class="text-gray-500 hover:underline">← Quay lại</a>
        </div>

        <?php if(!empty($msg)) echo "<div class='p-3 bg-red-50 text-red-700 mb-4 rounded'>$msg</div>"; ?>

        <form action="admin_add.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-500 mb-1">Tên sản phẩm *</label>
                <input type="text" name="product_name" required class="w-full border p-2 text-sm rounded outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-500 mb-1">Danh mục đồ câu</label>
                    <select name="category_id" class="w-full border p-2 text-sm rounded outline-none">
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Thương hiệu *</label>
                    <input type="text" name="brand" placeholder="Shimano, Daiwa..." required class="w-full border p-2 text-sm rounded outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-500 mb-1">Giá bán (đ) *</label>
                    <input type="number" name="price" required class="w-full border p-2 text-sm rounded outline-none">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Số lượng nhập kho *</label>
                    <input type="number" name="quantity" required class="w-full border p-2 text-sm rounded outline-none">
                </div>
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Đường dẫn ảnh (URL) *</label>
                <input type="text" name="image" required class="w-full border p-2 text-sm rounded outline-none">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded text-xs uppercase">Lưu sản phẩm</button>
        </form>
    </div>
</body>
</html>