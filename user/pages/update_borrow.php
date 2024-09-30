<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối

// Lấy thông tin từ biểu mẫu
$loan_id = $_POST['loan_id'] ?? null;
$new_device_id = $_POST['id_device'] ?? null;

// Lấy thông tin thiết bị mới một lần
$new_product_query = "SELECT device_name, price FROM products WHERE id_device = ?";
$stmt_new_product = $conn->prepare($new_product_query);
$stmt_new_product->bind_param("i", $new_device_id);
$stmt_new_product->execute();
$new_product_result = $stmt_new_product->get_result()->fetch_assoc();

// Cập nhật phiếu mượn
$update_loan_query = "UPDATE loan_records SET id_device = ?, device_name = ?, device_price = ?, loan_date = NOW() WHERE loan_id = ?";
$stmt = $conn->prepare($update_loan_query);
$stmt->bind_param("isii", $new_device_id, $new_product_result['device_name'], $new_product_result['price'], $loan_id);


if ($stmt->execute()) {
    // Cập nhật trạng thái sản phẩm cũ về có sẵn
    $update_old_product_query = "UPDATE products SET status = 'available' WHERE id_device = ?";
    $stmt_old = $conn->prepare($update_old_product_query);
    $stmt_old->bind_param("i", $old_loan_data['id_device']);
    $stmt_old->execute();

    // Cập nhật trạng thái sản phẩm mới về đã cho mượn
    $update_new_product_query = "UPDATE products SET status = 'rented' WHERE id_device = ?";
    $stmt_new = $conn->prepare($update_new_product_query);
    $stmt_new->bind_param("i", $new_device_id);
    $stmt_new->execute();

    // Thông báo thành công
    setcookie('message', 'Cập nhật phiếu mượn thành công!', time() + 60, "/");
} else {
    // Thông báo lỗi
    setcookie('message', 'Lỗi trong quá trình cập nhật phiếu mượn: ' . $stmt->error, time() + 60, "/");
}

// Chuyển hướng về trang home
header("Location: user.php?page=home");
exit();
?>
