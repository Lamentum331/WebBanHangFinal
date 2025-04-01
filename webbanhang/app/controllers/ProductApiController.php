<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/utils/JWTHandler.php'); // Thêm JWTHandler

class ProductApiController
{
    private $productModel;
    private $db;
    private $jwtHandler;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->jwtHandler = new JWTHandler(); // Khởi tạo JWTHandler
    }

    // Xác thực người dùng bằng JWT
    private function authenticate()
{
    $headers = apache_request_headers();

    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        $arr = explode(" ", $authHeader);
        $jwt = $arr[1] ?? null;

        if ($jwt) {
            try {
                $decoded = $this->jwtHandler->decode($jwt);
                if ($decoded) {
                    return $decoded; // Trả về dữ liệu đã giải mã nếu hợp lệ
                }
            } catch (Exception $e) {
                http_response_code(401);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Token không hợp lệ hoặc đã hết hạn'
                ]);
                exit;
            }
        }
    }

    return false; // Trả về false nếu không có token hoặc token không hợp lệ
}

    // Lấy danh sách sản phẩm
    public function index()
    {
        $user = $this->authenticate();
    if ($user) {
        header('Content-Type: application/json');
        $products = $this->productModel->getProducts();
        echo json_encode([
            'status' => 'success',
            'data' => $products
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'message' => 'Unauthorized'
        ]);
    }
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        $user = $this->authenticate();
        if ($user) {
            header('Content-Type: application/json');
            $product = $this->productModel->getProductById($id);
            if ($product) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $product
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Product not found'
                ]);
            }
        } else {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }
    }

    // Thêm sản phẩm mới
    public function store()
    {
        $user = $this->authenticate();
        if ($user) {
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents("php://input"), true);
            $name = $data['name'] ?? '';
            $description = $data['description'] ?? '';
            $price = $data['price'] ?? '';
            $category_id = $data['category_id'] ?? null;
            $image = $data['image'] ?? null; // Thêm trường hình ảnh
            $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = $this->uploadImage($_FILES['image']);
        }
            $result = $this->productModel->addProduct($name, $description, $price, $category_id, null);
            if (is_array($result)) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'errors' => $result
                ]);
            } else {
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Product created successfully'
                ]);
            }
        } else {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }
    }
    private function uploadImage($file)
{
    $target_dir = "uploads/";
    // Kiểm tra và tạo thư mục nếu chưa tồn tại
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra xem file có phải là hình ảnh không
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        throw new Exception("File không phải là hình ảnh.");
    }

    // Kiểm tra kích thước file (10 MB = 10 * 1024 * 1024 bytes)
    if ($file["size"] > 10 * 1024 * 1024) {
        throw new Exception("Hình ảnh có kích thước quá lớn.");
    }

    // Chỉ cho phép một số định dạng hình ảnh nhất định
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        throw new Exception("Chỉ cho phép các định dạng JPG, JPEG, PNG và GIF.");
    }

    // Lưu file
    if (!move_uploaded_file($file["tmp_name"], $target_file)) {
        throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
    }

    return $target_file;
}

    // Cập nhật sản phẩm theo ID
    public function update($id)
    {
        $user = $this->authenticate();
        if ($user) {
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents("php://input"), true);
            $name = $data['name'] ?? '';
            $description = $data['description'] ?? '';
            $price = $data['price'] ?? '';
            $category_id = $data['category_id'] ?? null;

            $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, null);
            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Product updated successfully'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Product update failed'
                ]);
            }
        } else {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }
    }

    // Xóa sản phẩm theo ID
    public function destroy($id)
    {
        $user = $this->authenticate();
        if ($user) {
            header('Content-Type: application/json');
            $result = $this->productModel->deleteProduct($id);
            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Product deleted successfully'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Product deletion failed'
                ]);
            }
        } else {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }
    }
}
?>