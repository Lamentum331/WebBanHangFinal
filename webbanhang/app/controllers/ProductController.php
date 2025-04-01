<?php
// Require SessionHelper and other necessary files
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/utils/JWTHandler.php');
class ProductController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        // Đảm bảo session đã được khởi động
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }
    public function beforeAction($action)
    {
        // Các trang yêu cầu đăng nhập
        $protectedActions = ['profile', 'orders', 'checkout'];

        if (in_array($action, $protectedActions) && !isset($_SESSION['user_id'])) {
            // Lưu URL hiện tại để chuyển hướng sau khi đăng nhập
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            // Chuyển hướng đến trang đăng nhập
            header('Location: ' . BASE_URL . '/User/login');
            return false;
        }

        return true;
    }

    // public function index()
    // {
    //     $products = $this->productModel->getProducts();
    //     include 'app/views/product/list.php';
    // }
    public function index()
{
    $user = $this->authenticate(); // Kiểm tra JWT
    if (!$user) {
        header('Location: /webbanhang/account/login');
        exit;
    }

    $products = $this->productModel->getProducts();
    include 'app/views/product/list.php';
}
public function getProducts()
{
    header('Content-Type: application/json'); // Đặt Content-Type là application/json

    $products = $this->productModel->getProducts();
    echo json_encode(['status' => 'success', 'data' => $products]);
}
    public function show($id)
    {
        $product = $this->productModel->getProductById($id);
        if ($product) {
            include 'app/views/product/show.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    // public function add()
    // {
    //     if (session_status() === PHP_SESSION_NONE) {
    //         session_start();
    //     }
    //     if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    //         // Nếu không phải admin, chuyển hướng về trang chủ
    //         header('Location: /webbanhang');
    //         exit;
    //     }

    //     // Logic thêm sản phẩm
    //     $this->view('product/add');
    // }
//     public function add()
// {
//     if (session_status() === PHP_SESSION_NONE) {
//         session_start();
//     }

//     if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
//         // Nếu không phải admin, chuyển hướng về trang chủ
//         header('Location: /webbanhang');
//         exit;
//     }

//     // Lấy danh sách danh mục để hiển thị trong form thêm sản phẩm
//     $categories = (new CategoryModel($this->db))->getCategories();

//     // Hiển thị form thêm sản phẩm
//     $this->view('product/add', ['categories' => $categories]);
// }

public function add()
{
    $user = $this->authenticate(); // Kiểm tra JWT
    if ($user['role'] !== 'admin') {
        header('Location: /webbanhang');
        exit;
    }

    // Lấy danh sách danh mục
    $categories = (new CategoryModel($this->db))->getCategories();

    // Hiển thị form thêm sản phẩm
    $this->view('product/add', ['categories' => $categories]);
}
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            $image = $_POST['image'] ??'';

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = $this->uploadImage($_FILES['image']);
            } else {
                $image = "";
            }

            $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);

            if (is_array($result)) {
                $errors = $result;
                $categories = (new CategoryModel($this->db))->getCategories();
                include 'app/views/product/add.php';
            } else {
                header('Location: /webbanhang/Product');
            }
        }
    }
//     public function edit($id)
// {
//     // Kiểm tra session hoặc JWT để xác thực quyền admin
//     if (session_status() === PHP_SESSION_NONE) {
//         session_start();
//     }

//     if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
//         // Nếu không phải admin, chuyển hướng về trang chủ
//         header('Location: /webbanhang');
//         exit;
//     }

//     // Lấy thông tin sản phẩm từ cơ sở dữ liệu
//     $product = $this->productModel->getProductById($id);

//     // Kiểm tra nếu sản phẩm không tồn tại
//     if (!$product) {
//         header('Location: /webbanhang/Product/list');
//         exit;
//     }

//     // Lấy danh sách danh mục từ cơ sở dữ liệu
//     $categories = (new CategoryModel($this->db))->getCategories();

