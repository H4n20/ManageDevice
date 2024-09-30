
<?php
// Khởi tạo biến tìm kiếm
$name = isset($_GET['device_name']) ? $_GET['device_name'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';



// Tạo câu truy vấn dựa trên các tiêu chí tìm kiếm
$query = "SELECT * FROM products WHERE 1=1";

// Điều kiện tìm kiếm theo tên sản phẩm (nếu có nhập)
if (!empty($name)) {
    $query .= " AND device_name LIKE '%" . $conn->real_escape_string($name) . "%'";
}

// Điều kiện tìm kiếm theo giá (nếu có nhập giá tối thiểu hoặc tối đa)
if (!empty($min_price)) {
    $query .= " AND price >= " . (int)$min_price;
}

if (!empty($max_price)) {
    $query .= " AND price <= " . (int)$max_price;
}

$result = $conn->query($query);

if (!$result) {
    die("Lỗi truy vấn sản phẩm: " . $conn->error); // Hiển thị lỗi nếu truy vấn không thành công
}

?>

<div class="product-search">
    <form method="GET" action="admin.php?page=home">
        <label for="device_name">Tên sản phẩm:</label>
        <input type="text" id="device_name" name="device_name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Nhập tên sản phẩm">

        <label for="min_price">Giá từ:</label>
        <input type="number" id="min_price" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" placeholder="Nhập giá thấp nhất">

        <label for="max_price">Đến:</label>
        <input type="number" id="max_price" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" placeholder="Nhập giá cao nhất">

        <button type="submit">Tìm kiếm</button>
    </form>
</div>
