<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối

// Lấy thông tin từ biểu mẫu
$return_id = $_POST['return_id'] ?? null;
$new_device_id = $_POST['id_device'] ?? null;

// Lấy thông tin phiếu trả cũ
$old_return_query = "SELECT * FROM return_records WHERE return_id = ?";
$stmt = $conn->prepare($old_return_query);
$stmt->bind_param("i", $return_id);
$stmt->execute();
$old_return_result = $stmt->get_result();
$old_return_data = $old_return_result->fetch_assoc();

// Cập nhật phiếu trả
$update_return_query = "UPDATE return_records SET id_device = ?, device_name = (SELECT device_name FROM products WHERE id_device = ?), device_price = (SELECT price FROM products WHERE id_device = ?), return_date = NOW() WHERE return_id = ?";
$stmt = $conn->prepare($update_return_query);
$stmt->bind_param("iiii", $new_device_id, $new_device_id, $new_device_id, $return_id);

if ($stmt->execute()) {
    // Cập nhật trạng thái sản phẩm mới về đã trả
    $update_product_query = "UPDATE products SET status = 'available' WHERE id_device = ?";
    $stmt_product = $conn->prepare($update_product_query);
    $stmt_product->bind_param("i", $new_device_id);
    $stmt_product->execute();

    // Thông báo thành công
    setcookie('message', 'Cập nhật phiếu trả thành công!', time() + 60, "/");
} else {
    // Thông báo lỗi
    setcookie('message', 'Lỗi trong quá trình cập nhật phiếu trả: ' . $stmt->error, time() + 60, "/");
}

// Chuyển hướng về trang home
header("Location: user.php?page=home");
exit();
?>
