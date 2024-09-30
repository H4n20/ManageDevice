<?php
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối

// Kiểm tra cookie để xác định người dùng
$user_id = isset($_COOKIE['user_id']) ? intval($_COOKIE['user_id']) : null;

// Nếu không có user_id trong cookie, chuyển hướng về trang khác hoặc thông báo lỗi
if ($user_id === null) {
    die("Không xác định được người dùng.");
}

// Truy vấn tất cả sản phẩm để hiển thị
$product_query = "SELECT * FROM products";
$product_result = $conn->query($product_query);

// Truy vấn kiểm tra xem người dùng đã mượn thiết bị nào mà chưa trả
$loan_status_query = "SELECT lr.id_device
                    FROM loan_records lr
                    LEFT JOIN return_records rr ON lr.id_device = rr.id_device AND lr.user_id = rr.user_id
                    WHERE lr.user_id = ?
                    AND lr.loan_date > COALESCE(rr.return_date, '1970-01-01')";
$loan_stmt = $conn->prepare($loan_status_query);
$loan_stmt->bind_param("i", $user_id); // Đây là đúng
$loan_stmt->execute();
$loan_status_result = $loan_stmt->get_result();

// $loan_status_result = $conn->query($loan_status_query);

// Kiểm tra xem truy vấn có thành công không
if ($loan_status_result === false) {
    die("Lỗi truy vấn: " . $conn->error);
}

// Lưu thông tin các thiết bị đang được mượn bởi người dùng vào một mảng
$loaned_devices = [];
while ($row = $loan_status_result->fetch_assoc()) {
    $loaned_devices[] = $row['id_device'];
}
?>

<div class="product-head-content">
    <h2>Danh sách sản phẩm</h2>
</div>

<!-- Gọi form tìm kiếm -->
<?php require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/function/search.php'; // Đường dẫn tuyệt đối?>

<!-- Hiển thị thông báo -->
<?php
// Kiểm tra xem cookie 'message' có tồn tại không
if (isset($_COOKIE['message'])): ?>
    <div class="message">
        <p><?php echo $_COOKIE['message']; ?></p>
    </div>
    <?php
    // Xóa cookie sau khi hiển thị thông báo để tránh hiển thị lại
    setcookie('message', '', time() - 3600, "/");
endif;
?>

<!-- Hiển thị thiết bị -->
<div class="product-grid-container">
    <?php while ($product = $product_result->fetch_assoc()): ?>
        <div class="product-grid-item">
            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['device_name']; ?>">
            <h3><?php echo $product['device_name']; ?></h3>
            <p>Loại: <?php echo $product['type']; ?></p>
            <p>Giá: <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</p>
            <p>Tình trạng: <?php echo $product['status'] == 'rented' ? 'Đang được cho mượn' : 'Rảnh rỗi'; ?></p>

            <?php if ($product['status'] != 'rented'): ?>
                <!-- Hiển thị nút "Mượn" nếu sản phẩm chưa được mượn -->
                <button class="product-button product-button-borrow" onclick="borrowProduct(<?php echo $product['id_device']; ?>)">Mượn</button>
            <?php elseif (in_array($product['id_device'], $loaned_devices)): ?>
                <!-- Chỉ hiển thị nút "Trả" với người đang mượn thiết bị này -->
                <button class="product-button product-button-return" onclick="returnProduct(<?php echo $product['id_device']; ?>)">Trả</button>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>

<script>
    function borrowProduct(productId) {
        window.location.href = `user.php?page=borrow&id_device=${productId}`;
    }
    function returnProduct(productId) {
        window.location.href = `user.php?page=return&id_device=${productId}`;
    }
</script>
