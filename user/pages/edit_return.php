<link rel="stylesheet" href="../style/edit_records.css">

<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối

// Lấy ID phiếu trả từ URL
$return_id = $_GET['return_id'] ?? null;

// Lấy thông tin phiếu trả
$return_query = "SELECT * FROM return_records WHERE return_id = ?";
$stmt = $conn->prepare($return_query);
$stmt->bind_param("i", $return_id);
$stmt->execute();
$return_result = $stmt->get_result();

if ($return_result->num_rows === 0) {
    echo "Không tìm thấy phiếu trả.";
    exit();
}

$return_data = $return_result->fetch_assoc();

// Lấy danh sách thiết bị đã mượn của người dùng
$user_id = isset($_COOKIE['user_id']) ? intval($_COOKIE['user_id']) : null;
$loan_query = "SELECT * FROM loan_records WHERE user_id = ?";
$loan_stmt = $conn->prepare($loan_query);
$loan_stmt->bind_param("i", $user_id);
$loan_stmt->execute();
$loan_result = $loan_stmt->get_result();

?>

<h2>Sửa phiếu trả</h2>

<div class="form-container">
    <!-- Thông tin thiết bị hiện tại đã trả -->
    <div class="old-device-info">
        <h3>Thông tin phiếu trả hiện tại</h3>
        <p>ID Thiết bị: <?php echo htmlspecialchars($return_data['id_device']); ?></p>
        <p>Tên thiết bị: <?php echo htmlspecialchars($return_data['device_name']); ?></p>
        <p>Giá: <?php echo htmlspecialchars($return_data['device_price']); ?></p>
        <p>User ID: <?php echo htmlspecialchars($return_data['user_id']); ?></p>
        <p>Username: <?php echo htmlspecialchars($return_data['username']); ?></p>
        <p>Ngày trả: <?php echo htmlspecialchars($return_data['return_date']); ?></p>
    </div>

    <!-- Form chọn thiết bị mới để trả và hiển thị thông tin đầy đủ -->
    <div class="new-device-info">
        <h3>Chọn thiết bị để sửa phiếu trả</h3>
        <form action="user.php?page=update_return" method="POST">
            <input type="hidden" name="return_id" value="<?php echo $return_id; ?>">
            <label for="id_device">Chọn thiết bị mới:</label>
            <select name="id_device" id="id_device" required>
                <?php while ($loan = $loan_result->fetch_assoc()) : ?>
                    <option value="<?php echo $loan['id_device']; ?>" 
                            data-device-name="<?php echo htmlspecialchars($loan['device_name']); ?>" 
                            data-device-price="<?php echo htmlspecialchars($loan['device_price']); ?>">
                        <?php echo htmlspecialchars($loan['device_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Khu vực hiển thị thông tin đầy đủ của thiết bị mới -->
            <div id="device-details">
                <p>Tên thiết bị: <span id="device_name"></span></p>
                <p>Giá: <span id="device_price"></span></p>
            </div>

            <input type="submit" value="Cập nhật">
        </form>
    </div>
</div>

<script>
    document.getElementById('id_device').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    document.getElementById('device_name').textContent = selectedOption.getAttribute('data-device-name');
    document.getElementById('device_price').textContent = selectedOption.getAttribute('data-device-price');
});

</script>


<?php
// Đóng kết nối
$conn->close();
?>
