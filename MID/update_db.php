// Bổ sung bảng bài viết (blogs) vào database nếu chưa có
$conn->query("CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    summary TEXT,
    content TEXT,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Chèn bài viết mẫu nếu bảng trống
$checkBlogs = $conn->query("SELECT id FROM blogs LIMIT 1");
if ($checkBlogs->num_rows == 0) {
    $conn->query("INSERT INTO blogs (title, summary, content, image) VALUES 
    ('Kỹ thuật quăng mồi Lure xa bờ cho người mới', 'Chia sẻ mẹo thắt nút dây PE và cách điều chỉnh lực phanh máy ngang để không bị rối rác.', 'Nội dung chi tiết bài viết hướng dẫn cần thủ căn góc quăng mồi hiệu quả...', 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=500'),
    ('Cách bảo dưỡng ổ bi (Bearing) máy câu Shimano', 'Hướng dẫn tra dầu chuyên dụng giúp máy câu hoạt động mượt mà sau khi câu biển.', 'Chi tiết quy trình tháo bánh răng và vệ sinh muối mặn bám trên máy câu...', 'https://images.unsplash.com/photo-1611095777215-6f1e1058379f?w=500')");
}