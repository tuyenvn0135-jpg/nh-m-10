<?php 
require_once('../config.php');
include_once 'header.php';

// Truy vấn lấy toàn bộ danh sách bài viết từ database
$blogs_res = $conn->query("SELECT * FROM blogs ORDER BY id DESC");
?>

<div class="container mx-auto px-4 py-10 max-w-5xl">
    <div class="mb-8" data-aos="fade-down">
        <h1 class="text-xl font-black text-gray-900 uppercase">📚 Cẩm Nang Cần Thủ</h1>
        <p class="text-xs text-gray-500 mt-1">Nơi chia sẻ kỹ thuật câu lure, kinh nghiệm chọn cần và bí quyết bảo dưỡng máy câu độc quyền từ KingFisher.</p>
    </div>

    <!-- Khối danh sách bài viết dạng Grid/Hàng -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php if($blogs_res && $blogs_res->num_rows > 0): ?>
            <?php while($row = $blogs_res->fetch_assoc()): ?>
                <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col md:flex-row p-4 gap-4 text-xs font-medium" data-aos="fade-up">
                    
                    <!-- Ảnh đại diện bài viết -->
                    <!-- SỬA LINK: Đổi thành /MID/blog_detail.php -->
                    <a href="/MID/blog_detail.php?id=<?php echo $row['id']; ?>" class="w-full md:w-40 h-28 shrink-0 block">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" class="w-full h-full object-cover rounded-lg border bg-gray-50">
                    </a>
                    
                    <!-- Khối chữ nội dung -->
                    <div class="flex flex-col justify-between flex-1">
                        <div>
                            <span class="text-[10px] uppercase font-mono text-gray-400 block mb-1">
                                📅 Ngày đăng: <?php echo date('d/m/Y', strtotime($row['created_at'] ?? date('Y-m-d'))); ?>
                            </span>
                            <h3 class="font-bold text-sm text-gray-900 line-clamp-2 hover:text-amber-500 transition mb-1.5">
                                <!-- SỬA LINK: Đổi thành /MID/blog_detail.php -->
                                <a href="/MID/blog_detail.php?id=<?php echo $row['id']; ?>">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </a>
                            </h3>
                            <p class="text-gray-500 text-[11px] font-normal line-clamp-2 leading-relaxed">
                                <?php echo htmlspecialchars($row['summary']); ?>
                            </p>
                        </div>
                        
                        <div class="mt-3">
                            <!-- SỬA LINK: Loại bỏ hoàn toàn /kingfisher/, chuyển thành /MID/blog_detail.php -->
                            <a href="/MID/blog_detail.php?id=<?php echo $row['id']; ?>" class="inline-block text-[#FF9F1C] font-black uppercase tracking-wider text-[11px] hover:underline">
                                Đọc bài viết khai sáng ➔
                            </a>
                        </div>
                    </div>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-2 text-center py-20 bg-white rounded-xl border border-dashed w-full">
                <p class="text-gray-400 text-sm">Chưa có bài viết cẩm nang nào được đăng tải trên hệ thống.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'footer.php'; ?>