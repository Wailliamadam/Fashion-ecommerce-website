<?php
// admins/add_product.php

// Database connection
$host = 'localhost';
$dbname = 'fashion_db';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get form data
    $name = $_POST['name'] ?? null;
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $categories = $_POST['categories'] ?? null;
    $branding = $_POST['branding'] ?? null;
    $size = $_POST['size'] ?? null;
    $color = $_POST['color'] ?? null;
    $discount = $_POST['discount'] ?? 0;
    $image_filename = null;
    
    // Image Upload Handling
    $target_dir = "../uploads/"; // Path to the uploads folder from the admin directory
    
    // Create the uploads directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $file_info = pathinfo($_FILES["image"]["name"]);
        $file_extension = strtolower($file_info['extension']);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            // Create a unique filename to prevent overwrites
            $new_filename = time() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $new_filename;
            
            // Move the file from temp directory to the target directory
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_filename = $new_filename;
                $message .= "Image uploaded successfully. ";
            } else {
                $message .= "Error uploading the image. ";
            }
        } else {
            $message .= "Invalid file type. Only JPG, JPEG, PNG, GIF are allowed. ";
        }
    } else {
        $message .= "No image uploaded. ";
    }
    
    // Insert into database
    try {
        $sql = "INSERT INTO products (name, price, stock, categories, branding, size, color, discount, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->execute([$name, $price, $stock, $categories, $branding, $size, $color, $discount, $image_filename]);
        
        $message .= "Product '$name' added successfully.";
    } catch (PDOException $e) {
        $message .= "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Add New Product</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>
                        <form action="add_product.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="stock" name="stock" required>
                            </div>
                            <div class="mb-3">
                                <label for="categories" class="form-label">Category</label>
                                <input type="text" class="form-control" id="categories" name="categories">
                            </div>
                            <div class="mb-3">
                                <label for="branding" class="form-label">Brand</label>
                                <input type="text" class="form-control" id="branding" name="branding">
                            </div>
                            <div class="mb-3">
                                <label for="size" class="form-label">Size</label>
                                <input type="text" class="form-control" id="size" name="size">
                            </div>
                            <div class="mb-3">
                                <label for="color" class="form-label">Color</label>
                                <input type="text" class="form-control" id="color" name="color">
                            </div>
                            <div class="mb-3">
                                <label for="discount" class="form-label">Discount (%)</label>
                                <input type="number" class="form-control" id="discount" name="discount" min="0"
                                    max="100">
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="image" name="image">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>