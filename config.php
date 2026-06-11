<?php
$host = "sql311.infinityfree.com";         // Lấy từ MYSQL HOSTNAME
$user = "if0_42067510";                   // Lấy từ MYSQL USERNAME
$pass = "tuyenvn1134";               // Bấm nút hình con mắt ở mục MYSQL PASSWORD để lấy
$dbname = "if0_42067510_kingfisher_data"; // Lấy từ DATABASE NAME

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Kết nối database thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
$base_url = "https://kingfisher.infinityfreeapp.com/";
function sendEmailOTP($toEmail, $toName, $otpCode) {
    // Đăng ký tài khoản Brevo.com (miễn phí 300 mail/ngày), lấy API Key dán vào đây
    $apiKey = 'YOUR_BREVO_API_KEY_HERE'; 

    $url = 'https://api.brevo.com/v3/smtp/email';
    
    $data = [
        "sender" => ["name" => "KingFisher Shop", "email" => "no-reply@kingfisher.com"],
        "to" => [["email" => $toEmail, "name" => $toName]],
        "subject" => "🔐 MÃ OTP XÁC THỰC TÀI KHOẢN KINGFISHER",
        "htmlContent" => "<h3>Chào cần thủ {$toName},</h3>
                          <p>Mã OTP kích hoạt tài khoản của bạn là: <b style='font-size:22px; color:#0A2540;'>{$otpCode}</b></p>
                          <p>Vui lòng nhập mã này tại trang xác thực để hoàn tất đăng ký.</p>"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
?>