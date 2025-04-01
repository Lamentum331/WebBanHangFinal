<?php include 'app/views/shares/header.php'; ?>

<div class="container my-4">
    <h1 class="mb-4">Giỏ hàng của bạn</h1>
    
    <?php if (!empty($cart)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalAmount = 0;
                    foreach ($cart as $id => $item): 
                        $itemTotal = $item['price'] * $item['quantity'];
                        $totalAmount += $itemTotal;
                    ?>
                    <tr id="product-row-<?php echo $id; ?>">
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($item['image']): ?>
                                    <img src="/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" class="img-thumbnail mr-3" style="max-width: 80px;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center mr-3" style="width: 80px; height: 80px;">
                                        <span class="text-muted">Không có ảnh</span>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h5><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle product-price" data-price="<?php echo $item['price']; ?>">
                            <?php echo number_format($item['price'], 0, ',', '.'); ?> VND
                        </td>
                        <td class="align-middle">
                            <div class="input-group" style="width: 140px;">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-outline-secondary decrease-qty" data-id="<?php echo $id; ?>">-</button>
                                </div>
                                <input type="number" value="<?php echo $item['quantity']; ?>" min="1" class="form-control text-center quantity-input" data-id="<?php echo $id; ?>">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary increase-qty" data-id="<?php echo $id; ?>">+</button>
                                </div>
                            </div>
                            <div class="mt-2 text-center">
                                <span class="updating-indicator text-success d-none" id="updating-<?php echo $id; ?>">
                                    <i class="fa fa-refresh fa-spin"></i> Đang cập nhật...
                                </span>
                            </div>
                        </td>
                        <td class="align-middle product-total">
                            <strong><?php echo number_format($itemTotal, 0, ',', '.'); ?> VND</strong>
                        </td>
                        <td class="align-middle">
                            <a href="javascript:void(0);" class="btn btn-sm btn-danger remove-item" data-id="<?php echo $id; ?>">
                                <i class="fa fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right">
                            <strong>Tổng tiền:</strong>
                        </td>
                        <td>
                            <strong class="text-danger" id="cart-total"><?php echo number_format($totalAmount, 0, ',', '.'); ?> VND</strong>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="/webbanhang/Product" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Tiếp tục mua sắm
            </a>
            <a href="/webbanhang/Product/Checkout" class="btn btn-primary">
                <i class="fa fa-credit-card"></i> Thanh toán
            </a>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fa fa-shopping-cart fa-4x text-muted"></i>
            </div>
            <h3 class="mb-3">Giỏ hàng của bạn đang trống</h3>
            <p class="text-muted mb-4">Hãy thêm sản phẩm vào giỏ hàng để tiến hành mua hàng</p>
            <a href="/webbanhang/Product" class="btn btn-primary">
                <i class="fa fa-arrow-left"></i> Quay lại cửa hàng
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tính toán lại tổng tiền của giỏ hàng
    function updateCartTotal() {
        let total = 0;
        document.querySelectorAll('.product-total strong').forEach(function(el) {
            let amount = parseFloat(el.textContent.replace(/[^\d]/g, ''));
            if (!isNaN(amount)) {
                total += amount;
            }
        });
        document.getElementById('cart-total').textContent = formatCurrency(total) + ' VND';
    }

    // Định dạng số tiền
    function formatCurrency(amount) {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Cập nhật số lượng sản phẩm qua AJAX
    function updateQuantity(productId, quantity) {
        // Hiển thị indicator
        document.getElementById('updating-' + productId).classList.remove('d-none');
        
        // Gửi request AJAX
        fetch('/webbanhang/Product/updateQuantity', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'id=' + productId + '&quantity=' + quantity
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật thành tiền
                const row = document.getElementById('product-row-' + productId);
                const priceElement = row.querySelector('.product-price');
                const totalElement = row.querySelector('.product-total strong');
                
                const price = parseFloat(priceElement.getAttribute('data-price'));
                const newTotal = price * quantity;
                
                totalElement.textContent = formatCurrency(newTotal) + ' VND';
                
                // Cập nhật tổng tiền
                updateCartTotal();
                
                // Hiển thị feedback thành công
                const indicator = document.getElementById('updating-' + productId);
                indicator.innerHTML = '<i class="fa fa-check"></i> Đã cập nhật';
                indicator.classList.add('text-success');
                
                setTimeout(function() {
                    indicator.classList.add('d-none');
                }, 1500);
            } else {
                // Hiển thị lỗi
                const indicator = document.getElementById('updating-' + productId);
                indicator.innerHTML = '<i class="fa fa-times"></i> Lỗi: ' + data.message;
                indicator.classList.add('text-danger');
                
                setTimeout(function() {
                    indicator.classList.add('d-none');
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Hiển thị lỗi
            const indicator = document.getElementById('updating-' + productId);
            indicator.innerHTML = '<i class="fa fa-times"></i> Lỗi kết nối';
            indicator.classList.add('text-danger');
            
            setTimeout(function() {
                indicator.classList.add('d-none');
            }, 2000);
        });
    }

    // Xử lý nút tăng số lượng
    document.querySelectorAll('.increase-qty').forEach(function(button) {
        button.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            let inputEl = this.parentNode.parentNode.querySelector('.quantity-input');
            let currentVal = parseInt(inputEl.value);
            
            if (!isNaN(currentVal)) {
                let newVal = currentVal + 1;
                inputEl.value = newVal;
                updateQuantity(id, newVal);
            } else {
                inputEl.value = 1;
                updateQuantity(id, 1);
            }
        });
    });
    
    // Xử lý nút giảm số lượng
    document.querySelectorAll('.decrease-qty').forEach(function(button) {
        button.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            let inputEl = this.parentNode.parentNode.querySelector('.quantity-input');
            let currentVal = parseInt(inputEl.value);
            
            if (!isNaN(currentVal) && currentVal > 1) {
                let newVal = currentVal - 1;
                inputEl.value = newVal;
                updateQuantity(id, newVal);
            } else {
                inputEl.value = 1;
                updateQuantity(id, 1);
            }
        });
    });
    
    // Xử lý khi nhập số lượng trực tiếp
    document.querySelectorAll('.quantity-input').forEach(function(input) {
        input.addEventListener('change', function() {
            let id = this.getAttribute('data-id');
            let currentVal = parseInt(this.value);
            
            if (isNaN(currentVal) || currentVal < 1) {
                this.value = 1;
                updateQuantity(id, 1);
            } else {
                updateQuantity(id, currentVal);
            }
        });
    });
    
    // Xử lý nút xóa sản phẩm
    document.querySelectorAll('.remove-item').forEach(function(button) {
        button.addEventListener('click', function() {
            if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                let id = this.getAttribute('data-id');
                
                fetch('/webbanhang/Product/removeFromCart/' + id, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Xóa dòng sản phẩm
                        document.getElementById('product-row-' + id).remove();
                        
                        // Cập nhật tổng tiền
                        updateCartTotal();
                        
                        // Nếu không còn sản phẩm nào, tải lại trang
                        if (document.querySelectorAll('tbody tr').length === 0) {
                            window.location.reload();
                        }
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Đã xảy ra lỗi khi xóa sản phẩm');
                });
            }
        });
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
