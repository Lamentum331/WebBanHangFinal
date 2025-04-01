<?php include 'app/views/shares/header.php'; ?>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light p-3 rounded shadow-sm">
            <li class="breadcrumb-item"><a href="/webbanhang/Product" class="text-decoration-none"><i class="fas fa-home"></i> Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/webbanhang/Product" class="text-decoration-none">Sản phẩm</a></li>
            <?php if($product && !empty($product->category_name)): ?>
                <li class="breadcrumb-item"><a href="/webbanhang/Product/category/<?php echo $product->category_id; ?>" class="text-decoration-none"><?php echo htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8'); ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $product ? htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') : 'Chi tiết sản phẩm'; ?></li>
        </ol>
    </nav>

    <?php if ($product): ?>
        <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="card-header bg-gradient-primary text-white py-3">
                <h2 class="mb-0 text-center font-weight-bold"><i class="fas fa-box-open mr-2"></i>Chi tiết sản phẩm</h2>
            </div>
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <div class="col-lg-6 p-4 bg-light d-flex align-items-center justify-content-center">
                        <div class="product-image-container">
                            <?php if ($product->image): ?>
                                <img src="/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" 
                                     class="img-fluid rounded product-image" 
                                     alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else: ?>
                                <div class="no-image-container bg-light d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                                    <div class="text-center text-muted">
                                        <i class="fas fa-image fa-5x mb-3"></i>
                                        <p>Không có ảnh sản phẩm</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-6 p-4">
                        <h3 class="product-title mb-3 text-primary font-weight-bold">
                            <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                        </h3>
                        
                        <div class="category-badge mb-4">
                            <span class="badge badge-pill badge-info px-3 py-2">
                                <i class="fas fa-tag mr-1"></i>
                                <?php echo !empty($product->category_name) ? htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8') : 'Chưa có danh mục'; ?>
                            </span>
                        </div>
                        
                        <div class="product-price mb-4">
                            <p class="text-danger font-weight-bold h3">
                                <i class="fas fa-tags mr-2"></i><?php echo number_format($product->price, 0, ',', '.'); ?> VND
                            </p>
                        </div>
                        
                        <div class="product-description mb-4">
                            <h5 class="text-secondary mb-2"><i class="fas fa-info-circle mr-2"></i>Mô tả sản phẩm</h5>
                            <div class="p-3 bg-light rounded">
                                <p class="card-text">
                                    <?php echo nl2br(htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8')); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="product-actions mt-4">
                            <div class="d-flex align-items-center mb-3">                                
                                <a href="/webbanhang/Product/addToCart/<?php echo $product->id; ?>" class="btn btn-success btn-lg add-to-cart-btn">
                                    <i class="fas fa-cart-plus mr-2"></i>Thêm vào giỏ hàng
                                </a>
                            </div>
                            
                            <div class="d-flex mt-3">
                                <a href="/webbanhang/Product" class="btn btn-secondary mr-2">
                                    <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
                                </a>
                                <button class="btn btn-outline-danger" id="wishlistBtn">
                                    <i class="far fa-heart mr-2"></i>Thêm vào yêu thích
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional product information -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <ul class="nav nav-tabs card-header-tabs" id="productTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="specs-tab" data-toggle="tab" href="#specs" role="tab">Thông số kỹ thuật</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab">Đánh giá</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="productTabContent">
                            <div class="tab-pane fade show active" id="specs" role="tabpanel">
                                <p class="text-muted">Thông tin chi tiết về sản phẩm sẽ được hiển thị tại đây.</p>
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <div class="alert alert-danger p-5 text-center shadow-sm">
            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
            <h4 class="alert-heading">Không tìm thấy sản phẩm!</h4>
            <p>Sản phẩm bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.</p>
            <hr>
            <a href="/webbanhang/Product" class="btn btn-outline-danger mt-3">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách sản phẩm
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wishlist button toggle
    const wishlistBtn = document.getElementById('wishlistBtn');
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.replace('far', 'fas');
                icon.parentElement.classList.add('active');
                this.classList.replace('btn-outline-danger', 'btn-danger');
            } else {
                icon.classList.replace('fas', 'far');
                icon.parentElement.classList.remove('active');
                this.classList.replace('btn-danger', 'btn-outline-danger');
            }
        });
    }
});
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #3a7bd5 0%, #2c3e50 100%);
}
.product-image {
    max-height: 400px;
    object-fit: contain;
    transition: transform 0.3s ease;
}
.product-image:hover {
    transform: scale(1.05);
}
.product-title {
    font-size: 1.8rem;
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 10px;
}
.category-badge .badge {
    font-size: 0.9rem;
}
.add-to-cart-btn {
    transition: all 0.3s ease;
}
.add-to-cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
#wishlistBtn.active {
    color: white;
}
</style>

<?php include 'app/views/shares/footer.php'; ?>
