<?php
require_once('../config.php');
include_once 'header.php';

// Lấy ID bài viết từ URL thanh địa chỉ
$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Truy vấn lấy dữ liệu chi tiết
$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$blog = $stmt->get_result()->fetch_assoc();

// Nếu không tồn tại bài viết này thì đẩy ngược về trang danh sách blog
if (!$blog) {
    echo "<script>window.location.href='blog.php';</script>";
    exit;
}
?>

<div class="container mx-auto px-4 py-10 max-w-3xl">
    <div class="text-[11px] text-gray-400 mb-4 font-medium">
        <a href="../index.php" class="hover:underline">Trang chủ</a> / 
        <a href="blog.php" class="hover:underline">Cẩm Nang Cần Thủ</a> / 
        <span class="text-gray-600"><?php echo htmlspecialchars($blog['title']); ?></span>
    </div>

    <article class="bg-white p-6 md:p-8 rounded-xl border shadow-sm" data-aos="fade-up">
        <h1 class="text-xl md:text-2xl font-black text-gray-950 leading-snug mb-3">
            <?php echo htmlspecialchars($blog['title']); ?>
        </h1>
        
        <div class="text-gray-400 text-[11px] font-mono mb-6 pb-4 border-b">
            📅 Đăng ngày: <?php echo date('d/m/Y', strtotime($blog['created_at'])); ?> | 👤 Tác giả: Ban quản trị KingFisher
        </div>

        <img src="<?php echo htmlspecialchars($blog['image']); ?>" class="w-full h-64 md:h-80 object-cover rounded-xl border mb-6 shadow-sm">

        <div class="text-gray-700 leading-relaxed font-normal text-xs space-y-4">
            <?php echo $blog['content']; ?>
        </div>
        
        <div class="border-t pt-6 mt-8 text-center">
            <a href="blog.php" class="inline-block bg-[#0A2540] text-white font-bold px-6 py-2.5 rounded-lg text-xs uppercase tracking-wider hover:bg-slate-800 transition">
                ⬅ Back về danh sách cẩm nang
            </a>
        </div>
    </article>
</div>

<?php include_once 'footer.php'; ?>