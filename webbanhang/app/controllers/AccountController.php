<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');
require_once('app/utils/JWTHandler.php');

class AccountController
{
    private $accountModel;
    private $db;
    private $jwtHandler;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
        $this->jwtHandler = new JWTHandler(); // Khởi tạo JWTHandler
    }

    // Hiển thị trang đăng ký
    public function register()
    {
        include_once 'app/views/account/register.php';
    }

    // Hiển thị trang đăng nhập
    public function login()
    {
        include_once 'app/views/account/login.php';
    }

    // Xử lý đăng ký tài khoản
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $fullName = $_POST['fullname'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmpassword'] ?? '';
            $errors = [];

            // Kiểm tra dữ liệu đầu vào
            if (empty($username)) {
                $errors['username'] = "Vui lòng nhập tên đăng nhập!";
            }
            if (empty($fullName)) {
                $errors['fullname'] = "Vui lòng nhập họ và tên!";
            }
            if (empty($password)) {
                $errors['password'] = "Vui lòng nhập mật khẩu!";
            }
            if ($password !== $confirmPassword) {
                $errors['confirmPass'] = "Mật khẩu và xác nhận mật khẩu không khớp!";
            }

            // Kiểm tra username đã được đăng ký chưa
            $account = $this->accountModel->getAccountByUsername($username);
            if ($account) {
                $errors['account'] = "Tài khoản này đã được đăng ký!";
            }

            // Nếu có lỗi, hiển thị lại form đăng ký
            if (count($errors) > 0) {
                include_once 'app/views/account/register.php';
            } else {
                // Mã hóa mật khẩu và lưu vào cơ sở dữ liệu
                $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $result = $this->accountModel->save($username, $fullName, $password);
                if ($result) {
                    header('Location: /webbanhang/account/login');
                }
            }
        }
    }

    // Xử lý đăng xuất
    // public function logout()
    // {
    //     session_start();
    //     session_unset(); // Xóa tất cả các biến session
    //     session_destroy(); // Hủy session
    //     header('Location: /webbanhang/product'); // Chuyển hướng về trang sản phẩm
    // }
    public function logout()
    {
        // Xóa cookie jwtToken
        setcookie('jwtToken', '', time() - 3600, "/", "", false, true); // Hết hạn cookie
        header('Location: /webbanhang/account/login'); // Chuyển hướng về trang đăng nhập
        exit;
    }

    // Kiểm tra thông tin đăng nhập và trả về JWT
    public function checkLogin()
    {
        header('Content-Type: application/json');
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    // Kiểm tra thông tin đăng nhập
    $user = $this->accountModel->getAccountByUserName($username);
    if ($user && password_verify($password, $user->password)) {
        // Tạo JWT token
        $token = $this->jwtHandler->encode([
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role // Lưu role vào token
        ]);

        // Lưu token vào cookie
        setcookie('jwtToken', $token, time() + 3600, "/", "", false, true); // Cookie có hiệu lực trong 1 giờ

        echo json_encode([
            'status' => 'success',
            'token' => $token
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'
        ]);
    }
    }

    // Xác thực JWT token
    public function verifyToken()
    {
        header('Content-Type: application/json');
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $jwt = $matches[1];

            $decodedData = $this->jwtHandler->decode($jwt);

            if ($decodedData) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $decodedData
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Token không hợp lệ hoặc đã hết hạn'
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Authorization header không hợp lệ'
            ]);
        }
    }

    // Hiển thị view
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