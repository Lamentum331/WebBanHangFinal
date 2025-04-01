<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách danh mục</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #2c3e50 0%, #1a2530 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
            font-weight: 600;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .table td {
            vertical-align: middle;
        }
        .btn-add {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
            background: linear-gradient(135deg, #27ae60 0%, #219a52 100%);
        }
        .btn-warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            border: none;
            transition: all 0.3s;
        }
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border: none;
            transition: all 0.3s;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #7f8c8d 0%, #6c7a7d 100%);
            border: none;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .page-title {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 25px;
            border-left: 5px solid #3498db;
            padding-left: 15px;
        }
        .alert-info {
            background-color: #e8f4f8;
            border-color: #d1ecf1;
            color: #0c5460;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            align-items: center;
        }
        .alert-info i {
            font-size: 24px;
            margin-right: 15px;
        }
        .category-badge {
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            background-color: #3498db;
            color: white;
            margin-right: 10px;
        }
        .action-buttons {
            display: flex;
        }
        .action-buttons a {
            margin-right: 5px;
        }
        .category-count {
            background-color: #e8f4f8;
            color: #0c5460;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            margin-left: 15px;
        }
        .category-count i {
            margin-right: 8px;
        }
        .table-responsive {
            border-radius: 0 0 10px 10px;
            overflow: hidden;
        }
        tr {
            transition: background-color 0.3s;
        }
        tr:hover {
            background-color: #f1f8fb !important;
        }
        .description-text {
            color: #6c757d;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Danh sách danh mục</h1>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a href="/webbanhang/Category/add" class="btn btn-add mb-4">
                <i class="fas fa-plus-circle mr-2"></i>Thêm danh mục mới
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($categories)): ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-tags mr-2"></i>Danh mục sản phẩm</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Tên danh mục</th>
                            <th width="50%">Mô tả</th>
                            <th width="20%" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category->id, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="category-badge">ID: <?php echo htmlspecialchars($category->id, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <strong><?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="description-text" title="<?php echo htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                        <div class="action-buttons">
                                            <a href="/webbanhang/Category/edit/<?php echo $category->id; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <a href="/webbanhang/Category/delete/<?php echo $category->id; ?>" class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                                <i class="fas fa-trash"></i> Xóa
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <p class="mb-0">Chưa có danh mục nào. Hãy thêm danh mục mới!</p>
        </div>
    <?php endif; ?>
        
        <a href="/webbanhang/Product" class="btn btn-secondary mt-3">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách sản phẩm
        </a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
