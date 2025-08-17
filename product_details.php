<?php
// Database connection
$host = 'localhost';
$dbname = 'fashion_db';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get product ID from URL
    $product_id = $_GET['id'] ?? null;
    
    if (!$product_id) {
        header("Location: shop.php");
        exit();
    }

    // Get product details
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: shop.php");
        exit();
    }

    // Get related products (same category)
    $related_stmt = $db->prepare("SELECT * FROM products WHERE categories = ? AND id != ? AND stock > 0 LIMIT 4");
    $related_stmt->execute([$product['categories'], $product['id']]);
    $related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    die("A database error occurred. Please try again later.");
}

// Image handling function
function getProductImage($image_filename) {
    $upload_dir = 'uploads/';
    $admin_upload_dir = 'admins/uploads/';
    
    if (!empty($image_filename)) {
        $potential_paths = [
            $upload_dir . $image_filename,
            $admin_upload_dir . $image_filename
        ];

        foreach ($potential_paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
    }
    
    return 'assets/products/product-1.jpg'; // Default fallback
}
?>

<?php if (file_exists('includes/navbar.php')) include 'includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($product['name']) ?> - Fashion Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
    .product-gallery {
        margin-bottom: 20px;
    }

    .main-image {
        height: 500px;
        object-fit: cover;
        width: 100%;
        border-radius: 8px;
    }

    .thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        margin-right: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 4px;
    }

    .thumbnail:hover,
    .thumbnail.active {
        border-color: #0d6efd;
    }

    .product-info {
        padding-left: 30px;
    }

    .price {
        font-size: 24px;
        font-weight: bold;
        margin: 15px 0;
    }

    .discounted-price {
        color: #dc3545;
    }

    .original-price {
        text-decoration: line-through;
        color: #6c757d;
        font-size: 18px;
    }

    .product-meta {
        margin: 20px 0;
    }

    .product-meta div {
        margin-bottom: 10px;
    }

    .related-products {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid #eee;
    }

    .quantity-selector {
        width: 120px;
        margin-right: 15px;
    }

    .stock-status {
        font-weight: bold;
    }

    .in-stock {
        color: #28a745;
    }

    .low-stock {
        color: #ffc107;
    }

    .out-of-stock {
        color: #dc3545;
    }
    </style>
</head>

