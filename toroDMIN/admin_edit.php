<?php
require_once('../config.php');
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) { die("Từ chối truy cập."); }

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = trim($_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $price = doubleval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $image = trim($_POST['image']);
    $brand = trim($_POST['brand']);

    $stmt = $conn->prepare("UPDATE products SET product_name=?, category_id=?, price=?, quantity=?, image=?, brand=? WHERE id=?");
    $stmt->bind_param("sidsssi", $product_name, $category_id, $price, $quantity, $image, $brand, $id);
    $stmt->execute();
    
    // ĐÃ SỬA: Điều hướng thẳng về trang admin.php cùng cấp
    header("Location: admin.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
if (!$item) { die("Sản phẩm không tồn tại."); }

$categories = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Thông Số Thiết Bị</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl border shadow-sm text-xs font-medium">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h2 class="text-xl font-black text-gray-800">⚙️ SỬA THÔNG SỐ SẢN PHẨM</h2>
            <a href="admin.php" class="text-gray-500 hover:underline">← Hủy bỏ</a>
        </div>

        <form action="admin_edit.php?id=<?php echo $id; ?>" method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-500 mb-1">Tên sản phẩm *</label>
                <input type="text" name="product_name" value="<?php echo htmlspecialchars($item['product_name']); ?>" required class="w-full border p-2 text-sm rounded outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-500 mb-1">Danh mục</label>
                    <select name="category_id" class="w-full border p-2 text-sm rounded outline-none">
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == $item['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Thương hiệu *</label>
                    <input type="text" name="brand" value="<?php echo htmlspecialchars($item['brand']); ?>" required class="w-full border p-2 text-sm rounded outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-500 mb-1">Giá bán *</label>
                    <input type="number" name="price" value="<?php echo $item['price']; ?>" required class="w-full border p-2 text-sm rounded outline-none">
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Tồn kho *</label>
                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" required class="w-full border p-2 text-sm rounded outline-none">
                </div>
            </div>
            <div>
                <label class="block text-gray-500 mb-1">URL Ảnh *</label>
                <input type="text" name="image" value="<?php echo htmlspecialchars($item['image']); ?>" required class="w-full border p-2 text-sm rounded outline-none">
            </div>
            <button type="submit" class="w-full bg-amber-500 text-black font-bold py-3 rounded text-xs uppercase">Cập nhật</button>
        </form>
    </div>
</body>
</html>