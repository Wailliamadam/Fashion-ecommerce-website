<?php session_start(); 
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
// $wishlist_count = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Fashion Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/search.css">

    <style>
    .dropdown-toggle::after {
        display: none;
    }

    .profile-avatar-img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    .dropdown-menu {
        min-width: 250px;
        z-index: 10000;
    }

    .dropdown.show .dropdown-menu {
        display: block;
    }

    .profile-header {
        padding: 0.5rem 1rem;
    }

    .profile-header-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
    }

    .profile-info {
        display: inline-block;
        vertical-align: middle;
    }

    .profile-name {
        font-weight: 600;
        margin-bottom: 2px;
    }

    .profile-email {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .profile-icon {
        width: 20px;
        margin-right: 10px;
        text-align: center;
    }

    .logout-item {
        color: #dc3545;
    }

    .logout-item:hover {
        color: #fff;
        background-color: #dc3545;
    }

    /* Cart Icon Styles */
    .cart-icon {
        position: relative;
        display: inline-block;
        margin-left: 15px;
    }

    .cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #ff4d4d;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 12px;
        font-weight: bold;
    }

    .cart-icon img {
        width: 24px;
        height: 24px;
        transition: transform 0.3s ease;
    }

    .cart-icon:hover img {
        transform: scale(1.1);
    }

    .cart-badge {
        position: absolute;
        top: 0;
        right: -8px;
        font-size: 0.75rem;
    }

    .position-relative {
        position: relative;
        display: inline-block;
    }

    .wishlist-badge {
        position: absolute;
        top: -5px;
        right: -10px;
        font-size: 0.75rem;
    }

    /* Animation for cart update */
    @keyframes cartPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .cart-pulse {
        animation: cartPulse 0.5s ease;
    }

    /* Ensure dropdown appears above other elements */
    .header__nav__option {
        position: relative;
        z-index: 1000;
    }
    </style>
</head>

<body>
    <header class="header">
        <div class="header__top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-7">
                        <div class="header__top__left">
                            <p>Free shipping, 30-day return or refund guarantee.</p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-5">
                        <div class="header__top__right">
                            <div class="header__top__links">
                                <a href="/faqs.php">FAQs</a>
                            </div>
                            <div class="header__top__hover">
                                <span>MMK </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-3">
                    <div class="header__logo">
                        <a href="/index.php"><img src="/assets/logo.png" alt="Fashion Store Logo"></a>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <nav class="header__menu mobile-menu">
                        <ul>
                            <li class="active"><a href="/index.php">Home</a></li>
                            <li><a href="/shop.php">Shop</a></li>
                            <li>
                                <a href="#">Pages</a>
                                <ul class="dropdown">
                                    <li><a href="/about-us.php">About Us</a></li>
                                    <li><a href="/shop-details.php">Shop Details</a></li>
                                    <li><a href="/checkout.php">Check Out</a></li>
                                    <li><a href="/blog-details.php">Blog Details</a></li>
                                </ul>
                            </li>
                            <li><a href="/blog.php">Blog</a></li>
                            <li><a href="/contact.php">Contacts</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-3 col-md-3">
                    <div class="header__nav__option">
                        <a href="/search.php" class="search-switch">
                            <img src="/assets/icon/search.png" alt="Search">
                        </a>
                        <div class="search-model">
                            <div class="search-close-switch">×</div>
                            <form class="search-model-form" action="/search.php" method="GET">
                                <input type="text" id="search-input" name="query" placeholder="Search here.....">
                            </form>
                        </div>

                        <a href="/cart.php" class="cart-icon" id="cartIcon">
                            <img src="/assets/icon/cart.png" alt="Cart">
                            <?php if ($cart_count > 0): ?>
                            <span class="cart-count"><?= $cart_count ?></span>
                            <?php endif; ?>
                        </a>

                        <div class="dropdown d-inline-block ms-3">
                            <a class="dropdown-toggle d-flex align-items-center nav-link p-0" href="#"
                                id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if (isset($_SESSION['username'])): ?>
                                <img src="/assets/user-avatar.png" alt="User Avatar" class="profile-avatar-img">
                                <?php else: ?>
                                <i class="fas fa-user-circle fa-lg"></i>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end profile-menu" aria-labelledby="profileDropdown">
                                <?php if (isset($_SESSION['username'])): ?>
                                <li>
                                    <div class="d-flex align-items-center px-3 py-2">
                                        <img src="/assets/user-avatar.png" alt="User Avatar"
                                            class="profile-header-avatar">
                                        <div class="profile-info">
                                            <div class="profile-name">
                                                <?php echo htmlspecialchars($_SESSION['username']); ?></div>
                                            <div class="profile-email">
                                                <?php echo htmlspecialchars($_SESSION['email']); ?></div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="/purchase_history.php"><i class="icon"></i>purchase history</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item logout-item" href="/logout.php"><i
                                            class="fas fa-power-off profile-icon"></i> Logout</a></li>
                                <?php else: ?>
                                <li><a class="dropdown-item" href="/login.php"><i
                                            class="fas fa-sign-in-alt profile-icon"></i> Login</a></li>
                                <li><a class="dropdown-item" href="/register.php"><i
                                            class="fas fa-user-plus profile-icon"></i> Register</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="canvas__open"><i class="fa fa-bars"></i></div>
        </div>
    </header>

    <!-- JavaScript at the bottom for better performance -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Add this right after the Bootstrap JS bundle script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/search.js"></script>
    <script src="/js/theme.js"></script>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            var dropdowns = document.querySelectorAll('.dropdown-menu.show');
            dropdowns.forEach(function(dropdown) {
                dropdown.classList.remove('show');
            });
        }
    });

    // Function to update cart count
    function updateCartCount(count) {
        const cartIcon = document.getElementById('cartIcon');
        let cartBadge = cartIcon.querySelector('.cart-count');

        if (count > 0) {
            if (!cartBadge) {
                cartBadge = document.createElement('span');
                cartBadge.className = 'cart-count';
                cartIcon.appendChild(cartBadge);
            }
            cartBadge.textContent = count;
        } else if (cartBadge) {
            cartBadge.remove();
        }

        // Add animation
        cartIcon.classList.add('cart-pulse');
        setTimeout(() => {
            cartIcon.classList.remove('cart-pulse');
        }, 500);
    }

    // Initialize cart count
    updateCartCount(<?= $cart_count ?>);
});
</script>
</body>
</html>