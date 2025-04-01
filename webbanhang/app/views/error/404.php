<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            max-width: 600px;
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .error-code {
            font-size: 100px;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 0;
            line-height: 1;
        }
        .error-text {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .back-btn {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            padding: 10px 25px;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">404</h1>
        <p class="error-text">Không tìm thấy trang bạn yêu cầu</p>
        <p>Trang bạn đang tìm kiếm có thể đã bị xóa, đổi tên hoặc tạm thời không truy cập được.</p>
        <a href="<?php echo BASE_URL; ?>" class="btn btn-primary back-btn mt-3">
            <i class="fas fa-home mr-2"></i>Quay lại trang chủ
        </a>
    </div>
</body>
</html>