//     // Truyền dữ liệu sang view
//     $this->view('product/edit', [
//         'product' => $product,
//         'categories' => $categories
//     ]);
// }
public function logout()
{
    // Xóa cookie JWT
    setcookie('jwtToken', '', time() - 3600, '/'); // Hết hạn cookie
    session_destroy(); // Hủy session nếu có

    // Chuyển hướng đến trang đăng nhập
    header('Location: /webbanhang/account/login');
    exit;
}
public function edit($id)
{
    // Kiểm tra session hoặc JWT để xác thực quyền admin
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        // Nếu không phải admin, chuyển hướng về trang chủ
        header('Location: /webbanhang');
        exit;
    }

    // Lấy thông tin sản phẩm từ cơ sở dữ liệu
    $product = $this->productModel->getProductById($id);

    // Kiểm tra nếu sản phẩm không tồn tại
    if (!$product) {
        header('Location: /webbanhang/Product/list');
        exit;
    }
    $categories = (new CategoryModel($this->db))->getCategories();
    // Lấy danh sách danh mục từ cơ sở dữ liệu
    

    // Hiển thị trang chỉnh sửa sản phẩm
    include 'app/views/product/edit.php';
}
private function authenticate()
{
    // $jwtHandler = new JWTHandler();
    // $user = null;

    // if (isset($_COOKIE['jwtToken'])) {
    //     $jwt = $_COOKIE['jwtToken'];
    //     $decoded = $jwtHandler->decode($jwt);
    //     if ($decoded) {
    //         $user = $decoded;
    //     }
    // }

    // if (!$user) {
    //     header('Location: /webbanhang/account/login');
    //     exit;
    // }

    // return $user;
    $jwtHandler = new JWTHandler();
    $user = null;

    if (isset($_COOKIE['jwtToken'])) {
        $jwt = $_COOKIE['jwtToken'];
        $decoded = $jwtHandler->decode($jwt);
        if ($decoded) {
            $user = $decoded;
        }
    }

    if (!$user) {
        echo "<script>alert('Vui lòng đăng nhập để tiếp tục.');</script>";
        header('Location: /webbanhang/account/login');
        exit;
    }

    return $user;
    
}

    // public function edit($id)
    // {
    //     session_start();
    //     if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    //         // Nếu không phải admin, chuyển hướng về trang chủ
    //         header('Location: /webbanhang');
    //         exit;
    //     }

    //     // Logic sửa sản phẩm
    //     $product = $this->productModel->getProductById($id);
    //     $this->view('product/edit', ['product' => $product]);
    // }

//     public function update()
// {
//     if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
//         // Lấy dữ liệu từ request body
//         $input = json_decode(file_get_contents('php://input'), true);

//         $id = $input['id'] ?? null;
//         $name = $input['name'] ?? '';
//         $description = $input['description'] ?? '';
//         $price = $input['price'] ?? '';
//         $category_id = $input['category_id'] ?? null;

//         if (isset($input['image']) && !empty($input['image'])) {
//             $image = $input['image'];
//         } else {
//             $image = $_POST['existing_image'] ?? '';
//         }

