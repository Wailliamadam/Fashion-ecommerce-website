<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Database configuration
$host = 'localhost';
$dbname = 'fashion_db';
$username = 'root';
$password = '';

// Establish database connection
try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get current page and action from URL
$page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'dashboard';
$action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$p = isset($_GET['p']) ? (int)$_GET['p'] : 1; // Pagination page

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : '';
    
    try {
        // Handle order status updates
        if ($post_action === 'update_order_status') {
            $order_id = (int)$_POST['order_id'];
            $status = htmlspecialchars($_POST['status']);
            
            $stmt = $db->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
            if ($stmt->execute([$status, $order_id])) {
                $_SESSION['message'] = "Order status updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to update order status!";
            }
            
            header("Location: ?page=order-details&id=$order_id");
            exit;
        }
        
        // Handle product add/edit
        elseif ($post_action === 'add_product' || $post_action === 'update_product') {
            $product_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
            $categories = filter_input(INPUT_POST, 'categories', FILTER_SANITIZE_STRING);
            $branding = filter_input(INPUT_POST, 'branding', FILTER_SANITIZE_STRING);
            $color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_STRING);
            $size = filter_input(INPUT_POST, 'size', FILTER_SANITIZE_STRING);
            $tag = filter_input(INPUT_POST, 'tag', FILTER_SANITIZE_STRING);
            
            // Handle file upload
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image = uniqid('product_', true) . '.' . $file_ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
            } elseif ($post_action === 'update_product' && !empty($_POST['current_image'])) {
                $image = $_POST['current_image'];
            }
            
            if ($post_action === 'add_product') {
                $stmt = $db->prepare("INSERT INTO products (name, price, stock, categories, branding, color, size, tag, image, created_at) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $price, $stock, $categories, $branding, $color, $size, $tag, $image]);
                $_SESSION['message'] = "Product added successfully!";
            } else {
                $stmt = $db->prepare("UPDATE products SET 
                                    name = ?, price = ?, stock = ?, categories = ?, branding = ?, 
                                    color = ?, size = ?, tag = ?, image = ?
                                    WHERE id = ?");
                $stmt->execute([$name, $price, $stock, $categories, $branding, $color, $size, $tag, $image, $product_id]);
                $_SESSION['message'] = "Product updated successfully!";
            }
            
            header("Location: ?page=products");
            exit;
        }
        elseif ($post_action === 'delete_product') {
            $product_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            // First get the image path to delete it
            $stmt = $db->prepare("SELECT image FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product && !empty($product['image'])) {
                $image_path = 'uploads/' . $product['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            // Delete the product
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $_SESSION['message'] = "Product deleted successfully!";
            
            header("Location: ?page=products");
            exit;
        }
        elseif ($post_action === 'delete_message') {
            $message_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->execute([$message_id]);
            $_SESSION['message'] = "Message deleted successfully!";
            
            header("Location: ?page=messages");
            exit;
        }
        elseif ($post_action === 'delete_user') {
            $user_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $_SESSION['message'] = "User deleted successfully!";
            
            header("Location: ?page=users");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ?page=$page");
        exit;
    }
}

// Fetch data based on current page
try {
    switch ($page) {
        case 'dashboard':
            // Dashboard statistics
            $users_count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $products_count = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
            $orders_count = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
            $messages_count = $db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();

            // Recent orders for dashboard (5 most recent)
            $recent_orders = $db->query("
                SELECT 
                    o.id,
                    o.total,
                    o.status,
                    o.created_at,
                    CONCAT(o.first_name, ' ', o.last_name) AS customer_name,
                    o.email
                FROM orders o
                ORDER BY o.created_at DESC
                LIMIT 5
            ")->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'orders':
            // Paginated orders list
            $limit = 10;
            $offset = ($p - 1) * $limit;
            
            $orders = $db->query("
                SELECT 
                    o.id,
                    o.total,
                    o.status,
                    o.created_at,
                    CONCAT(o.first_name, ' ', o.last_name) AS customer_name,
                    o.email,
                    o.payment_method
                FROM orders o
                ORDER BY o.created_at DESC
                LIMIT $limit OFFSET $offset
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            $total_orders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
            $total_pages = ceil($total_orders / $limit);
            break;

        case 'order-details':
            if ($id) {
                // Get order details
                $stmt = $db->prepare("
                    SELECT * FROM orders 
                    WHERE id = ?
                ");
                $stmt->execute([$id]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Get order items
                $stmt = $db->prepare("
                    SELECT oi.*, p.name, p.price as unit_price, p.image
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                ");
                $stmt->execute([$id]);
                $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            break;

        case 'products':
            $products = $db->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

            if ($action === 'edit' && $id) {
                $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $edit_product = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            break;
            
        case 'users':
            $users = $db->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'messages':
            $messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
            break;
    }
} catch (PDOException $e) {
    die("Database error while fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fashion Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
    :root {
        --sidebar-width: 250px;
        --primary-color: #4e73df;
        --sidebar-bg: #2c3e50;
        --sidebar-hover: #34495e;
        --content-bg: #f8f9fc;
        --card-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    body {
        font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background-color: var(--content-bg);
        color: #333;
    }

    .sidebar {
        width: var(--sidebar-width);
        min-height: 100vh;
        position: fixed;
        background-color: var(--sidebar-bg);
        color: white;
        transition: all 0.3s;
        z-index: 1000;
    }

    .sidebar-brand {
        background-color: rgba(0, 0, 0, 0.2);
        height: 4.375rem;
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 1rem;
        font-weight: 600;
        border-left: 3px solid transparent;
        transition: all 0.3s;
    }

    .sidebar .nav-link:hover {
        color: white;
        background-color: var(--sidebar-hover);
    }

    .sidebar .nav-link.active {
        color: white;
        background-color: var(--sidebar-hover);
        border-left: 3px solid var(--primary-color);
    }

    .sidebar .nav-link i {
        margin-right: 0.5rem;
    }

    .main-content {
        margin-left: var(--sidebar-width);
        transition: all 0.3s;
        min-height: 100vh;
    }

    .navbar {
        height: 4.375rem;
        box-shadow: var(--card-shadow);
    }

    .card {
        border: none;
        box-shadow: var(--card-shadow);
        margin-bottom: 1.5rem;
        border-radius: 0.35rem;
    }

    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.35rem;
        font-weight: 600;
    }

    .status-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-processing {
        background-color: #cce5ff;
        color: #004085;
    }

    .status-shipped {
        background-color: #d4edda;
        color: #155724;
    }

    .status-delivered {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }

    .product-thumb {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 0.25rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #333;
    }

    .table th {
        background-color: #f8f9fc;
        border-bottom: 2px solid #e3e6f0;
        font-weight: 600;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2653d4;
    }

    .btn-outline-primary {
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .alert {
        border-radius: 0.35rem;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 0;
            overflow: hidden;
        }

        .sidebar.active {
            width: var(--sidebar-width);
        }

        .main-content {
            margin-left: 0;
        }

        .main-content.active {
            margin-left: var(--sidebar-width);
        }
    }
    </style>
</head>

<body>
    <div class="sidebar text-white">
        <div class="sidebar-brand p-3 d-flex align-items-center">
            <i class="bi bi-shop fs-4 me-2"></i>
            <span class="fs-5 fw-bold">Fashion Admin</span>
        </div>
        <hr class="text-white-50">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?= $page === 'dashboard' ? 'active' : '' ?>" href="?page=dashboard">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?= $page === 'products' ? 'active' : '' ?>" href="?page=products">
                    <i class="bi bi-box-seam me-2"></i> Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?= $page === 'orders' ? 'active' : '' ?>" href="?page=orders">
                    <i class="bi bi-cart-check me-2"></i> Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?= $page === 'users' ? 'active' : '' ?>" href="?page=users">
                    <i class="bi bi-people me-2"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?= $page === 'messages' ? 'active' : '' ?>" href="?page=messages">
                    <i class="bi bi-envelope me-2"></i> Messages
                </a>
            </li>
            <hr class="text-white-50">
            <li class="nav-item">
                <a class="nav-link text-white" href="logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <nav class="navbar navbar-expand bg-white shadow-sm">
            <div class="container-fluid">
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="d-flex align-items-center ms-auto">
                    <span class="me-3">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4">
            <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if ($page === 'dashboard'): ?>
            <!-- Dashboard Content -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Dashboard</h2>
            </div>

            <div class="row mb-4">
                <!-- Stats Cards -->
                <div class="col-md-3 mb-3">
                    <div class="card border-start border-primary border-4 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-primary text-uppercase mb-1">Total Users</h6>
                                    <h4 class="mb-0"><?= $users_count ?></h4>
                                </div>
                                <i class="bi bi-people fs-2 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-start border-success border-4 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-success text-uppercase mb-1">Total Products</h6>
                                    <h4 class="mb-0"><?= $products_count ?></h4>
                                </div>
                                <i class="bi bi-box-seam fs-2 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-start border-info border-4 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-info text-uppercase mb-1">Total Orders</h6>
                                    <h4 class="mb-0"><?= $orders_count ?></h4>
                                </div>
                                <i class="bi bi-cart-check fs-2 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-start border-warning border-4 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-warning text-uppercase mb-1">Messages</h6>
                                    <h4 class="mb-0"><?= $messages_count ?></h4>
                                </div>
                                <i class="bi bi-envelope fs-2 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_orders)): ?>
                    <div class="alert alert-info mb-0">No recent orders found</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                    <td>$<?= number_format($order['total'], 2) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?page=order-details&id=<?= $order['id'] ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php elseif ($page === 'orders'): ?>
            <!-- Orders Page -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>All Orders</h2>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Order List</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                    <div class="alert alert-info mb-0">No orders found</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                    <td>$<?= number_format($order['total'], 2) ?></td>
                                    <td><?= ucwords(str_replace('_', ' ', $order['payment_method'])) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?page=order-details&id=<?= $order['id'] ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($p > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=orders&p=<?= $p - 1 ?>">Previous</a>
                            </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i === $p ? 'active' : '' ?>">
                                <a class="page-link" href="?page=orders&p=<?= $i ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($p < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=orders&p=<?= $p + 1 ?>">Next</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php elseif ($page === 'order-details' && isset($order)): ?>
            <!-- Order Details Page -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Order #<?= $order['id'] ?></h2>
                <a href="?page=orders" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Orders
                </a>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong>
                                <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                            <p><strong>Address:</strong><br>
                                <?= nl2br(htmlspecialchars($order['address'])) ?><br>
                                <?= htmlspecialchars($order['city'] . ', ' . $order['postcode'] . ', ' . $order['country']) ?>
                            </p>
                            <?php if (!empty($order['notes'])): ?>
                            <p><strong>Notes:</strong> <?= htmlspecialchars($order['notes']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Order Date:</strong> <?= date('M d, Y H:i', strtotime($order['created_at'])) ?>
                            </p>
                            <p><strong>Status:</strong>
                                <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </p>
                            <p><strong>Payment Method:</strong>
                                <?= ucwords(str_replace('_', ' ', $order['payment_method'])) ?></p>
                            <hr>
                            <p><strong>Subtotal:</strong> $<?= number_format($order['subtotal'], 2) ?></p>
                            <p><strong>Shipping:</strong> $<?= number_format($order['shipping'], 2) ?></p>
                            <p><strong>Tax:</strong> $<?= number_format($order['tax'], 2) ?></p>
                            <p class="fw-bold"><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>

                            <form method="post" class="mt-4">
                                <input type="hidden" name="action" value="update_order_status">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                                <div class="row g-3 align-items-center">
                                    <div class="col-md-8">
                                        <select name="status" class="form-select">
                                            <option value="pending"
                                                <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="processing"
                                                <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing
                                            </option>
                                            <option value="shipped"
                                                <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                            <option value="delivered"
                                                <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered
                                            </option>
                                            <option value="cancelled"
                                                <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Items (<?= count($order_items) ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['image'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($item['image']) ?>"
                                                alt="<?= htmlspecialchars($item['name']) ?>" class="product-thumb me-3">
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>$<?= number_format($item['unit_price'], 2) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td>$<?= number_format($item['price'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                    <td>$<?= number_format($order['subtotal'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Shipping:</td>
                                    <td>$<?= number_format($order['shipping'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Tax:</td>
                                    <td>$<?= number_format($order['tax'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold">$<?= number_format($order['total'], 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <?php elseif ($page === 'products'): ?>
            <!-- Products Page -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Products</h2>
                <a href="?page=products&action=add" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Add Product
                </a>
            </div>

            <?php if ($action === 'add' || $action === 'edit'): ?>
            <!-- Add/Edit Product Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><?= $action === 'add' ? 'Add New' : 'Edit' ?> Product</h5>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action"
                            value="<?= $action === 'add' ? 'add_product' : 'update_product' ?>">
                        <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
                        <input type="hidden" name="current_image" value="<?= $edit_product['image'] ?? '' ?>">
                        <?php endif; ?>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" required
                                    value="<?= $action === 'edit' ? htmlspecialchars($edit_product['name']) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required
                                    value="<?= $action === 'edit' ? htmlspecialchars($edit_product['price']) : '' ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="stock" class="form-label">Stock Quantity</label>
                                <input type="number" class="form-control" id="stock" name="stock" required
                                    value="<?= $action === 'edit' ? htmlspecialchars($edit_product['stock']) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="categories" class="form-label">Categories</label>
                                <input type="text" class="form-control" id="categories" name="categories"
                                    value="<?= $action === 'edit' ? htmlspecialchars($edit_product['categories']) : '' ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="branding" class="form-label">Brand</label>
                                <input type="text" class="form-control" id="branding" name="branding"
                                    value="<?= $action === 'edit' ? htmlspecialchars($edit_product['branding']) : '' ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="color" class="form-label">Color</label>
                                <input type="text" class="form-control" id="color" name="color"
                                    value="<?= $action === 'edit' ? htmlspecialchars($edit_product['color']) : '' ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="size" class="form-label">Size</label>
                                <input type="text" class="form-control" id="size" name="size"
                                    value="<?= $action === 'edit' ? htmlspecialchars($edit_product['size']) : '' ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tag" class="form-label">Tags</label>
                            <input type="text" class="form-control" id="tag" name="tag"
                                value="<?= $action === 'edit' ? htmlspecialchars($edit_product['tag']) : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="image" name="image"
                                <?= $action === 'add' ? 'required' : '' ?>>
                            <?php if ($action === 'edit' && !empty($edit_product['image'])): ?>
                            <div class="mt-2">
                                <img src="uploads/<?= htmlspecialchars($edit_product['image']) ?>" alt="Current Image"
                                    class="product-thumb">
                                <p class="small text-muted mt-1">Current image</p>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="?page=products" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Product</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <!-- Products List -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Product List</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($products)): ?>
                    <div class="alert alert-info mb-0">No products found</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Categories</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($product['image'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($product['image']) ?>"
                                            alt="<?= htmlspecialchars($product['name']) ?>" class="product-thumb">
                                        <?php else: ?>
                                        <span class="text-muted">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td>$<?= number_format($product['price'], 2) ?></td>
                                    <td><?= $product['stock'] ?></td>
                                    <td><?= htmlspecialchars($product['categories']) ?></td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="?page=products&action=edit&id=<?= $product['id'] ?>"
                                                class="btn btn-sm btn-outline-primary me-2">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form method="post"
                                                onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                <input type="hidden" name="action" value="delete_product">
                                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php elseif ($page === 'users'): ?>
            <!-- Users Page -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Users</h2>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">User List</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($users)): ?>
                    <div class="alert alert-info mb-0">No users found</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <form method="post"
                                            onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php elseif ($page === 'messages'): ?>
            <!-- Messages Page -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Contact Messages</h2>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Message List</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($messages)): ?>
                    <div class="alert alert-info mb-0">No messages found</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $message): ?>
                                <tr>
                                    <td><?= $message['id'] ?></td>
                                    <td><?= htmlspecialchars($message['name']) ?></td>
                                    <td><?= htmlspecialchars($message['email']) ?></td>
                                    <td><?= htmlspecialchars($message['message']) ?></td>
                                    <td><?= date('M d, Y', strtotime($message['created_at'])) ?></td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="mailto:<?= htmlspecialchars($message['email']) ?>"
                                                class="btn btn-sm btn-outline-primary me-2">
                                                <i class="bi bi-reply"></i> Reply
                                            </a>
                                            <form method="post"
                                                onsubmit="return confirm('Are you sure you want to delete this message?');">
                                                <input type="hidden" name="action" value="delete_message">
                                                <input type="hidden" name="id" value="<?= $message['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Toggle sidebar on mobile
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
        document.querySelector('.main-content').classList.toggle('active');
    });
    </script>
</body>

</html>