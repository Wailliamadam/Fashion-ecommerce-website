<?php
// Database connection
$host = 'localhost';
$dbname = 'fashion_db';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- Start: Refactored Filtering Logic ---
    $where_clauses = ["stock > 0"];
    $params = [];

    // Filters
    $category_filter = $_GET['category'] ?? null;
    $brand_filter = $_GET['brand'] ?? null;
    $price_min = $_GET['price_min'] ?? null;
    $price_max = $_GET['price_max'] ?? null;
    $size_filter = $_GET['size'] ?? null;
    $color_filter = $_GET['color'] ?? null;
    $sort = $_GET['sort'] ?? 'newest';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 12;
    $offset = ($page - 1) * $per_page;

    if ($category_filter) {
        $where_clauses[] = "categories = ?";
        $params[] = $category_filter;
    }
    if ($brand_filter) {
        $where_clauses[] = "branding = ?";
        $params[] = $brand_filter;
    }
    if ($price_min && $price_max) {
        $where_clauses[] = "price BETWEEN ? AND ?";
        $params[] = $price_min;
        $params[] = $price_max;
    }
    if ($size_filter) {
        $where_clauses[] = "size = ?";
        $params[] = $size_filter;
    }
    if ($color_filter) {
        $where_clauses[] = "color = ?";
        $params[] = $color_filter;
    }

    $where_sql = implode(' AND ', $where_clauses);
    // --- End: Refactored Filtering Logic ---


    // 1. Get total count for pagination
    $count_query = "SELECT COUNT(*) FROM products WHERE $where_sql";
    $count_stmt = $db->prepare($count_query);
    $count_stmt->execute($params);
    $total_products = $count_stmt->fetchColumn();
    $total_pages = ceil($total_products / $per_page);

    // 2. Build and execute the main query for fetching products
    $query = "SELECT * FROM products WHERE $where_sql";

    // Sorting
    switch ($sort) {
        case 'price_low':
            $query .= " ORDER BY price ASC";
            break;
        case 'price_high':
            $query .= " ORDER BY price DESC";
            break;
        default:
            $query .= " ORDER BY created_at DESC";
    }

    // Pagination
    $query .= " LIMIT ? OFFSET ?";

    // Add pagination parameters to the params array
    $params[] = $per_page;
    $params[] = $offset;

    // Execute main query
    $stmt = $db->prepare($query);
    
    // Determine the type for each parameter before binding
    $types = [];
    foreach ($params as $param) {
        if (is_int($param)) {
            $types[] = PDO::PARAM_INT;
        } elseif (is_numeric($param)) {
            $types[] = PDO::PARAM_INT;
        } else {
            $types[] = PDO::PARAM_STR;
        }
    }

    // Bind parameters using the determined types
    foreach ($params as $i => $param) {
        $stmt->bindValue($i + 1, $param, $types[$i]);
    }
    
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get filter options
    $categories = $db->query("SELECT DISTINCT categories FROM products WHERE stock > 0")->fetchAll(PDO::FETCH_COLUMN);
    $brands = $db->query("SELECT DISTINCT branding FROM products WHERE stock > 0")->fetchAll(PDO::FETCH_COLUMN);
    $sizes = $db->query("SELECT DISTINCT size FROM products WHERE stock > 0 AND size IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
    $colors = $db->query("SELECT DISTINCT color FROM products WHERE stock > 0 AND color IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    // Log the detailed error for you to debug
    error_log("Database Error: " . $e->getMessage());
    // Show a generic, user-friendly message
    die("A database error occurred. Please try again later.");
}

// Helper functions
function buildPageLink($page, $add = []) {
    return 'shop.php?' . http_build_query(array_merge($_GET, ['page' => $page], $add));
}

function buildFilterLink($filter, $value) {
    $current_filters = $_GET;
    if ($value === '' || $value === null) {
        unset($current_filters[$filter]);
    } else {
        $current_filters[$filter] = $value;
    }
    $current_filters['page'] = 1; // Reset to first page when filter changes
    return 'shop.php?' . http_build_query($current_filters);
}

function isActiveFilter($filter, $value) {
    return isset($_GET[$filter]) && $_GET[$filter] == $value;
}
?>

<?php if (file_exists('includes/navbar.php')) include 'includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fashion Shop - Browse Our Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
    /* Pagination Container */
    .pagination {
        margin: 30px 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Page Items */
    .pagination .page-item {
        margin: 0 4px;
        transition: all 0.3s ease;
    }

    /* Page Links */
    .pagination .page-link {
        color: #555;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    /* Hover State */
    .pagination .page-link:hover {
        color: #333;
        background-color: #e9ecef;
        border-color: #dee2e6;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Active State */
    .pagination .page-item.active .page-link {
        color: white;
        background-color: #007bff;
        border-color: #007bff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    /* Disabled State */
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        opacity: 0.7;
        pointer-events: none;
    }

    /* Navigation Arrows */
    .pagination .page-link[aria-label="Previous"],
    .pagination .page-link[aria-label="Next"] {
        padding: 8px 12px;
        font-size: 0.9rem;
    }

    /* Responsive Adjustments */
    @media (max-width: 576px) {
        .pagination .page-item {
            margin: 0 2px;
        }

        .pagination .page-link {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
    }


    #wishlist-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        color: #333;
        font-weight: 500;
        z-index: 1050;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-20px);
        transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
    }

    #wishlist-notification.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    #wishlist-notification.alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    #wishlist-notification.alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Animation for Active State */
    @keyframes pulse {
        0% {
            transform: translateY(-2px) scale(1);
        }

        50% {
            transform: translateY(-2px) scale(1.05);
        }

        100% {
            transform: translateY(-2px) scale(1);
        }
    }

    .pagination .page-item.active .page-link {
        animation: pulse 1.5s infinite;
    }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="filter-section mb-4">
                    <h5 class="filter-title mb-3">Filters</h5>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Categories</h6>
                        <div class="list-group list-group-flush">
                            <a href="<?= buildFilterLink('category', '') ?>"
                                class="list-group-item list-group-item-action border-0 p-1 <?= !isset($_GET['category']) ? 'active' : '' ?>">
                                All Categories
                            </a>
                            <?php foreach ($categories as $cat): ?>
                            <a href="<?= buildFilterLink('category', $cat) ?>"
                                class="list-group-item list-group-item-action border-0 p-1 <?= isActiveFilter('category', $cat) ? 'active' : '' ?>">
                                <?= htmlspecialchars($cat) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Brands</h6>
                        <div class="list-group list-group-flush">
                            <a href="<?= buildFilterLink('brand', '') ?>"
                                class="list-group-item list-group-item-action border-0 p-1 <?= !isset($_GET['brand']) ? 'active' : '' ?>">
                                All Brands
                            </a>
                            <?php foreach ($brands as $brand): ?>
                            <a href="<?= buildFilterLink('brand', $brand) ?>"
                                class="list-group-item list-group-item-action border-0 p-1 <?= isActiveFilter('brand', $brand) ? 'active' : '' ?>">
                                <?= htmlspecialchars($brand) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Price Range</h6>
                        <form method="get" action="shop.php">
                            <input type="hidden" name="page" value="1" />
                            <?php foreach ($_GET as $key => $value): ?>
                            <?php if (!in_array($key, ['price_min', 'price_max', 'page'])): ?>
                            <input type="hidden" name="<?= htmlspecialchars($key) ?>"
                                value="<?= htmlspecialchars($value) ?>" />
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <div class="row g-2 mb-2">
                                <div class="col">
                                    <input type="number" class="form-control" name="price_min" placeholder="Min"
                                        value="<?= htmlspecialchars($_GET['price_min'] ?? '') ?>" />
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control" name="price_max" placeholder="Max"
                                        value="<?= htmlspecialchars($_GET['price_max'] ?? '') ?>" />
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100">Apply</button>
                        </form>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Sizes</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= buildFilterLink('size', '') ?>"
                                class="btn btn-sm <?= !isset($_GET['size']) ? 'btn-primary' : 'btn-outline-secondary' ?>">
                                All
                            </a>
                            <?php foreach ($sizes as $size): ?>
                            <a href="<?= buildFilterLink('size', $size) ?>"
                                class="btn btn-sm <?= isActiveFilter('size', $size) ? 'btn-primary' : 'btn-outline-secondary' ?>">
                                <?= htmlspecialchars($size) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Colors</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= buildFilterLink('color', '') ?>"
                                class="btn btn-sm <?= !isset($_GET['color']) ? 'btn-primary' : 'btn-outline-secondary' ?>">
                                All
                            </a>
                            <?php foreach ($colors as $color): ?>
                            <a href="<?= buildFilterLink('color', $color) ?>"
                                class="btn btn-sm <?= isActiveFilter('color', $color) ? 'btn-primary' : 'btn-outline-secondary' ?>"
                                style="background-color: <?= htmlspecialchars($color) ?>; color: white; text-shadow: 0 0 2px #000;">
                                <?= htmlspecialchars($color) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>


                    <a href="shop.php" class="btn btn-outline-danger w-100">Reset Filters</a>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Our Products</h2>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Sort: <?= ucfirst(str_replace('_', ' ', $sort)) ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item <?= $sort === 'newest' ? 'active' : '' ?>"
                                    href="<?= buildPageLink(1, ['sort' => 'newest']) ?>">Newest</a></li>
                            <li><a class="dropdown-item <?= $sort === 'price_low' ? 'active' : '' ?>"
                                    href="<?= buildPageLink(1, ['sort' => 'price_low']) ?>">Price: Low to High</a></li>
                            <li><a class="dropdown-item <?= $sort === 'price_high' ? 'active' : '' ?>"
                                    href="<?= buildPageLink(1, ['sort' => 'price_high']) ?>">Price: High to Low</a></li>
                        </ul>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    No products found matching your filters. <a href="shop.php" class="alert-link">Clear filters</a> to
                    see all products.
                </div>
                <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card product-card h-100 shadow-sm">
                            <?php
                            // Image configuration
                            $upload_dir = 'uploads/';
                            $default_images_dir = 'assets/products/defaults/';
                            $admin_upload_dir = 'admins/uploads/';

                            // Get image filename from product data
                            $image_filename = $product['image'] ?? '';
                            $image_src = '';

                            // 1. Check main uploads directory
                            if (!empty($image_filename)) {
                                $potential_paths = [
                                    $upload_dir . $image_filename,
                                    $admin_upload_dir . $image_filename
                                ];

                                foreach ($potential_paths as $path) {
                                    if (file_exists($path)) {
                                        $image_src = $path;
                                        break;
                                    }
                                }
                            }

                            // 2. If no image found, use a default fallback
                            if (empty($image_src)) {
                                $image_src = 'assets/products/product-1.jpg'; // A single, reliable fallback
                            }
                            ?>

                            <div class="product-img-container position-relative">
                                <img src="<?= htmlspecialchars($image_src) ?>" class="card-img-top"
                                    alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy"
                                    style="height: 250px; object-fit: cover;"
                                    onerror="this.onerror=null;this.src='assets/products/product-1.jpg';">
                            </div>

                            <div class="card-body d-flex flex-column">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="card-text text-muted small mb-2">
                                        <?= htmlspecialchars($product['branding']) ?></p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div>
                                        <?php if ($product['discount'] > 0): ?>
                                        <span class="text-danger fw-bold">MMK
                                            <?= number_format($product['price'] * (1 - $product['discount'] / 100), 2) ?></span>
                                        <span class="text-decoration-line-through text-muted small ms-2">MMK
                                            <?= number_format($product['price'], 2) ?></span>
                                        <?php else: ?>
                                        <span class="fw-bold">MMK <?= number_format($product['price'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($product['stock'] > 0): ?>
                                    <span class="badge bg-light text-dark"><?= $product['stock'] ?> left</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 pt-0">
                                <div class="d-grid gap-2">
                                    <?php if ($product['stock'] > 0): ?>
                                    <?php if (!empty($product['size'])): ?>
                                    <div class="product-size mb-1">
                                        <small class="text-muted">Size:
                                            <?= htmlspecialchars($product['size']) ?></small>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($product['color'])): ?>
                                    <div class="product-color mb-2">
                                        <small class="text-muted">Color:
                                            <?= htmlspecialchars($product['color']) ?></small>
                                    </div>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-between gap-2">
                                        <button class="btn btn-outline-primary flex-grow-1"
                                            onclick="window.location.href='product_details.php?id=<?= $product['id'] ?>'">
                                            <i class="fas fa-info-circle me-2"></i> Product Details
                                        </button>

                                        <button class="btn btn-primary add-to-cart" data-id="<?= $product['id'] ?>"
                                            data-name="<?= htmlspecialchars($product['name']) ?>">
                                            <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                                        </button>

                                        <div id="notification-toast"
                                            style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;"></div>

                                    </div>

                                    <?php else: ?>
                                    <button class="btn btn-outline-secondary" disabled>
                                        <i class="fas fa-ban me-2"></i>Out of Stock
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.add-to-cart').forEach(btn => {
                        btn.addEventListener('click', async function(e) {
                            e.preventDefault();

                            const productId = this.dataset.id;
                            const productName = this.dataset.name;

                            // Show loading state
                            const originalHTML = this.innerHTML;
                            this.innerHTML =
                                '<i class="fas fa-spinner fa-spin me-2"></i> Adding...';
                            this.disabled = true;

                            try {
                                const response = await fetch('add_to_cart.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        product_id: productId,
                                        quantity: 1
                                    })
                                });

                                const result = await response.json();

                                if (result.success) {
                                    showNotification(`${productName} added to cart!`,
                                        'success');
                                    updateCartCount(result.cart_count);
                                } else {
                                    showNotification(result.message ||
                                        'Failed to add to cart', 'danger');
                                }

                                this.innerHTML = originalHTML;
                                this.disabled = false;

                            } catch (error) {
                                showNotification('Network error. Please try again.',
                                    'danger');
                                this.innerHTML = originalHTML;
                                this.disabled = false;
                            }
                        });
                    });

                    // Notification function
                    function showNotification(message, type = 'success') {
                        const toast = document.createElement('div');
                        toast.className = `alert alert-${type} alert-dismissible fade show`;
                        toast.role = 'alert';
                        toast.innerHTML = `
            <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
                        const container = document.getElementById('notification-toast');
                        container.appendChild(toast);

                        // Auto remove after 3 seconds
                        setTimeout(() => {
                            toast.remove();
                        }, 3000);
                    }

                    // Update cart count
                    function updateCartCount(count) {
                        document.querySelectorAll('.cart-count').forEach(el => {
                            el.textContent = count;
                        });
                    }
                });
                </script>


                <?php if ($total_pages > 1): ?>
                <nav class="mt-5" aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= buildPageLink($page - 1) ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php 
                        // Show limited page numbers with ellipsis
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
            
                    if ($start > 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>

                        <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="<?= buildPageLink($i) ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($end < $total_pages): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>

                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= buildPageLink($page + 1) ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            // You would typically make an AJAX request here to a file like 'add_to_cart.php'
            console.log('Adding product ' + productId + ' to cart.');
            alert('Added product to cart! (Placeholder)');

            // Example: Update cart badge in the navbar
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                const currentCount = parseInt(cartBadge.textContent) || 0;
                cartBadge.textContent = currentCount + 1;
            }
        });
    });



    // add wishlist functionality
    document.addEventListener('DOMContentLoaded', function() {
        const wishlistButtons = document.querySelectorAll('.add-to-wishlist');
        const wishlistBadge = document.querySelector('.wishlist-badge');

        // Create notification element
        const notification = document.createElement('div');
        notification.id = 'wishlist-notification';
        notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 4px;
        background-color: #28a745;
        color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 9999;
        opacity: 0;
        transform: translateY(-30px);
        transition: all 0.3s ease;
        pointer-events: none;
        max-width: 300px;
        word-wrap: break-word;
    `;
        document.body.appendChild(notification);

        // Function to update button UI
        function updateButtonUI(button, action) {
            if (action === 'added') {
                button.classList.add('btn-primary');
                button.classList.remove('btn-outline-primary');
                button.innerHTML = '<i class="fas fa-heart"></i> Added to Wishlist';
            } else if (action === 'removed') {
                button.classList.remove('btn-primary');
                button.classList.add('btn-outline-primary');
                button.innerHTML = '<i class="far fa-heart"></i> Wishlist';
            }
        }

        // Improved notification function
        function showNotification(message, isSuccess = true) {
            // Update notification content and style
            notification.textContent = message;
            notification.style.backgroundColor = isSuccess ? '#28a745' : '#dc3545';

            // Reset animation state
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-30px)';

            // Force reflow before showing
            void notification.offsetHeight;

            // Show notification
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';

            // Hide after delay
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-30px)';
            }, 3000);
        }

        // Attach click listeners to wishlist buttons
        wishlistButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.id;
                const productName = this.dataset.name;

                // Show loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                this.disabled = true;

                fetch('wishlist_handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${productId}`
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            updateButtonUI(this, data.action);

                            // Update wishlist badge count
                            if (wishlistBadge) {
                                wishlistBadge.textContent = data.wishlist_count;
                                wishlistBadge.style.display = data.wishlist_count > 0 ?
                                    'block' : 'none';
                            }

                            // Show appropriate notification
                            const message = data.action === 'added' ?
                                `${productName} added to wishlist` :
                                `${productName} removed from wishlist`;
                            showNotification(message);
                        } else {
                            showNotification(data.message || 'Failed to update wishlist',
                                false);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Failed to update wishlist. Please try again.',
                            false);
                    })
                    .finally(() => {
                        this.disabled = false;
                        this.innerHTML = originalText;
                    });
            });
        });

        // Initialize wishlist badge visibility
        if (wishlistBadge) {
            wishlistBadge.style.display = wishlistBadge.textContent > 0 ? 'block' : 'none';
        }
    });




    // Price range validation
    const priceForm = document.querySelector('form[action="shop.php"]');
    if (priceForm) {
        priceForm.addEventListener('submit', function(e) {
            const priceMinInput = this.elements.price_min;
            const priceMaxInput = this.elements.price_max;

            // Only validate if both fields are filled
            if (priceMinInput.value && priceMaxInput.value) {
                const priceMin = parseFloat(priceMinInput.value);
                const priceMax = parseFloat(priceMaxInput.value);

                if (priceMin > priceMax) {
                    e.preventDefault();
                    alert('Minimum price cannot be greater than maximum price.');
                    priceMinInput.focus();
                }
            }
        });
    }
    </script>
</body>

</html>

<?php
if (file_exists('includes/footer.php')) {
    include 'includes/footer.php';
}
?>