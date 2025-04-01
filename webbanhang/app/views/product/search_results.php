<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-4">
    <h1 class="mb-4">Kết quả tìm kiếm: "<?php echo $searchQuery; ?>"</h1>
    
    <?php if (count($products) > 0): ?>
        <p class="text-muted">Tìm thấy <?php echo count($products); ?> sản phẩm</p>
        
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if ($product->image): ?>
                            <img src="/<?php echo $product->image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <span class="text-muted">Không có ảnh</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="/webbanhang/Product/show/<?php echo $product->id; ?>">
                                    <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </h5>
                            <p class="card-text text-truncate"><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="card-text font-weight-bold"><?php echo number_format($product->price, 0, ',', '.'); ?> VND</p>
                            <p class="card-text">
                                <span class="badge badge-info"><?php echo htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8'); ?></span>
                            </p>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="btn-group w-100">
                                <a href="/webbanhang/Product/addToCart/<?php echo $product->id; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                </a>
                                <a href="/webbanhang/Product/show/<?php echo $product->id; ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-eye"></i> Chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>Không tìm thấy sản phẩm nào phù hợp với từ khóa "<?php echo $searchQuery; ?>".
        </div>
    <?php endif; ?>
    
    <a href="/webbanhang/Product" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left mr-1"></i>Quay lại danh sách sản phẩm
    </a>
</div>

<?php include 'app/views/shares/footer.php'; ?>
