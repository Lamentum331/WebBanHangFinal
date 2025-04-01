<?php
class ProductModel
{
    private $conn;
    private $table_name = "product";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy danh sách sản phẩm
    public function getProducts()
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy thông tin sản phẩm theo ID
    public function getProductById($id)
    {
        $query = "SELECT p.*, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Thêm sản phẩm mới
    public function addProduct($name, $description, $price, $category_id, $image)
{
    $errors = [];
    if (empty($name)) {
        $errors['name'] = 'Tên sản phẩm không được để trống';
    }
    if (empty($description)) {
        $errors['description'] = 'Mô tả không được để trống';
    }
    if (empty($price) || !is_numeric($price)) {
        $errors['price'] = 'Giá sản phẩm không hợp lệ';
    }
    if (empty($category_id)) {
        $errors['category_id'] = 'Danh mục không được để trống';
    }

    if (!empty($errors)) {
        return $errors;
    }

    $query = "INSERT INTO " . $this->table_name . " (name, description, price, category_id, image)
              VALUES (:name, :description, :price, :category_id, :image)";
    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':image', $image);

    return $stmt->execute();
}

    // Cập nhật sản phẩm
    // public function updateProduct($id, $name, $description, $price, $category_id, $image)
    // {
    //     $query = "UPDATE " . $this->table_name . " 
    //               SET name=:name, description=:description, price=:price, category_id=:category_id, image=:image 
    //               WHERE id=:id";
    //     $stmt = $this->conn->prepare($query);
    //     $name = htmlspecialchars(strip_tags($name ?? ''));
    //     $description = htmlspecialchars(strip_tags($description ?? ''));
    //     $price = htmlspecialchars(strip_tags($price ?? ''));
    //     $category_id = htmlspecialchars(strip_tags($category_id ?? ''));
    //     $image = htmlspecialchars(strip_tags($image ?? ''));

    //     $stmt->bindParam(':id', $id);
    //     $stmt->bindParam(':name', $name);
    //     $stmt->bindParam(':description', $description);
    //     $stmt->bindParam(':price', $price);
    //     $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    //     $stmt->bindParam(':image', $image);

    //     if ($stmt->execute()) {
    //         return true;
    //     }
    //     return false;
    // }
    public function updateProduct($id, $name, $description, $price, $category_id, $image)
{
    $query = "UPDATE product 
              SET name = :name, description = :description, price = :price, category_id = :category_id, image = :image 
              WHERE id = :id";
    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':image', $image);

    return $stmt->execute();
}

    // Xóa sản phẩm
    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Tìm kiếm sản phẩm
    public function searchProducts($query)
    {
        $searchTerm = '%' . $query . '%';
        $sql = "SELECT p.*, c.name as category_name 
                FROM " . $this->table_name . " p 
                LEFT JOIN category c ON p.category_id = c.id 
                WHERE p.name LIKE :query OR p.description LIKE :query 
                ORDER BY p.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':query', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}