//         // Gọi phương thức cập nhật sản phẩm
//         $edit = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);
//         if ($edit) {
//             echo json_encode(['message' => 'Product updated successfully']);
//         } else {
//             echo json_encode(['message' => 'Failed to update product']);
//         }
//     } else {
//         http_response_code(405);
//         echo json_encode(['message' => 'Method Not Allowed']);
//     }
// }
public function update()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? '';
        $category_id = $_POST['category_id'] ?? null;
        $image = $_POST['existing_image'] ?? '';

        // Xử lý hình ảnh
        $image = $_POST['existing_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image = $this->uploadImage($_FILES['image']);
        }

        // Cập nhật sản phẩm
        $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);

        if ($result) {
            header('Location: /webbanhang/Product/list');
        } else {
            echo "Đã xảy ra lỗi khi cập nhật sản phẩm.";
        }
    }
}

    public function delete($id)
    {
        if ($this->productModel->deleteProduct($id)) {
            header('Location: /webbanhang/Product');
        } else {
            echo "Đã xảy ra lỗi khi xóa sản phẩm.";
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

    public function addToCart($id)
    {
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            echo "Không tìm thấy sản phẩm.";
            return;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image
            ];
        }

        header('Location: /webbanhang/Product/cart');
    }

    public function cart()
    {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        include 'app/views/product/cart.php';
    }

    public function checkout()
    {
        include 'app/views/product/checkout.php';
    }

    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];

            // Kiểm tra giỏ hàng
            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                echo "Giỏ hàng trống.";
                return;
            }

            // Bắt đầu giao dịch
            $this->db->beginTransaction();
            try {
                // Lưu thông tin đơn hàng vào bảng orders
                $query = "INSERT INTO orders (name, phone, address) VALUES (:name, :phone, :address)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $address);
                $stmt->execute();
                $order_id = $this->db->lastInsertId();

                // Lưu chi tiết đơn hàng vào bảng order_details
                $cart = $_SESSION['cart'];
                foreach ($cart as $product_id => $item) {
                    $query = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                              VALUES (:order_id, :product_id, :quantity, :price)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':order_id', $order_id);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->bindParam(':price', $item['price']);
                    $stmt->execute();
                }

                // Xóa giỏ hàng sau khi đặt hàng thành công
                unset($_SESSION['cart']);

                // Commit giao dịch
                $this->db->commit();

                // Chuyển hướng đến trang xác nhận đơn hàng
                header('Location: /webbanhang/Product/orderConfirmation');
            } catch (Exception $e) {
                // Rollback giao dịch nếu có lỗi
                $this->db->rollBack();
                echo "Đã xảy ra lỗi khi xử lý đơn hàng: " . $e->getMessage();
            }
        }
    }

    public function orderConfirmation()
    {
        include 'app/views/product/orderConfirmation.php';
    }

    // Phương thức mới để cập nhật số lượng sản phẩm trong giỏ hàng (AJAX)
    public function updateQuantity()
    {
        // Kiểm tra xem có phải là AJAX request không
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
            return;
        }

        // Lấy thông tin từ request
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

        // Kiểm tra tính hợp lệ của dữ liệu
        if ($id <= 0 || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            return;
        }

        // Kiểm tra xem sản phẩm có tồn tại trong giỏ hàng không
        if (!isset($_SESSION['cart'][$id])) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng']);
            return;
        }

        // Cập nhật số lượng trong session
        $_SESSION['cart'][$id]['quantity'] = $quantity;

        // Trả về kết quả thành công
        echo json_encode(['success' => true]);
    }

    // Phương thức cải tiến để xóa sản phẩm khỏi giỏ hàng (hỗ trợ AJAX)
    public function removeFromCart($id)
    {
        // Kiểm tra xem có phải là AJAX request không
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';

        if (!$id) {
            if ($isAjax) {
                echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
                return;
            } else {
                header('Location: /webbanhang/Product/cart');
                return;
            }
        }

        // Xóa sản phẩm khỏi giỏ hàng
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }

        if ($isAjax) {
            echo json_encode(['success' => true]);
        } else {
            header('Location: /webbanhang/Product/cart');
        }
    }
    // Add this method to your ProductController class
    public function search()
    {
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';

        if (empty($query)) {
            // If no query provided, redirect to product list
            header('Location: /webbanhang/Product');
            return;
        }

        // Get search results from model
        $products = $this->productModel->searchProducts($query);

        // Pass search query to view for display purposes
        $searchQuery = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');

        // Load view with search results
        include 'app/views/product/search_results.php';
    }
    private function view($viewPath, $data = [])
{
    // Truyền dữ liệu sang view
    extract($data);

    // Đường dẫn tới file view
    $viewFile = 'app/views/' . $viewPath . '.php';

    // Kiểm tra xem file view có tồn tại không
    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        echo "View file not found: " . $viewFile;
    }
}
}
?>