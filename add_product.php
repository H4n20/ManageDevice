<!-- admin/pages/add_product.php -->
<?php
// session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối
 // Kết nối cơ sở dữ liệu

$message = ""; // Biến để chứa thông báo

// Xử lý khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['device_name'];
    $type = $_POST['type'];
    $price = $_POST['price'];

    // Xử lý ảnh
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["image"]["tmp_name"]);

    if ($check !== false) {
        // Kiểm tra định dạng ảnh
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Chuẩn bị câu truy vấn
            $stmt = $conn->prepare("INSERT INTO products (device_name, type, price, image, status) VALUES (?, ?, ?, ?, 'available')");

            // Kiểm tra xem prepare() có thành công không
            if ($stmt === false) {
                $message = "Có lỗi khi chuẩn bị truy vấn: " . $conn->error;
            } else {
                // Gắn các tham số cho truy vấn
                $stmt->bind_param("ssds", $name, $type, $price, $target_file);

                // Thực thi câu lệnh
                if ($stmt->execute()) {
                    $message = "Thêm sản phẩm thành công!";
                } else {
                    $message = "Có lỗi xảy ra khi thêm sản phẩm.";
                }

                // Đóng statement
                $stmt->close();
            }
        } else {
            $message = "Có lỗi khi tải ảnh lên.";
        }
    } else {
        $message = "File không phải là ảnh.";
    }
}
?>
    
<link rel="stylesheet" href="../style/products.css">

<div class="add-product-container">
    <h2>Thêm sản phẩm</h2>

    <!-- Hiển thị thông báo nếu có -->
    <?php if (!empty($message)): ?>
        <p class="<?php echo strpos($message, 'Thêm sản phẩm thành công') !== false ? 'add-product-message' : 'add-product-error'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

    <!-- Form thêm sản phẩm -->
    <form class="add-product-form" action="admin.php?page=add_product" method="post" enctype="multipart/form-data">
        <div class="form-left">
            <div class="form-group">
                <label for="device_name">Tên thiết bị:</label>
                <input type="text" id="device_name" name="device_name" required>
            </div>

            <div class="form-group">
                <label for="type">Loại thiết bị:</label>
                <input type="text" id="type" name="type" required>
            </div>

            <div class="form-group">
                <label for="price">Giá sản phẩm:</label>
                <input type="number" id="price" name="price" required>
            </div>
            <button class="add-product-submit" type="submit">Thêm thiết bị</button>
        </div>

        <div class="form-right">
            <div class="form-group">
                <label for="image">Hình ảnh thiết bị:</label>
                <!-- Nút chọn file -->
                <button type="button" id="select-image-button">Chọn hình ảnh</button>
                <input type="file" id="image" name="image" accept="image/*" onchange="previewImage();" required style="display:none;">
            </div>
            <img id="preview" class="add-product-preview-img" alt="Hình ảnh sản phẩm" />
        </div>

    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Khi nhấn nút "Chọn hình ảnh", trigger input file
        $('#select-image-button').on('click', function() {
            $('#image').click();
        });

        // Xem trước ảnh
        function previewImage() {
            var file = $('#image')[0].files[0];
            var reader = new FileReader();

            reader.onloadend = function() {
                $('#preview').attr('src', reader.result);
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                $('#preview').attr('src', '');
            }
        }

        // Gọi hàm xem trước khi có sự thay đổi ở input ảnh
        $('#image').on('change', previewImage);
    });
</script>
