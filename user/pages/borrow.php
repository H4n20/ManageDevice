<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối

// Lấy ID sản phẩm từ URL
$product_id = $_GET['id_device'] ?? null;

// Kiểm tra user_id từ cookie
$user_id = isset($_COOKIE['user_id']) ? intval($_COOKIE['user_id']) : null;

$message = ''; // Biến để chứa thông báo

// Kiểm tra sản phẩm
$product_check_query = "SELECT * FROM products WHERE id_device = ?";
$stmt = $conn->prepare($product_check_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_check_result = $stmt->get_result();
$product = $product_check_result->fetch_assoc();

if (!$product) {
    setcookie('message', 'Sản phẩm không tồn tại!', time() + 60, "/");
    header("Location: user.php?page=home");
    exit();
}

if ($product['status'] != 'rented') {
    // Kiểm tra xem người dùng có đã mượn thiết bị này chưa và chưa trả
    $previous_loan_check_query = "SELECT * FROM loan_records WHERE id_device = ? AND user_id = ?";
    $previous_stmt = $conn->prepare($previous_loan_check_query);
    $previous_stmt->bind_param("ii", $product_id, $user_id);
    $previous_stmt->execute();
    $previous_loan_result = $previous_stmt->get_result();

    if ($previous_loan_result->num_rows > 0) {
        // Nếu có phiếu mượn mà chưa trả, thông báo lỗi
        setcookie('message', 'Bạn đã mượn thiết bị này và chưa trả lại!', time() + 60, "/");
    } else {
        // Thêm phiếu mượn vào loan_records
        $loan_query = "INSERT INTO loan_records (id_device, device_name, device_price, user_id, username, loan_date) VALUES (?, ?, ?, ?, ?, NOW())";
        
        // Kiểm tra xem việc chuẩn bị câu lệnh có thành công không
        $stmt = $conn->prepare($loan_query);
        if ($stmt === false) {
            die("Lỗi trong câu lệnh SQL: " . $conn->error);
        }

        $device_name = $product['device_name'];
        $device_price = $product['price'];
        $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'Unknown'; // Lấy tên người dùng từ cookie nếu có

        $stmt->bind_param("isdis", $product_id, $device_name, $device_price, $user_id, $username);
        
        if ($stmt->execute()) {
            // Cập nhật trạng thái sản phẩm
            $update_product_query = "UPDATE products SET status = 'rented' WHERE id_device = ?";
            $update_stmt = $conn->prepare($update_product_query);
            $update_stmt->bind_param("i", $product_id);
            $update_stmt->execute();
        
            // Lưu thông báo vào cookie, thời gian sống của cookie là 1 phút
            setcookie('message', 'Bạn đã mượn thiết bị thành công!', time() + 60, "/");
        } else {
            // Lưu thông báo lỗi vào cookie
            setcookie('message', 'Lỗi trong quá trình mượn thiết bị: ' . $stmt->error, time() + 60, "/");
        }
    }
}

// Chuyển hướng về trang home
header("Location: user.php?page=home");
exit();
?>
