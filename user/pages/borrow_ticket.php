<link rel="stylesheet" href="../style/record.css">
<?php
// Kết nối đến cơ sở dữ liệu
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối

// Lấy user_id từ cookie hoặc session
$user_id = isset($_COOKIE['user_id']) ? intval($_COOKIE['user_id']) : null;

// Kiểm tra nếu không có user_id
if ($user_id === null) {
    echo "Vui lòng đăng nhập để xem các phiếu mượn của bạn.";
    exit();
}

// Truy vấn chỉ những phiếu mượn do người dùng hiện tại tạo ra
$sql = "SELECT * FROM loan_records WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra và hiển thị dữ liệu
if ($result->num_rows > 0) {
    echo "<h2>Danh sách phiếu mượn của bạn</h2>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>ID Thiết bị</th>
                <th>Tên thiết bị</th>
                <th>Giá</th>
                <th>User ID</th>
                <th>Username</th>
                <th>Ngày mượn</th>
                <th>Hành động</th> <!-- Thêm cột Hành động -->
            </tr>";
    // Xuất dữ liệu của từng hàng
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["loan_id"]) . "</td>
                <td>" . htmlspecialchars($row["id_device"]) . "</td>
                <td>" . htmlspecialchars($row["device_name"]) . "</td>
                <td>" . htmlspecialchars($row["device_price"]) . "</td>
                <td>" . htmlspecialchars($row["user_id"]) . "</td>
                <td>" . htmlspecialchars($row["username"]) . "</td>
                <td>" . htmlspecialchars($row["loan_date"]) . "</td>
                <td><a href='user.php?page=edit_borrow&loan_id=" . htmlspecialchars($row["loan_id"]) . "'>Sửa</a></td> <!-- Nút sửa -->
            </tr>";
    }
    echo "</table>";
} else {
    echo "Không có phiếu mượn nào.";
}

// Đóng kết nối
$conn->close();
?>
