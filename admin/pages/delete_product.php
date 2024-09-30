<?php
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối

// Lấy ID sản phẩm từ POST
$product_id = $_POST['id_device'] ?? null;

if ($product_id) {
    // Chuẩn bị truy vấn để xóa sản phẩm
    $stmt = $conn->prepare("DELETE FROM products WHERE id_device = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Sản phẩm đã được xóa thành công.']);
    } else {
        echo json_encode(['message' => 'Có lỗi xảy ra khi xóa sản phẩm: ' . htmlspecialchars($stmt->error)]);
    }

    $stmt->close();
} else {
    echo json_encode(['message' => 'Không tìm thấy ID sản phẩm.']);
}

$conn->close();
?>
