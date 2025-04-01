<?php include 'app/views/shares/header.php'; ?>

<div class="container py-4">
    <h2 class="mb-4">Chỉnh sửa sản phẩm</h2>
    <form id="edit-product-form" method="POST" action="/webbanhang/Product/update/<?php echo $product->id; ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $product->id; ?>">

        <div class="mb-3">
            <label for="name" class="form-label">Tên sản phẩm</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea id="description" name="description" class="form-control" rows="5" required><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Giá</label>
            <input type="number" id="price" name="price" class="form-control" value="<?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Danh mục</label>
            <select id="category_id" name="category_id" class="form-select" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $product->category_id ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Hình ảnh</label>
            <input type="file" id="image" name="image" class="form-control">
            <?php if ($product->image): ?>
                <img src="/<?php echo $product->image; ?>" alt="Product Image" class="img-thumbnail mt-2" style="max-width: 200px;">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="/webbanhang/Product/list" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php include 'app/views/shares/footer.php'; ?>