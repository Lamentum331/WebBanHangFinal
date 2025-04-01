<?php
require_once('app/utils/JWTHandler.php'); // Đảm bảo bạn đã include JWTHandler
$jwtHandler = new JWTHandler();
$username = null;

// Kiểm tra JWT token trong cookie
if (isset($_COOKIE['jwtToken'])) {
    $jwt = $_COOKIE['jwtToken'];
    $decoded = $jwtHandler->decode($jwt);
    if ($decoded) {
        $username = $decoded['username']; // Lấy tên người dùng từ token
    }
}
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Khởi tạo session nếu chưa được khởi tạo
}
?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Navbar styling */
        .navbar {
            background: linear-gradient(135deg, #2c3e50 0%, #1a2530 100%) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 0.7rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .navbar.sticky-top {
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 600;
            font-size: 1.5rem;
            letter-spacing: 0.5px;
            position: relative;
            padding: 0.5rem 0;
        }

        .navbar-brand::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 30px;
            height: 3px;
            background-color: #3498db;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .navbar-brand:hover::after {
            width: 100%;
        }

        .navbar-nav .nav-item {
            margin-right: 0.6rem;
            position: relative;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            padding: 0.7rem 1.2rem;
            transition: all 0.3s ease;
            border-radius: 5px;
            font-weight: 500;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link.active {
            background-color: rgba(52, 152, 219, 0.2);
            color: white !important;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.3);
            padding: 0.4rem 0.7rem;
        }

        .navbar-toggler:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
        }

        /* Search form styling */
        .search-form {
            position: relative;
            margin-left: auto;
            transition: all 0.3s ease;
        }

        .search-input {
            border-radius: 50px;
            padding-left: 18px;
            padding-right: 50px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: white;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            width: 220px;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .search-input:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            color: white;
            width: 280px;
        }

        .search-button {
            position: absolute;
            right: 4px;
            top: 4px;
            border-radius: 50%;
            width: 34px;
            height: 34px;
            background-color: rgba(52, 152, 219, 0.7);
            border: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .search-button:hover {
            background-color: #3498db;
            color: white;
            transform: scale(1.05);
        }

        /* User actions styling */
        .user-actions {
            display: flex;
            align-items: center;
        }

        .user-actions .btn.user-btn {
            width: auto;
            /* Allow the button to expand based on content */
            padding: 0.5rem 1rem;
            /* Add padding for better spacing */
            border-radius: 20px;
            /* Rounded corners */
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            /* Space between icon and text */
        }

        .user-actions .btn.user-btn .username {
            font-size: 0.9rem;
            /* Adjust font size */
            font-weight: 500;
            /* Medium font weight */
        }

        .user-actions .btn {
            color: white;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-left: 0.6rem;
            width: 60px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
            padding: 0;
        }

        .user-actions .btn:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-btn {
            position: relative;
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.65rem;
            padding: 0.2rem 0.45rem;
            border-radius: 50%;
            background-color: #e74c3c;
            border: 2px solid #2c3e50;
            font-weight: bold;
            transition: all 0.3s ease;
            z-index: 1;
        }

        @media (max-width: 991.98px) {
            .search-form {
                margin: 1rem 0;
                width: 100%;
            }

            .search-input,
            .search-input:focus {
                width: 100%;
            }

            .navbar-nav .nav-link {
                padding-left: 0.8rem;
            }

            .user-actions {
                margin-top: 0.5rem;
                justify-content: flex-start;
            }

            .user-actions .btn:first-child {
                margin-left: 1;
            }
        }

        .dropdown-menu {
            background-color: #fff;
            border: none;
            border-radius: 8px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
            margin-top: 10px;
            min-width: 230px;
        }

        .dropdown-item {
            color: #2c3e50;
            padding: 0.6rem 1.2rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }

        .dropdown-item i {
            width: 40px;
            text-align: center;
        }

        .dropdown-item-text {
            color: #2c3e50;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
        }

        .dropdown-divider {
            margin: 0.3rem 0;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }


        .dropdown-menu:before {
            content: '';
            position: absolute;
            top: -6px;
            right: 17px;
            width: 12px;
            height: 12px;
            background-color: #fff;
            transform: rotate(45deg);
            border-left: 1px solid rgba(0, 0, 0, 0.05);
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/webbanhang">
                <i class="fas fa-box-open mr-2"></i>Quản lý sản phẩm
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/webbanhang/Product">Danh sách sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/webbanhang/Category">Danh mục</a>
                    </li>
                </ul>
                <div class="user-actions">
                    <?php
                    if (isset($_COOKIE['jwtToken'])) {
                        require_once('app/utils/JWTHandler.php'); // Đảm bảo bạn đã include JWTHandler
                        $jwtHandler = new JWTHandler();
                        $jwt = $_COOKIE['jwtToken'];
                        $decoded = $jwtHandler->decode($jwt);
                        if ($decoded) {
                            $username = $decoded['username'];
                            echo '<span class="username text-white mr-3">Xin chào, ' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . '</span>';
                            echo '<a href="/webbanhang/account/logout" class="btn btn-outline-light btn-sm"><i class="fas fa-sign-out-alt mr-1"></i>Đăng xuất</a>';
                        } else {
                            echo '<a href="/webbanhang/account/login" class="btn btn-outline-light btn-sm mr-2"><i class="fas fa-sign-in-alt mr-1"></i>Đăng nhập</a>';
                            echo '<a href="/webbanhang/account/register" class="btn btn-outline-light btn-sm"><i class="fas fa-user-plus mr-1"></i>Đăng ký</a>';
                        }
                    } else {
                        echo '<a href="/webbanhang/account/login" class="btn btn-outline-light btn-sm mr-2"><i class="fas fa-sign-in-alt mr-1"></i>Đăng nhập</a>';
                        echo '<a href="/webbanhang/account/register" class="btn btn-outline-light btn-sm"><i class="fas fa-user-plus mr-1"></i>Đăng ký</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
       
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>