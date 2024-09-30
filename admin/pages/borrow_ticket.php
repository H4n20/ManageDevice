<link rel="stylesheet" href="../style/record.css">
<?php
// Kết nối đến cơ sở dữ liệu
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối


// Truy vấn dữ liệu
$sql = "SELECT * FROM loan_records";
$result = $conn->query($sql);

// Kiểm tra và hiển thị dữ liệu
if ($result->num_rows > 0) {
    echo"<h2>Danh sách phiếu mượn</h2>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>ID Thiết bị</th>
                <th>Tên thiết bị</th>
                <th>Giá</th>
                <th>User ID</th>
                <th>Username</th>
                <th>Ngày mượn</th>
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
            </tr>";
    }
    echo "</table>";
} else {
    echo "Không có dữ liệu.";
}

// Đóng kết nối
$conn->close();
?>
