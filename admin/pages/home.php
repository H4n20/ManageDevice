
<?php
// session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/includes/db_connection.php'; // Đường dẫn tuyệt đối

// Gọi phần xử lý phân trang và truy vấn sản phẩm
// include '../function/pagination.php';
?>

<div class="main-content">
<div class="product-head-content">
    <h2>Danh sách sản phẩm</h2>
    <button class="product-button product-button-add" id="add-product-btn">Thêm sản phẩm</button>
</div>

<!-- Gọi form tìm kiếm -->
<?php require $_SERVER['DOCUMENT_ROOT'] . '/dongoclinh/project/function/search.php'; // Đường dẫn tuyệt đối?>

<div class="product-grid-container">
    <?php while ($product = $result->fetch_assoc()): ?>
        <div class="product-grid-item">
            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['device_name']; ?>">
            <h3><?php echo $product['device_name']; ?></h3>
            <p>Loại: <?php echo $product['type']; ?></p>
            <p>Giá: <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</p>
            <p>Tình trạng: <?php echo $product['status'] == 'rented' ? 'Đang được cho mượn' : 'Rảnh rỗi'; ?></p>
            <button class="product-button product-button-edit edit-product-btn" data-id="<?php echo $product['id_device']; ?>">Sửa</button>
            <button class="product-button product-button-delete delete-product-btn" data-id="<?php echo $product['id_device']; ?>">Xóa</button>
        </div>
    <?php endwhile; ?>
</div>
</div>
<!-- Gọi phân trang -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
    // Chuyển hướng đến trang thêm sản phẩm khi nhấn nút Thêm sản phẩm
    $('#add-product-btn').on('click', function(e) {
        e.preventDefault(); // Ngăn chặn hành vi mặc định của nút
        $('#content').load('pages/add_product.php'); // Tải nội dung trang thêm sản phẩm vào #content
    });

    // Chuyển hướng đến trang sửa sản phẩm khi nhấn nút Sửa
    $('.edit-product-btn').on('click', function(e) {
        e.preventDefault(); // Ngăn chặn hành vi mặc định của nút
        var productId = $(this).data('id');
        $('#content').load(`pages/edit_product.php?id_device=${productId}`); // Tải nội dung trang sửa sản phẩm vào #content
    });

    // Gửi yêu cầu xóa sản phẩm khi nhấn nút Xóa
    $('.delete-product-btn').on('click', function(e) {
    e.preventDefault(); // Ngăn chặn hành vi mặc định của nút
    var productId = $(this).data('id');
    if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) {
        $.post('pages/delete_product.php', { id_device: productId }, function(response) {
            alert(response.message); // Hiển thị thông báo
            $('#content').load('pages/product_list.php'); // Tải lại danh sách sản phẩm vào #content
        }, 'json');
    }
});

});

</script>

</html>
