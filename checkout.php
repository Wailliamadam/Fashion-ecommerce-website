<?php
// Start session at the very beginning
session_start();

// // Enable error reporting for debugging. You should turn this off on a live site.
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Check if user has items in cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    // If cart is empty, redirect the user back to the cart page.
    header('Location: cart.php');
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
} catch (PDOException $e) {
    // If the database connection fails, return a JSON error response.
    header('Content-Type: application/json');
    http_response_code(500); // 500 Internal Server Error
    echo json_encode(['success' => false, 'message' => "ERROR: Could not connect to the database. " . $e->getMessage()]);
    exit;
}

// Get cart items from session with proper type handling
$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $placeholders = rtrim(str_repeat('?,', count($_SESSION['cart'])), ',');
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_keys($_SESSION['cart']));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $cart_item = $_SESSION['cart'][$product['id']];
        $quantity = is_array($cart_item) ? $cart_item['quantity'] : $cart_item;
        $price = is_numeric($product['price']) ? (float)$product['price'] : 0;
        $quantity = is_numeric($quantity) ? (int)$quantity : 0;
        
        $item_total = $price * $quantity;
        $cart_items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $price,
            'quantity' => $quantity,
            'total' => $item_total,
            'image' => $product['image'] ?? 'default-product.jpg'
        ];
        $subtotal += $item_total;
    }
}

// Calculate shipping and tax
$shipping = 0; // Free shipping
$tax = $subtotal * 0.05; // 5% tax
$total = $subtotal + $shipping + $tax;

// Process checkout form via POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token verification (recommended)
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Content-Type: application/json');
        http_response_code(403); // 403 Forbidden
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
        exit;
    }

    // Validate and sanitize inputs
    $errors = [];
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
    $postcode = filter_input(INPUT_POST, 'postcode', FILTER_SANITIZE_STRING);
    $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);

    // Validate required fields
    if (empty($first_name)) $errors['first_name'] = 'First name is required';
    if (empty($last_name)) $errors['last_name'] = 'Last name is required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required';
    if (empty($phone)) $errors['phone'] = 'Phone number is required';
    if (empty($address)) $errors['address'] = 'Address is required';
    if (empty($city)) $errors['city'] = 'City is required';
    if (empty($postcode)) $errors['postcode'] = 'Postcode is required';
    if (empty($country)) $errors['country'] = 'Country is required';
    if (empty($payment_method)) $errors['payment_method'] = 'Payment method is required';

    if (!empty($errors)) {
        // Return a JSON response with validation errors
        header('Content-Type: application/json');
        http_response_code(400); // 400 Bad Request
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    } else {
        try {
            // Begin transaction
            $pdo->beginTransaction();

            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, first_name, last_name, email, phone, address, city, postcode, country, notes, payment_method, subtotal, shipping, tax, total, status)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([
                $_SESSION['id'] ?? null,
                $first_name,
                $last_name,
                $email,
                $phone,
                $address,
                $city,
                $postcode,
                $country,
                $notes,
                $payment_method,
                $subtotal,
                $shipping,
                $tax,
                $total
            ]);

            $order_id = $pdo->lastInsertId();

            // Insert order items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, total)
                                 VALUES (?, ?, ?, ?, ?, ?)");

            foreach ($cart_items as $item) {
                $stmt->execute([
                    $order_id,
                    $item['id'],
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $item['total']
                ]);

                // Update product stock (optional)
                $update_stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $update_stmt->execute([$item['quantity'], $item['id']]);
            }

            // Commit transaction
            $pdo->commit();

            // Clear cart
            unset($_SESSION['cart']);

            // Return a JSON success response instead of a redirect
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'order_id' => $order_id]);
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            // Return a JSON error response if a database error occurs
            header('Content-Type: application/json');
            http_response_code(500); // 500 Internal Server Error
            echo json_encode(['success' => false, 'message' => "Error processing your order: " . $e->getMessage()]);
            exit;
        }
    }
}

