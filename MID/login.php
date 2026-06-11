<?php
if (!isset($_SESSION)) { session_start(); }
error_reporting(E_ALL);
ini_set('display_errors', 1);

$msg = "";
$base_dir = dirname(__DIR__); 

// XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT ĐĂNG NHẬP
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (file_exists($base_dir . '/config.php')) {
        require_once $base_dir . '/config.php';
    } else {
        $msg = "❌ Không tìm thấy file config.php ở đường dẫn: " . $base_dir . '/config.php';
    }

    if (empty($msg)) {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (isset($conn)) {
            // Lấy thông tin user (Bỏ qua luôn cột is_verified cho nhẹ nợ)
            $stmt = $conn->prepare("SELECT id, username, password, email, role FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Kiểm tra mật khẩu mã hóa
                if (password_verify($password, $user['password'])) {
                    
                    // LƯU BIẾN SESSION ĐỂ NHẬN DIỆN ĐĂNG NHẬP
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role']; 

                    // PHÂN QUYỀN ĐƯỜNG ĐI
                    if ($user['role'] == 1 || $user['role'] == 'admin') { 
                        // Nếu là Admin -> Vào trang quản trị
                        header("Location: ../admin/index.php");
                        exit;
                    } else {
                        // Nếu là Người dùng thường -> Vào thẳng trang chủ luôn, không OTP gì hết!
                        header("Location: ../index.php");
                        exit;
                    }
                } else {
                    $msg = "❌ Mật khẩu không chính xác!";
                }
            } else {
                $msg = "❌ Tài khoản không tồn tại trên hệ thống!";
            }
        } else {
            $msg = "❌ Biến kết nối Database (\$conn) chưa được cấu hình đúng!";
        }
    }
}

if (file_exists('header.php')) { include_once 'header.php'; }
?>

<div style="max-width: 400px; margin: 60px auto; padding: 30px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; font-family: sans-serif;">
    <h2 style="text-align: center; margin-bottom: 24px; color: #0a2540; font-weight: 800;">ĐĂNG NHẬP CẦN THỦ</h2>
    
    <?php if(!empty($msg)): ?>
        <div style="padding: 12px; background: #fff5f5; color: #c53030; border-radius: 6px; font-size: 13px; margin-bottom: 16px; border: 1px solid #fed7d7; line-height: 1.5;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
        <div>
            <label style="display: block; font-size: 12px; color: #718096; margin-bottom: 4px; font-weight: bold;">Tên đăng nhập hoặc Email</label>
            <input type="text" name="username" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
        </div>
        <div>
            <label style="display: block; font-size: 12px; color: #718096; margin-bottom: 4px; font-weight: bold;">Mật khẩu</label>
            <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
        </div>
        <button type="submit" style="width: 100%; background: #0a2540; color: #fff; padding: 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; text-transform: uppercase; margin-top: 8px; letter-spacing: 0.5px;">Đăng Nhập</button>
    </form>
</div>

<?php if (file_exists('footer.php')) { include_once 'footer.php'; } ?>