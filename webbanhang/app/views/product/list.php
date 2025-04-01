<?php include 'app/views/shares/header.php'; ?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Danh sách sản phẩm</h1>
        <?php
        require_once('app/utils/JWTHandler.php');
        $jwtHandler = new JWTHandler();
        $role = null;

        if (isset($_COOKIE['jwtToken'])) {
            $jwt = $_COOKIE['jwtToken'];
            $decoded = $jwtHandler->decode($jwt);
            if ($decoded) {
                $role = $decoded['role'];
            }
        }

        if ($role === 'admin'): ?>
            <a href="/webbanhang/Product/add" class="btn btn-success">
                <i class="fa fa-plus"></i> Thêm sản phẩm mới
            </a>
        <?php endif; ?>
    </div>

    <!-- Hiển thị danh mục -->
    

    <ul class="list-group" id="product-list">
        <!-- Danh sách sản phẩm sẽ được tải từ API -->
         
    </ul>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem('jwtToken');
    if (!token) {
        alert('Vui lòng đăng nhập');
        location.href = '/webbanhang/account/login';
        return;
    }

    fetch('/webbanhang/api/product', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const productList = document.getElementById('product-list');
            data.data.forEach(product => {
                const productItem = document.createElement('li');
                productItem.className = 'list-group-item';
                productItem.innerHTML = `
                    <div class="row">
                        <div class="col-md-3">
                            <img src="${product.image}" alt="${product.name}" class="img-fluid">
                        </div>
                        <div class="col-md-9">
                            <h2>${product.name}</h2>
                            <p>${product.description}</p>
                            <p>Giá: ${product.price.toLocaleString()} VND</p>
                            <p>Danh mục: ${product.category_name}</p>
                            <a href="/webbanhang/Product/show/${product.id}" class="btn btn-info">Xem chi tiết</a>
                            
                            
                            <?php if ($role === 'admin'): ?>
                                <a href="/webbanhang/Product/update/${product.id}" class="btn btn-warning">Sửa</a>
                                <button class="btn btn-danger" onclick="deleteProduct(${product.id})">Xóa</button>
                            <?php endif; ?>
                        </div>
                    </div>
                `;
                productList.appendChild(productItem);
            });
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi tải danh sách sản phẩm.');
    });
});

// Thêm sản phẩm vào giỏ hàng
function addToCart(productId) {
    const token = localStorage.getItem('jwtToken');
    if (!token) {
        alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.');
        location.href = '/webbanhang/account/login';
        return;
    }

    fetch('/webbanhang/api/cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Sản phẩm đã được thêm vào giỏ hàng.');
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng.');
    });
}

// Xóa sản phẩm
function deleteProduct(productId) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        const token = localStorage.getItem('jwtToken');
        fetch(`/webbanhang/api/product/${productId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Sản phẩm đã được xóa.');
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi xóa sản phẩm.');
        });
    }
}
</script>