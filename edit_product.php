<?php
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối

$message = ""; // Biến để chứa thông báo

// Lấy ID sản phẩm từ URL
$product_id = $_GET['id_device'] ?? null;

if ($product_id) {
    // Lấy thông tin sản phẩm hiện tại
    $stmt = $conn->prepare("SELECT * FROM products WHERE id_device = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    // Xử lý khi form được submit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['device_name'];
        $type = $_POST['type'];
        $price = $_POST['price'];

        // Xử lý ảnh nếu có tải ảnh mới lên
        if (!empty($_FILES["image"]["name"])) {
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["image"]["tmp_name"]);

            if ($check !== false) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    // Cập nhật cả ảnh
                    $stmt = $conn->prepare("UPDATE products SET device_name = ?, type = ?, price = ?, image = ? WHERE id_device = ?");
                    $stmt->bind_param("ssdsi", $name, $type, $price, $target_file, $product_id);
                } else {
                    $message = "Có lỗi khi tải ảnh lên.";
                }
            } else {
                $message = "File không phải là ảnh.";
            }
        } else {
            // Chỉ cập nhật thông tin, không thay đổi ảnh
            $stmt = $conn->prepare("UPDATE products SET device_name = ?, type = ?, price = ? WHERE id_device = ?");
            $stmt->bind_param("ssdi", $name, $type, $price, $product_id);
        }

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            header("Location: admin.php?page=home");
            $message = "Cập nhật sản phẩm thành công!";
        } else {
            $message = "Có lỗi xảy ra khi cập nhật sản phẩm.";
        }

        $stmt->close();
    }
} else {
    $message = "Không tìm thấy sản phẩm.";
}
?>

<link rel="stylesheet" href="../style/products.css">

<div class="edit-product-container">
    <h2>Sửa sản phẩm</h2>

    <?php if (!empty($message)): ?>
        <p class="<?php echo strpos($message, 'thành công') !== false ? 'edit-product-message' : 'edit-product-error'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

    <!-- Form sửa sản phẩm -->
    <form class="edit-product-form" action="admin.php?page=edit_product&id_device=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data">
        <div class="form-left">
            <div class="form-group">
                <label for="device_name">Tên thiết bị:</label>
                <input type="text" id="device_name" name="device_name" value="<?php echo $product['device_name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="type">Loại thiết bị:</label>
                <input type="text" id="type" name="type" value="<?php echo $product['type']; ?>" required>
            </div>

            <div class="form-group">
                <label for="price">Giá sản phẩm:</label>
                <input type="number" id="price" name="price" value="<?php echo $product['price']; ?>" required>
            </div>
            <button class="add-product-submit" type="submit">Cập nhật thiết bị</button>
        </div>

        <div class="form-right">
            <div class="form-group">
                <label for="image">Hình ảnh thiết bị:</label>
                <button type="button" id="select-image-button" onclick="document.getElementById('image').click();">Chọn hình ảnh mới</button>
                <input type="file" id="image" name="image" accept="image/*" onchange="previewImage();" style="display:none;">
            </div>
            <img id="preview" class="edit-product-preview-img" src="<?php echo $product['image']; ?>" alt="Hình ảnh sản phẩm hiện tại" />
        </div>
    </form>
</div>

<script>
    function previewImage() {
        var file = document.getElementById("image").files[0];
        var reader = new FileReader();

        reader.onloadend = function() {
            document.getElementById("preview").src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            document.getElementById("preview").src = "<?php echo $product['image']; ?>";
        }
    }
</script>
