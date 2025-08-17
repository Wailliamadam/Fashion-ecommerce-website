<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Database connection
$host = 'localhost';
$dbname = 'fashion_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

$wishlist_items = [];
$productIds = [];

// Get product IDs from the session wishlist
if (isset($_SESSION['wishlist']) && !empty($_SESSION['wishlist'])) {
    // Correctly get the 'id' column from the wishlist array
    $productIds = array_column($_SESSION['wishlist'], 'id');

    if (!empty($productIds)) {
        // Create a string of placeholders for the SQL query
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        // Execute with the array of product IDs
        $stmt->execute($productIds);
        $wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Remove item from wishlist (using GET request)
if (isset($_GET['remove'])) {
    $product_id_to_remove = filter_var($_GET['remove'], FILTER_SANITIZE_NUMBER_INT);
    
    if (isset($_SESSION['wishlist']) && is_array($_SESSION['wishlist'])) {
        foreach ($_SESSION['wishlist'] as $key => $item) {
            // Find the item with the matching ID and remove it
            if ($item['id'] == $product_id_to_remove) {
                unset($_SESSION['wishlist'][$key]);
                // Re-index the array after removal
                $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
                break;
            }
        }
    }
    // Redirect back to the wishlist page after removal
    header('Location: wishlist.php');
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - Fashion Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #6c5ce7;
        --secondary-color: #a29bfe;
        --danger-color: #ff7675;
        --light-gray: #f8f9fa;
        --dark-gray: #343a40;
    }

    body {
        background-color: #f9f9f9;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .wishlist-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 15px;
    }

    .wishlist-header {
        text-align: center;
        margin-bottom: 2.5rem;
        position: relative;
    }

    .wishlist-header h1 {
        font-weight: 700;
        color: var(--dark-gray);
        position: relative;
        display: inline-block;
    }

    .wishlist-header h1:after {
        content: '';
        position: absolute;
        width: 50px;
        height: 3px;
        background: var(--primary-color);
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
    }

    .wishlist-header p {
        color: #6c757d;
        font-size: 1.1rem;
    }

    .wishlist-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        border: none;
    }

    .wishlist-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .product-img-container {
        height: 200px;
        overflow: hidden;
        position: relative;
    }

    .product-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .wishlist-card:hover .product-img {
        transform: scale(1.05);
    }

    .product-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: var(--danger-color);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }

    .product-info {
        padding: 1.5rem;
    }

    .product-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--dark-gray);
        font-size: 1.1rem;
    }

    .product-category {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .product-price {
        font-weight: 700;
        color: var(--primary-color);
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }

    .action-btns .btn {
        border-radius: 50px;
        padding: 8px 15px;
        font-size: 0.9rem;
        margin-right: 8px;
    }

    .btn-move-to-cart {
        background: var(--primary-color);
        color: white;
        border: none;
    }

    .btn-move-to-cart:hover {
        background: #5649d6;
        color: white;
    }

    .btn-remove {
        background: white;
        color: var(--danger-color);
        border: 1px solid var(--danger-color);
    }

    .btn-remove:hover {
        background: var(--danger-color);
        color: white;
    }

    .empty-wishlist {
        text-align: center;
        padding: 5rem 0;
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .empty-icon {
        font-size: 5rem;
        color: #e0e0e0;
        margin-bottom: 1.5rem;
    }

    .continue-shopping {
        background: var(--primary-color);
        color: white;
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 500;
        display: inline-block;
        margin-top: 1.5rem;
        transition: all 0.3s;
    }

    .continue-shopping:hover {
        background: #5649d6;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }

    .wishlist-actions {
        margin-top: 3rem;
        text-align: center;
    }

    .wishlist-actions .btn {
        min-width: 200px;
        padding: 12px;
        font-weight: 500;
        margin: 0 10px;
    }

    @media (max-width: 768px) {
        .product-img-container {
            height: 150px;
        }

        .action-btns .btn {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        .wishlist-actions .btn {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }
    }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="wishlist-container">
        <div class="wishlist-header">
            <h1><i class="fas fa-heart" style="color: var(--danger-color);"></i> My Wishlist</h1>
            <p>Your saved favorite items</p>
        </div>

        <?php if (empty($wishlist_items)): ?>
        <div class="empty-wishlist">
            <i class="far fa-heart empty-icon"></i>
            <h3>Your wishlist is empty</h3>
            <p class="text-muted">You haven't added any items to your wishlist yet.</p>
            <a href="shop.php" class="continue-shopping">
                <i class="fas fa-shopping-bag"></i> Continue Shopping
            </a>
        </div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($wishlist_items as $item): ?>
            <div class="col-lg-4 col-md-6">
                <div class="wishlist-card">
                    <div class="product-img-container">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>"
                            alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-img">
                        <?php if($item['discount'] > 0): ?>
                        <span class="product-badge">-<?php echo $item['discount']; ?>%</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h5 class="product-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p class="product-category"><?php echo htmlspecialchars($item['category']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="product-price">
                                $<?php echo number_format($item['price'] * (1 - $item['discount']/100), 2); ?>
                                <?php if($item['discount'] > 0): ?>
                                <small
                                    class="text-muted text-decoration-line-through">$<?php echo number_format($item['price'], 2); ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="action-btns">
                                <a href="?add_to_cart=<?php echo $item['id']; ?>" class="btn btn-move-to-cart btn-sm">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </a>
                                <a href="?remove=<?php echo $item['id']; ?>" class="btn btn-remove btn-sm">
                                    <i class="fas fa-trash-alt"></i> Remove
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="wishlist-actions">
            <a href="shop.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
            <a href="checkout.php" class="btn btn-primary">
                <i class="fas fa-credit-card"></i> Proceed to Checkout
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Confirm before removing item
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to remove this item from your wishlist?')) {
                e.preventDefault();
            }
        });
    });

    // Add animation when adding to cart
    document.querySelectorAll('.btn-move-to-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        });
    });
    </script>
</body>

</html>