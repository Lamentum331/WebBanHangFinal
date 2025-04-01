<?php
// Bắt đầu session
if (session_status() === PHP_SESSION_NONE) {

    session_start();
    require_once 'app/models/ProductModel.php';

require_once 'app/controllers/ProductApiController.php';
require_once 'app/controllers/CategoryApiController.php';
}

// Định nghĩa hằng số đường dẫn gốc
define('ROOT_DIR', __DIR__);
define('BASE_URL', '/webbanhang'); // Thêm base URL cho ứng dụng

// Cấu hình báo lỗi (chỉ sử dụng khi phát triển)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Tự động load các class cần thiết
spl_autoload_register(function ($className) {
    // Xác định các vị trí có thể chứa class
    $locations = [
        'app/models/',
        'app/controllers/',
        'app/config/',
        'app/lib/'
    ];

    // Kiểm tra từng vị trí
    foreach ($locations as $location) {
        $file = ROOT_DIR . '/' . $location . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }

    return false;
});

// Xử lý URL
$requestUri = $_SERVER['REQUEST_URI'];
$baseUrlPos = strpos($requestUri, BASE_URL);

// Lấy phần URL sau base URL
if ($baseUrlPos !== false) {
    $url = substr($requestUri, $baseUrlPos + strlen(BASE_URL));
} else {
    $url = $requestUri;
}

// Loại bỏ query string nếu có
if (($pos = strpos($url, '?')) !== false) {
    $url = substr($url, 0, $pos);
}

$url = trim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Định tuyến các yêu cầu API
if (isset($url[0]) && $url[0] === 'api') {
    $apiControllerName = isset($url[1]) ? ucfirst($url[1]) . 'ApiController' : null;

    if ($apiControllerName && file_exists('app/controllers/' . $apiControllerName . '.php')) {
        require_once 'app/controllers/' . $apiControllerName . '.php';
        $controller = new $apiControllerName();
        $method = $_SERVER['REQUEST_METHOD'];
        $id = $url[2] ?? null;

        switch ($method) {
            case 'GET':
                $action = $id ? 'show' : 'index';
                break;
            case 'POST':
                $action = 'store';
                break;
            case 'PUT':
                $action = $id ? 'update' : null;
                break;
            case 'DELETE':
                $action = $id ? 'destroy' : null;
                break;
            default:
                http_response_code(405);
                echo json_encode(['message' => 'Method Not Allowed']);
                exit;
        }

        if ($action && method_exists($controller, $action)) {
            if ($id) {
                call_user_func_array([$controller, $action], [$id]);
            } else {
                call_user_func_array([$controller, $action], []);
            }
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Action not found']);
        }
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Controller not found']);
        exit;
    }
}

// Cấu hình routes tùy chỉnh
$routes = [
    '' => ['controller' => 'ProductController', 'action' => 'index'],
    'login' => ['controller' => 'UserController', 'action' => 'login'],
    'register' => ['controller' => 'UserController', 'action' => 'register'],
    'logout' => ['controller' => 'UserController', 'action' => 'logout'],
    'profile' => ['controller' => 'UserController', 'action' => 'profile'],
    // Thêm các route tùy chỉnh khác ở đây
];

// Xác định controller và action
$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'ProductController'; // Mặc định là ProductController
$action = !empty($url[1]) ? $url[1] : 'index'; // Mặc định là action index

// Kiểm tra các route tùy chỉnh
$routeKey = !empty($url[0]) ? $url[0] : '';
if (array_key_exists($routeKey, $routes)) {
    $controllerName = $routes[$routeKey]['controller'];
    $action = $routes[$routeKey]['action'];
    // Điều chỉnh tham số nếu cần
    $url = array_merge([$url[0]], array_slice($url, 1));
}

// Đường dẫn đến file controller
$controllerFile = ROOT_DIR . '/app/controllers/' . $controllerName . '.php';

// Kiểm tra sự tồn tại của controller
if (!file_exists($controllerFile)) {
    // Log lỗi
    error_log("Controller not found: $controllerFile");

    // Chuyển hướng đến trang lỗi 404
    header("HTTP/1.0 404 Not Found");
    include ROOT_DIR . '/app/views/error/404.php'; // Sửa đường dẫn file 404
    exit;
}

// Load controller
require_once $controllerFile;

// Kiểm tra xem class controller có tồn tại không
if (!class_exists($controllerName)) {
    error_log("Controller class '$controllerName' not found in file $controllerFile");
    header("HTTP/1.0 500 Internal Server Error");
    include ROOT_DIR . '/app/views/error/500.php'; // Sửa đường dẫn file 500
    exit;
}

// Khởi tạo controller
$controller = new $controllerName();

// Kiểm tra xem method có tồn tại không
if (!method_exists($controller, $action)) {
    // Log lỗi
    error_log("Action not found: $action in $controllerName");

    // Kiểm tra nếu có phương thức notFound trong controller
    if (method_exists($controller, 'notFound')) {
        $controller->notFound();
        exit;
    }

    // Nếu không có phương thức notFound, chuyển hướng đến action mặc định
    $action = 'index';

    // Kiểm tra lại method mặc định
    if (!method_exists($controller, $action)) {
        header("HTTP/1.0 404 Not Found");
        include ROOT_DIR . '/app/views/error/404.php'; // Sửa đường dẫn file 404
        exit;
    }
}

// Gọi middleware trước khi chạy action (nếu có)
if (method_exists($controller, 'beforeAction')) {
    $result = $controller->beforeAction($action);
    if ($result === false) {
        exit; // Dừng thực thi nếu middleware trả về false
    }
}

// Gọi action với các tham số còn lại (nếu có)
$params = array_slice($url, 2);
call_user_func_array([$controller, $action], $params);

// Gọi afterAction hook (nếu có)
if (method_exists($controller, 'afterAction')) {
    $controller->afterAction($action);
}