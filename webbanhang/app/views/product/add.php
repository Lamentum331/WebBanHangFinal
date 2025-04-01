<?php include 'app/views/shares/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="display-5 fw-bold mb-0">
                                <i class="fas fa-box-open me-2"></i>Thêm sản phẩm mới
                            </h1>
                            <p class="mb-0 lead">Nhập thông tin chi tiết để tạo sản phẩm mới</p>
                        </div>
                        <a href="/webbanhang/Product/list" class="btn btn-light btn-lg shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h3 class="mb-0 text-center text-primary">
                        <i class="fas fa-box-open me-2"></i>Thêm sản phẩm mới
                    </h3>
                </div>
                
                <div class="card-body p-4">
                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger border-start border-5 border-danger bg-danger bg-opacity-10" role="alert">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-exclamation-circle fs-3 text-danger"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1">Có lỗi xảy ra!</h5>
                                <ul class="mb-0 ps-3">
                                    <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form id="add-product-form" method="POST" action="/webbanhang/Product/save" enctype="multipart/form-data" 
                          class="needs-validation" novalidate>
                        
                        <div class="row g-4">
                            <!-- Product name field -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold mb-2">
                                        <i class="fas fa-tag me-1 text-primary"></i>
                                        Tên sản phẩm <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" class="form-control form-control-lg" 
                                           placeholder="Ví dụ: Điện thoại iPhone 15 Pro" required>
                                    <div class="invalid-feedback">Không được để trống trường này</div>
                                </div>
                            </div>

                            <!-- Price field -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold mb-2">
                                        <i class="fas fa-money-bill-wave me-1 text-primary"></i>
                                        Giá <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">₫</span>
                                        <input type="number" id="price" name="price" class="form-control form-control-lg" 
                                               placeholder="0.00" step="0.01" min="0" required>
                                    </div>
                                    <div class="form-text">Nhập số tiền (VND)</div>
                                </div>
                            </div>

                            <!-- Description field -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label fw-bold mb-2">
                                        <i class="fas fa-align-left me-1 text-primary"></i>
                                        Mô tả <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="description" name="description" class="form-control" 
                                              rows="5" placeholder="Nhập mô tả chi tiết sản phẩm..." required></textarea>
                                    <div class="invalid-feedback">Mô tả không được để trống</div>
                                </div>
                            </div>

                            <!-- Category field -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold mb-2">
                                        <i class="fas fa-folder me-1 text-primary"></i>
                                        Danh mục <span class="text-danger">*</span>
                                    </label>
                                    <select id="category_id" name="category_id" class="form-select form-select-lg" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        <!-- Các danh mục sẽ được tải từ API -->
                                    </select>
                                    <div class="invalid-feedback">Vui lòng chọn danh mục</div>
                                </div>
                            </div>

                            <!-- Image upload field -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold mb-2">
                                        <i class="fas fa-image me-1 text-primary"></i>
                                        Hình ảnh
                                    </label>
                                    <div class="input-group">
                                        <input type="file" name="image" class="form-control form-control-lg" 
                                               accept="image/*" onchange="previewImage(event)">
                                    </div>
                                    <div class="form-text">Tối đa 5MB (PNG, JPG, JPEG)</div>
                                </div>
                                <div class="mt-3 text-center" id="imagePreview">
                                    <div class="border border-2 border-dashed p-3 bg-light text-center">
                                        <i class="fas fa-cloud-upload-alt text-secondary fs-2"></i>
                                        <p class="text-muted mb-0">Hình ảnh sẽ hiển thị ở đây</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="/webbanhang/Product/list" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Lưu sản phẩm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Tải danh mục từ API
    fetch('/webbanhang/api/category')
        .then(response => response.json())
        .then(data => {
            const categorySelect = document.getElementById('category_id');
            data.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        });

    // Xử lý gửi form
    document.getElementById('add-product-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        const jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        fetch('/webbanhang/api/product', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'Product created successfully') {
                location.href = '/webbanhang/Product';
            } else {
                alert('Thêm sản phẩm thất bại');
            }
        });
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>