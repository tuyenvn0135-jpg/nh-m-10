<?php
require_once('../config.php');

// 1. Tạo Database nếu chưa có
$conn->query("CREATE DATABASE IF NOT EXISTS kingfisher_data CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db("kingfisher_data");

// 2. Tạo bảng users
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    fullname VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    role TINYINT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 3. Tạo bảng categories
$conn->query("CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 4. Tạo bảng products
$conn->query("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    category_id INT,
    price DOUBLE NOT NULL,
    quantity INT DEFAULT 0,
    image VARCHAR(255),
    brand VARCHAR(100),
    specifications TEXT,
    origin VARCHAR(100),
    description TEXT,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 5. Tạo bảng orders
$conn->query("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_price DOUBLE NOT NULL,
    status VARCHAR(50) DEFAULT 'Chờ xử lý',
    shipping_address TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 6. Tạo bảng order_details
$conn->query("CREATE TABLE IF NOT EXISTS order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DOUBLE NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 7. Chèn dữ liệu mẫu nếu bảng trống
$checkCats = $conn->query("SELECT id FROM categories LIMIT 1");
if ($checkCats->num_rows == 0) {
    $conn->query("INSERT INTO categories (category_name, description) VALUES 
    ('Cần câu cá', 'Các dòng cần lure, cần đài, câu lục cao cấp'),
    ('Máy câu cá', 'Máy đứng, máy ngang độ chính xác cao'),
    ('Phụ kiện & Mồi', 'Dây PE, phao câu, mồi giả cao cấp')");
    
    $conn->query("INSERT INTO products (product_name, category_id, price, quantity, image, brand, specifications, origin, description) VALUES 
    ('Cần câu Shimano Poison Adrena', 1, 8500000, 12, 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=500', 'Shimano', 'Chiều dài: 2.08m | Độ cứng: MH | Chất liệu: Carbon Monocoque', 'Nhật Bản', 'Dòng cần câu Lure đỉnh cao thuộc phân khúc High-end.'),
    ('Máy câu Daiwa Exist LT 2026', 2, 16000000, 5, 'https://images.unsplash.com/photo-1611095777215-6f1e1058379f?w=500', 'Daiwa', 'Trọng lượng: 160g | Ratio: 5.1:1 | Tải dây: PE 0.8', 'Nhật Bản', 'Cơ khí tinh xảo, hoạt động mượt mà trong mọi môi trường.'),
    ('Cần Câu Đài Khải Hoàn 5H', 1, 1250000, 30, 'https://images.unsplash.com/photo-1517462964-21fdcec3f25b?w=500', 'Nội địa', 'Chiều dài: 4.5m | Độ cứng: 5H | Phân bổ lực: 28', 'Đài Loan', 'Thích hợp cho cần thủ phong trào lẫn chuyên nghiệp thi đấu đài.'),
    ('Dây Câu PE YGK Bornrush X8', 3, 650000, 50, 'https://images.unsplash.com/photo-1541532713592-79a0317b6b77?w=500', 'YGK', 'Chiều dài: 200m | Kích thước: PE 1.0 | Lực tải: 22lb', 'Nhật Bản', 'Dây bện siêu mịn, hỗ trợ quăng mồi lure đi cực xa.')");
    
    echo "<div style='padding:20px; font-family:sans-serif; color:green;'>✔️ Đã khởi tạo cơ sở dữ liệu và nạp dữ liệu mẫu KingFisher thành công! <a href='index.php' style='color:blue; font-weight:bold;'>Đi tới Trang chủ</a></div>";
} else {
    echo "<div style='padding:20px; font-family:sans-serif; color:orange;'>⚠️ Cơ sở dữ liệu đã tồn tại sẵn từ trước. <a href='index.php' style='color:blue; font-weight:bold;'>Đi tới Trang chủ</a></div>";
}
?>