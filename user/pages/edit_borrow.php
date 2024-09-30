<link rel="stylesheet" href="../style/edit_records.css">
<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối

// Lấy ID phiếu mượn từ URL
$loan_id = $_GET['loan_id'] ?? null;

// Lấy thông tin phiếu mượn
$loan_query = "SELECT * FROM loan_records WHERE loan_id = ?";
$stmt = $conn->prepare($loan_query);
$stmt->bind_param("i", $loan_id);
$stmt->execute();
$loan_result = $stmt->get_result();

if ($loan_result->num_rows === 0) {
    echo "Không tìm thấy phiếu mượn.";
    exit();
}

$loan_data = $loan_result->fetch_assoc();

// Lấy danh sách sản phẩm có thể chọn
$product_query = "SELECT * FROM products WHERE status != 'rented'";
$product_result = $conn->query($product_query);

?>

<h2>Sửa phiếu mượn</h2>

<div class="form-container">
    <!-- Thông tin thiết bị cũ -->
    <div class="old-device-info">
        <h3>Thông tin phiếu mượn hiện tại</h3>
        <p>ID Thiết bị: <?php echo htmlspecialchars($loan_data['id_device']); ?></p>
        <p>Tên thiết bị: <?php echo htmlspecialchars($loan_data['device_name']); ?></p>
        <p>Giá: <?php echo htmlspecialchars($loan_data['device_price']); ?></p>
        <p>User ID: <?php echo htmlspecialchars($loan_data['user_id']); ?></p>
        <p>Username: <?php echo htmlspecialchars($loan_data['username']); ?></p>
        <p>Ngày mượn: <?php echo htmlspecialchars($loan_data['loan_date']); ?></p>
    </div>

    <!-- Form chọn thiết bị mới và hiển thị thông tin mới -->
    <div class="new-device-info">
        <h3>Thông tin thiết bị mới</h3>
        <form action="user.php?page=update_borrow" method="POST">
            <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">
            <label for="id_device">Chọn thiết bị mới:</label>
            <select name="id_device" id="id_device" required>
                <?php while ($product = $product_result->fetch_assoc()) : ?>
                    <option value="<?php echo $product['id_device']; ?>" 
                            data-device-name="<?php echo htmlspecialchars($product['device_name']); ?>" 
                            data-device-price="<?php echo htmlspecialchars($product['price']); ?>" 
                            data-device-type="<?php echo htmlspecialchars($product['type']); ?>" 
                            data-device-status="<?php echo htmlspecialchars($product['status']); ?>"
                            data-device-image="<?php echo htmlspecialchars($product['image']); ?>">
                        <?php echo htmlspecialchars($product['device_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Khu vực hiển thị thông tin đầy đủ của thiết bị mới -->
            <div id="device-details">
                <p>Tên thiết bị: <span id="device_name"></span></p>
                <p>Giá: <span id="device_price"></span></p>
                <p>Loại: <span id="device_type"></span></p>
                <p>Trạng thái: <span id="device_status"></span></p>
                <p>Hình ảnh:</p>
                <img id="device_image" src="" alt="Hình ảnh thiết bị" style="max-width: 200px;">
            </div>

            <input type="submit" value="Cập nhật">
        </form>
    </div>
</div>


<script>
// Hàm cập nhật thông tin thiết bị khi người dùng chọn từ danh sách
document.getElementById('id_device').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    document.getElementById('device_name').textContent = selectedOption.getAttribute('data-device-name');
    document.getElementById('device_price').textContent = selectedOption.getAttribute('data-device-price');
    document.getElementById('device_type').textContent = selectedOption.getAttribute('data-device-type');
    document.getElementById('device_status').textContent = selectedOption.getAttribute('data-device-status');
    
    // Cập nhật hình ảnh thiết bị
    var imagePath = selectedOption.getAttribute('data-device-image');
    document.getElementById('device_image').src = imagePath ? imagePath : '';
});

// Cập nhật thông tin thiết bị đầu tiên khi trang được tải
document.getElementById('id_device').dispatchEvent(new Event('change'));
</script>

<?php
// Đóng kết nối
$conn->close();
?>