// Generate a CSRF token for the form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Fashion Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --primary-color: #140de7ff;
        --secondary-color: #6c757d;
        --dark-color: #343a40;
        --light-color: #f8f9fa;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f5f5;
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 0;
        font-size: 0.9rem;
    }

    .breadcrumb__text h4 {
        font-weight: 700;
        color: var(--dark-color);
    }

    .breadcrumb__links a {
        color: var(--secondary-color);
        text-decoration: none;
        transition: color 0.3s;
    }

    .breadcrumb-item a:hover {
        color: #e83e8c;
        text-decoration: none;
    }

    .breadcrumb__links a:hover {
        color: var(--primary-color);
    }

    .checkout__form {
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        padding: 30px;
        margin-bottom: 50px;
    }

    .checkout__title {
        font-weight: 700;
        margin-bottom: 30px;
        color: var(--dark-color);
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 10px;
    }

    .checkout__input {
        margin-bottom: 20px;
    }

    .checkout__input p {
        font-weight: 600;
        margin-bottom: 8px;
    }

    .checkout__input p span {
        color: var(--primary-color);
    }

    .checkout__input input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        transition: border-color 0.3s;
    }

    .checkout__input input:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 0.25rem rgba(232, 62, 140, 0.25);
    }

    .checkout__input__add {
        margin-bottom: 15px;
    }

    .checkout__order {
        background: var(--light-color);
        padding: 30px;
        border-radius: 10px;
        border: 1px solid #eee;
    }

    .order__title {
        font-weight: 700;
        margin-bottom: 20px;
        color: var(--dark-color);
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 10px;
    }

    .checkout__order__products {
        font-weight: 600;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .checkout__order__products span {
        float: right;
    }

    .checkout__total__products li {
        list-style: none;
        padding: 8px 0;
        border-bottom: 1px dashed #ddd;
    }

    .checkout__total__products li span {
        float: right;
    }

    .checkout__total__all {
        margin: 20px 0;
        border-top: 1px solid #ddd;
        padding-top: 15px;
    }

    .checkout__total__all li {
        list-style: none;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .checkout__total__all li span {
        float: right;
        color: var(--primary-color);
    }

    .checkout__input__radio {
        margin-bottom: 25px;
    }

    .checkout__input__radio p {
        font-weight: 600;
        margin-bottom: 15px;
    }

    .checkout__input__radio p span {
        color: var(--primary-color);
    }

    .form-check {
        margin-bottom: 10px;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .site-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 5px;
        width: 100%;
        transition: all 0.3s;
    }

    .site-btn:hover {
        background: #d62d7a;
        transform: translateY(-2px);
    }

    .coupon__code {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 30px;
    }

    .coupon__code a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 5px;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    @media (max-width: 767px) {
        .checkout__form {
            padding: 20px;
        }

        .checkout__order {
            margin-top: 30px;
        }
    }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <section class="checkout spad">
        <div class="container">
            <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="checkout__form">
                <form action="checkout.php" method="POST" id="checkoutForm">
                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                    <div class="row">
                        <div class="col-lg-8 col-md-6">
                            <div class="coupon__code">
                                <i class="fas fa-tag me-2"></i> Have a coupon? <a href="#" data-bs-toggle="modal"
                                    data-bs-target="#couponModal">Click here</a> to enter your code
                            </div>
                            <h6 class="checkout__title">Billing Details</h6>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>First Name<span>*</span></p>
                                        <input type="text" name="first_name"
                                            value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                                        <?php if (isset($errors['first_name'])): ?>
                                        <div class="error-message"><?= $errors['first_name'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>Last Name<span>*</span></p>
                                        <input type="text" name="last_name"
                                            value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                                        <?php if (isset($errors['last_name'])): ?>
                                        <div class="error-message"><?= $errors['last_name'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout__input">
                                <p>Country<span>*</span></p>
                                <select class="form-select" name="country" required>
                                    <option value="">Select Country</option>
                                    <option value="Myanmar"
                                        <?= (isset($_POST['country']) && $_POST['country'] == 'Myanmar') ? 'selected' : '' ?>>
                                        Myanmar</option>
                                    <option value="Thailand"
                                        <?= (isset($_POST['country']) && $_POST['country'] == 'Thailand') ? 'selected' : '' ?>>
                                        Thailand</option>
                                    <option value="Singapore"
                                        <?= (isset($_POST['country']) && $_POST['country'] == 'Singapore') ? 'selected' : '' ?>>
                                        Singapore</option>
                                </select>
                                <?php if (isset($errors['country'])): ?>
                                <div class="error-message"><?= $errors['country'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="checkout__input">
                                <p>Address<span>*</span></p>
                                <input type="text" name="address" placeholder="Street Address"
                                    value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" required>
                                <?php if (isset($errors['address'])): ?>
                                <div class="error-message"><?= $errors['address'] ?></div>
                                <?php endif; ?>
                                <br>
                                <br>
                                <input type="text" name="address2" placeholder="Apartment, suite, unit etc (optional)"
                                    value="<?= htmlspecialchars($_POST['address2'] ?? '') ?>">
                            </div>
                            <div class="checkout__input">
                                <p>Town/City<span>*</span></p>
                                <input type="text" name="city" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>"
                                    required>
                                <?php if (isset($errors['city'])): ?>
                                <div class="error-message"><?= $errors['city'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="checkout__input">
                                <p>Postcode / ZIP<span>*</span></p>
                                <input type="text" name="postcode"
                                    value="<?= htmlspecialchars($_POST['postcode'] ?? '') ?>" required>
                                <?php if (isset($errors['postcode'])): ?>
                                <div class="error-message"><?= $errors['postcode'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>Phone<span>*</span></p>
                                        <input type="tel" name="phone"
                                            value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                                        <?php if (isset($errors['phone'])): ?>
                                        <div class="error-message"><?= $errors['phone'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>Email<span>*</span></p>
                                        <input type="email" name="email"
                                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                        <?php if (isset($errors['email'])): ?>
                                        <div class="error-message"><?= $errors['email'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout__input">
                                <p>Order notes</p>
                                <textarea name="notes" class="form-control"
                                    placeholder="Notes about your order, e.g. special notes for delivery."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="checkout__order">
                                <h4 class="order__title">Your Order</h4>
                                <div class="checkout__order__products">Product <span>Total</span></div>
                                <ul class="checkout__total__products">
                                    <?php foreach ($cart_items as $item): ?>
                                    <li>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><?= $item['quantity'] ?> ×
                                                <?= htmlspecialchars($item['name']) ?></span>
                                            <span>MMK <?= number_format($item['total'], 2) ?></span>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <ul class="checkout__total__all">
                                    <li>Subtotal <span>MMK <?= number_format($subtotal, 2) ?></span></li>
                                    <li>Shipping <span>MMK <?= number_format($shipping, 2) ?></span></li>
                                    <li>Tax <span>MMK <?= number_format($tax, 2) ?></span></li>
                                    <li class="fw-bold">Total <span>MMK <?= number_format($total, 2) ?></span></li>
                                </ul>
                                <div class="checkout__input__radio">
                                    <p>Payment Method<span>*</span></p>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            id="credit_card" value="credit_card"
                                            <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'credit_card') || !isset($_POST['payment_method']) ? 'checked' : '' ?>
                                            required>
                                        <label class="form-check-label" for="credit_card">
                                            <i class="fab fa-cc-visa me-2"></i>Credit Card
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="kpay"
                                            value="kpay"
                                            <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'kpay') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="kpay">
                                            <i class="fas fa-mobile-alt me-2"></i>KPay
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            id="wave_money" value="wave_money"
                                            <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'wave_money') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="wave_money">
                                            <i class="fas fa-money-bill-wave me-2"></i>WaveMoney
                                        </label>
                                    </div>
                                    <?php if (isset($errors['payment_method'])): ?>
                                    <div class="error-message"><?= $errors['payment_method'] ?></div>
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="site-btn">PLACE ORDER</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponModalLabel">Apply Coupon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="couponCode" class="form-label">Coupon Code</label>
                        <input type="text" class="form-control" id="couponCode" placeholder="Enter coupon code">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Apply Coupon</button>
                </div>
            </div>
        </div>
    </div>

    <?php if (file_exists('includes/footer.php')) include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);

        // Clear previous error messages
        const errorMessages = form.querySelectorAll('.error-message');
        errorMessages.forEach(el => el.remove());
        const invalidFields = form.querySelectorAll('.is-invalid');
        invalidFields.forEach(el => el.classList.remove('is-invalid'));

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        fetch('checkout.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // If the response is not a redirect, it's a JSON response.
                if (response.headers.get('content-type')?.includes('application/json')) {
                    return response.json().then(data => ({
                        status: response.status,
                        data
                    }));
                }
                // If it's a redirect, we'll follow it
                if (response.redirected) {
                    window.location.href = response.url;
                }
                // Return an error if we can't parse the response
                throw new Error('Unexpected server response.');
            })
            .then(({
                status,
                data
            }) => {
                if (status === 200 && data.success) {
                    // Success case: redirect to the success page with the order ID
                    window.location.href = 'order_success.php?id=' + data.order_id;
                } else if (status === 400 && data.errors) {
                    // Validation errors case
                    for (const field in data.errors) {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const errorMessage = document.createElement('div');
                            errorMessage.classList.add('error-message');
                            errorMessage.textContent = data.errors[field];
                            input.closest('.checkout__input').appendChild(errorMessage);
                        }
                    }
                    alert('Please correct the form errors.');
                } else if (data.message) {
                    // Other server-side errors
                    alert(data.message);
                } else {
                    alert('An unknown error occurred.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Server error. Please try again later.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'PLACE ORDER';
            });
    });
    </script>

</body>

</html>