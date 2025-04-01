<?php
// Require SessionHelper and other necessary files
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');

class CategoryController
{
    private $categoryModel;
    private $db;
    
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }
    
    public function index()
    {
        $categories = $this->categoryModel->getCategories();
        include 'app/views/categories/list.php';
    }
    
    public function add()
    {
        $errors = [];
        include 'app/views/categories/add.php';
    }
    public function beforeAction($action) {
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
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            
            // Validate input
            $errors = [];
            if (empty($name)) {
                $errors['name'] = 'Tên danh mục không được để trống';
            }
            
            if (empty($errors)) {
                // Call model to add category
                $result = $this->categoryModel->addCategory($name, $description);
                
                if ($result) {
                    header('Location: /webbanhang/Category');
                    exit;
                } else {
                    $errors['db'] = 'Có lỗi khi thêm danh mục';
                }
            }
            
            // If there are errors, redisplay the form
            include 'app/views/categories/add.php';
        }
    }
    
    public function edit($id)
    {
        $category = $this->categoryModel->getCategoryById($id);
        $errors = [];
        
        if (!$category) {
            // Handle category not found
            $errors['category'] = 'Không tìm thấy danh mục';
        }
        
        include 'app/views/categories/edit.php';
    }
    
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            
            // Validate input
            $errors = [];
            if (empty($name)) {
                $errors['name'] = 'Tên danh mục không được để trống';
            }
            
            if (empty($errors)) {
                // Call model to update category
                $result = $this->categoryModel->updateCategory($id, $name, $description);
                
                if ($result) {
                    header('Location: /webbanhang/Category');
                    exit;
                } else {
                    $errors['db'] = 'Có lỗi khi cập nhật danh mục';
                }
            }
            
            // If there are errors, redisplay the form with the filled data
            $category = (object) [
                'id' => $id,
                'name' => $name,
                'description' => $description
            ];
            
            include 'app/views/categories/edit.php';
        }
    }
    
    public function delete($id)
    {
        if ($id) {
            // Check if category exists and delete it
            $result = $this->categoryModel->deleteCategory($id);
            
            if ($result) {
                // Successful deletion
                header('Location: /webbanhang/Category');
                exit;
            }
        }
        
        // Error occurred or invalid ID
        header('Location: /webbanhang/Category');
    }
}
?>
