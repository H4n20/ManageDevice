<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối

// Lấy ID sản phẩm từ URL
$product_id = $_GET['id_device'] ?? null;

// Kiểm tra user_id từ cookie
$user_id = isset($_COOKIE['user_id']) ? intval($_COOKIE['user_id']) : null;

$message = ''; // Biến để chứa thông báo

// Kiểm tra sản phẩm đã mượn
$loan_check_query = "SELECT * FROM loan_records WHERE id_device = ? AND user_id = ?";
$stmt = $conn->prepare($loan_check_query);
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$loan_record = $result->fetch_assoc();

if ($loan_record) {
    // Thêm thông tin trả thiết bị vào return_records
    $return_query = "INSERT INTO return_records (id_device, device_name, device_price, user_id, username, return_date) VALUES (?, ?, ?, ?, ?, NOW())";
    
    // Kiểm tra xem việc chuẩn bị câu lệnh có thành công không
    $return_stmt = $conn->prepare($return_query);
    if ($return_stmt === false) {
        die("Lỗi trong câu lệnh SQL: " . $conn->error);
    }

    $device_name = $loan_record['device_name'];
    $device_price = $loan_record['device_price'];
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'Unknown'; // Lấy tên người dùng từ cookie nếu có

    $return_stmt->bind_param("isdis", $product_id, $device_name, $device_price, $user_id, $username);
    
    if ($return_stmt->execute()) {
        // Cập nhật trạng thái sản phẩm về 'rảnh rỗi'
        $update_product_query = "UPDATE products SET status = 'available' WHERE id_device = ?";
        $update_stmt = $conn->prepare($update_product_query);
        $update_stmt->bind_param("i", $product_id);
        $update_stmt->execute();

        // Lưu thông báo vào cookie, thời gian sống của cookie là 1 phút
        setcookie('message', 'Bạn đã trả thiết bị thành công!', time() + 60, "/");
    } else {
        // Lưu thông báo lỗi vào cookie
        setcookie('message', 'Lỗi trong quá trình trả thiết bị: ' . $return_stmt->error, time() + 60, "/");
    }
} else {
    // Nếu không tìm thấy phiếu mượn, lưu thông báo lỗi
    setcookie('message', 'Không tìm thấy phiếu mượn cho thiết bị này.', time() + 60, "/");
}

// Chuyển hướng về trang home
header("Location: user.php?page=home");
exit();
?>
