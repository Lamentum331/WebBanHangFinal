<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0"><i class="fas fa-edit mr-2"></i>Sửa danh mục</h5>
        </div>
        <div class="card-body">
            <?php if (isset($category) && $category): ?>
                <form action="/Category/update" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($category->id, ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <div class="form-group">
                        <label for="name">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required 
                               value="<?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if(isset($errors['name'])): ?>
                            <small class="text-danger"><?php echo $errors['name']; ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <?php if(isset($errors['description'])): ?>
                            <small class="text-danger"><?php echo $errors['description']; ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-2"></i>Cập nhật danh mục
                        </button>
                        <a href="/Category" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left mr-2"></i>Quay lại
                        </a>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Không tìm thấy danh mục!
                </div>
                <a href="/Category" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
