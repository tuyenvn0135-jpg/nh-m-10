<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KingFisher - Đồ Câu Cao Cấp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        #mobile-menu {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    <header class="bg-[#0A2540] text-white sticky top-0 z-50 shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            
            <a href="/index.php" class="text-2xl font-black tracking-wider text-white animate__animated animate__fadeInLeft">
                <span class="text-amber-400">King</span> Fisher
            </a>

            <nav class="hidden md:flex items-center space-x-6 text-sm font-semibold text-slate-300">
                <a href="/index.php" class="hover:text-white transition">Trang Chủ</a>
                <a href="/MID/blog.php" class="hover:text-white transition">Cẩm Nang Cần Thủ</a>
                <a href="/MID/cart.php" class="hover:text-white transition">Giỏ Hàng</a>
                <a href="/MID/contact.php" class="hover:text-amber-400 transition">Liên Hệ</a>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 2): ?>
                    <a href="/toroDMIN/admin.php" class="bg-amber-500 text-slate-950 px-3 py-1.5 rounded-md hover:bg-amber-400 font-bold transition shadow-sm uppercase text-[11px] tracking-wider">
                        👑 Quản Trị
                    </a>
                <?php endif; ?>
            </nav>

            <div class="hidden md:flex items-center space-x-4 text-xs font-medium">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="text-slate-400">Chào, <a href="/MID/profile.php" class="text-amber-400 font-bold hover:underline">👤 <?php echo htmlspecialchars($_SESSION['fullname'] ?? 'Cần Thủ'); ?></a></span>
                    <span class="text-slate-600">|</span>
                    <a href="/MID/logout.php" class="text-red-400 hover:underline">Thoát</a>
                <?php else: ?>
                    <a href="/MID/login.php" class="hover:text-amber-400 transition">Đăng Nhập</a>
                    <span class="text-slate-600">|</span>
                    <a href="/MID/register.php" class="text-amber-400 hover:underline">Đăng Ký</a>
                <?php endif; ?>
            </div>

            <button id="menu-btn" class="block md:hidden text-slate-300 hover:text-white focus:outline-none p-2 rounded border border-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-[#071d33] border-t border-slate-800 px-4 py-4 space-y-3 font-semibold text-sm">
            <a href="/index.php" class="block text-slate-300 hover:text-white py-1">Trang Chủ</a>
            <a href="/MID/blog.php" class="block text-slate-300 hover:text-white py-1">Cẩm Nang Cần Thủ</a>
            <a href="/MID/cart.php" class="block text-slate-300 hover:text-white py-1">Giỏ Hàng</a>
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 2): ?>
                <a href="/toroDMIN/admin.php" class="block bg-amber-500 text-slate-950 px-3 py-2 rounded font-bold text-center uppercase text-xs">👑 Ban Quản Trị</a>
            <?php endif; ?>

            <div class="pt-3 border-t border-slate-800 flex justify-between items-center text-xs">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="text-slate-400">Chào, <a href="/MID/profile.php" class="text-yellow-400 font-bold hover:underline">👤 <?php echo htmlspecialchars($_SESSION['fullname'] ?? 'Cần thủ'); ?></a></span>
                    <a href="/MID/logout.php" class="text-red-400 font-bold">Thoát</a>
                <?php else: ?>
                    <a href="/MID/login.php" class="text-slate-300 hover:text-amber-400">Đăng Nhập</a>
                    <a href="/MID/register.php" class="text-amber-400 font-bold">Đăng Ký</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <script>
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>