<body>
    <div class="container py-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-6">
                <div class="product-gallery">
                    <img src="<?= getProductImage($product['image']) ?>" id="mainImage" class="main-image img-fluid"
                        alt="<?= htmlspecialchars($product['name']) ?>"
                        onerror="this.onerror=null;this.src='assets/products/product-1.jpg';">
                </div>
            </div>

            <div class="col-lg-6 product-info">
                <h1 class="mb-3"><?= htmlspecialchars($product['name']) ?></h1>
                <p class="text-muted">Brand: <?= htmlspecialchars($product['branding']) ?></p>

                <div class="price">
                    <?php if ($product['discount'] > 0): ?>
                    <span class="discounted-price">MMK
                        <?= number_format($product['price'] * (1 - $product['discount'] / 100), 2) ?></span>
                    <span class="original-price">MMK <?= number_format($product['price'], 2) ?></span>
                    <span class="badge bg-danger ms-2"><?= $product['discount'] ?>% OFF</span>
                    <?php else: ?>
                    <span>MMK <?= number_format($product['price'], 2) ?></span>
                    <?php endif; ?>
                </div>

                <div class="product-meta">
                    <?php if (!empty($product['size'])): ?>
                    <div><strong>Size:</strong> <?= htmlspecialchars($product['size']) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($product['color'])): ?>
                    <div><strong>Color:</strong>
                        <span style="display: inline-block; width: 20px; height: 20px; background-color: <?= htmlspecialchars($product['color']) ?>; 
                                  border: 1px solid #ddd; vertical-align: middle;"></span>
                        <?= htmlspecialchars($product['color']) ?>
                    </div>
                    <?php endif; ?>

                    <div>
                        <strong>Availability:</strong>
                        <span class="stock-status 
                            <?= $product['stock'] > 10 ? 'in-stock' : 
                               ($product['stock'] > 0 ? 'low-stock' : 'out-of-stock') ?>">
                            <?= $product['stock'] > 10 ? 'In Stock' : 
                               ($product['stock'] > 0 ? 'Low Stock ('.$product['stock'].' left)' : 'Out of Stock') ?>
                        </span>
                    </div>
                </div>

                <p class="mb-4"><?= nl2br(htmlspecialchars($product['description'] ?? 'No description available.')) ?>
                </p>

                <form class="add-to-cart-form mb-4">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                    <?php if ($product['stock'] > 0): ?>
                    <div class="d-flex align-items-center mb-3">
                        <select name="quantity" class="form-select quantity-selector">
                            <?php 
                                $max_quantity = min($product['stock'], 10);
                                for ($i = 1; $i <= $max_quantity; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>

                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                            <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                        </button>
                    </div>

                    <!-- <button type="button" class="btn btn-outline-secondary add-to-wishlist"
                        data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>">
                        <i class="far fa-heart me-2"></i> Add to Wishlist
                    </button> -->
                    <?php else: ?>
                    <button class="btn btn-secondary btn-lg" disabled>
                        <i class="fas fa-ban me-2"></i> Out of Stock
                    </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if (!empty($related_products)): ?>
        <div class="related-products">
            <h3 class="mb-4">You May Also Like</h3>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php foreach ($related_products as $related): ?>
                <div class="col">
                    <div class="card h-100">
                        <img src="<?= getProductImage($related['image']) ?>" class="card-img-top"
                            alt="<?= htmlspecialchars($related['name']) ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($related['name']) ?></h5>
                            <p class="card-text text-muted small"><?= htmlspecialchars($related['branding']) ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if ($related['discount'] > 0): ?>
                                    <span class="text-danger fw-bold">MMK
                                        <?= number_format($related['price'] * (1 - $related['discount'] / 100), 2) ?></span>
                                    <span class="text-decoration-line-through text-muted small ms-2">MMK
                                        <?= number_format($related['price'], 2) ?></span>
                                    <?php else: ?>
                                    <span class="fw-bold">MMK <?= number_format($related['price'], 2) ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="product_details.php?id=<?= $related['id'] ?>"
                                    class="btn btn-sm btn-outline-primary">View</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add to cart form submission
        const addToCartForm = document.querySelector('.add-to-cart-form');
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const productId = formData.get('product_id');
                const quantity = formData.get('quantity');

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Adding...';
                submitBtn.disabled = true;

                fetch('add_to_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: quantity
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(
                                '<?= htmlspecialchars($product['name']) ?> added to cart!',
                                'success');
                            updateCartCount(data.cart_count);
                        } else {
                            showNotification(data.message || 'Failed to add to cart', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Network error. Please try again.', 'danger');
                    })
                    .finally(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });
        }

        // Wishlist functionality
        const wishlistBtn = document.querySelector('.add-to-wishlist');
        if (wishlistBtn) {
            wishlistBtn.addEventListener('click', function() {
                const productId = this.dataset.id;
                const productName = this.dataset.name;

                // Show loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
                this.disabled = true;

                fetch('wishlist_handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${productId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.action === 'added') {
                                this.innerHTML = '<i class="fas fa-heart me-2"></i> In Wishlist';
                                this.classList.add('btn-danger');
                                this.classList.remove('btn-outline-secondary');
                                showNotification(`${productName} added to wishlist`, 'success');
                            } else {
                                this.innerHTML =
                                    '<i class="far fa-heart me-2"></i> Add to Wishlist';
                                this.classList.remove('btn-danger');
                                this.classList.add('btn-outline-secondary');
                                showNotification(`${productName} removed from wishlist`, 'info');
                            }

                            // Update wishlist badge if exists
                            const wishlistBadge = document.querySelector('.wishlist-badge');
                            if (wishlistBadge) {
                                wishlistBadge.textContent = data.wishlist_count;
                                wishlistBadge.style.display = data.wishlist_count > 0 ? 'block' :
                                    'none';
                            }
                        } else {
                            showNotification(data.message || 'Failed to update wishlist', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Failed to update wishlist. Please try again.', 'danger');
                    })
                    .finally(() => {
                        this.disabled = false;
                    });
            });
        }

        // Notification function
        function showNotification(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} alert-dismissible fade show`;
            toast.role = 'alert';
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '9999';
            toast.style.maxWidth = '300px';
            toast.innerHTML = `
                <strong>${type === 'success' ? 'Success!' : type === 'danger' ? 'Error!' : 'Info!'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.body.appendChild(toast);

            // Auto remove after 3 seconds
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Update cart count in navbar
        function updateCartCount(count) {
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = count;
            });
        }
    });
    </script>
</body>

</html>

<?php
if (file_exists('includes/footer.php')) {
    include 'includes/footer.php';
}
?>