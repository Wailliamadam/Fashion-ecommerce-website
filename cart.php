<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'fashion_db';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get cart items with full product details
    $cart_items = [];
    $total = 0;

    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        
        $stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($products as $product) {
            $item = $_SESSION['cart'][$product['id']];
            $discounted_price = $product['price'] * (1 - $product['discount'] / 100);
            $item_total = $discounted_price * $item['quantity'];
            
            $cart_items[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'discount' => $product['discount'],
                'discounted_price' => $discounted_price,
                'quantity' => $item['quantity'],
                'image' => $product['image'],
                'item_total' => $item_total
            ];
            
            $total += $item_total;
        }
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container py-5">
        <h1 class="mb-4">Your Shopping Cart</h1>

        <?php if (empty($cart_items)): ?>
        <div class="alert alert-info">
            Your cart is empty. <a href="shop.php" class="alert-link">Continue shopping</a>.
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($item['image']) ?>"
                                                    alt="<?= htmlspecialchars($item['name']) ?>"
                                                    class="img-thumbnail me-3"
                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                                <div>
                                                    <h5 class="mb-1"><?= htmlspecialchars($item['name']) ?></h5>
                                                    <?php if ($item['discount'] > 0): ?>
                                                    <span class="badge bg-danger"><?= $item['discount'] ?>% OFF</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($item['discount'] > 0): ?>
                                            <span class="text-danger fw-bold">MMK
                                                <?= number_format($item['discounted_price'], 2) ?></span>
                                            <br>
                                            <span class="text-decoration-line-through text-muted small">MMK
                                                <?= number_format($item['price'], 2) ?></span>
                                            <?php else: ?>
                                            <span class="fw-bold">MMK <?= number_format($item['price'], 2) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="input-group" style="width: 120px;">
                                                <button class="btn btn-outline-secondary update-quantity"
                                                    data-id="<?= $item['id'] ?>" data-action="decrease">-</button>
                                                <input type="text" class="form-control text-center quantity-input"
                                                    value="<?= $item['quantity'] ?>" data-id="<?= $item['id'] ?>">
                                                <button class="btn btn-outline-secondary update-quantity"
                                                    data-id="<?= $item['id'] ?>" data-action="increase">+</button>
                                            </div>
                                        </td>
                                        <td class="fw-bold">MMK <?= number_format($item['item_total'], 2) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger remove-item"
                                                data-id="<?= $item['id'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <a href="shop.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                    </a>
                    <button class="btn btn-outline-danger" id="clear-cart">
                        <i class="fas fa-trash me-2"></i> Clear Cart
                    </button>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>MMK <?= number_format($total, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>MMK 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax:</span>
                            <span>MMK 0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold">MMK <?= number_format($total, 2) ?></span>
                        </div>
                        <a href="checkout.php" class="btn btn-primary w-100 py-2">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity update buttons
        document.querySelectorAll('.update-quantity').forEach(btn => {
            btn.addEventListener('click', async function() {
                const productId = this.dataset.id;
                const action = this.dataset.action;
                const input = this.closest('.input-group').querySelector('.quantity-input');
                let quantity = parseInt(input.value);

                if (action === 'increase') {
                    quantity++;
                } else if (action === 'decrease' && quantity > 1) {
                    quantity--;
                }

                await updateCartItem(productId, quantity);
            });
        });

        // Quantity input changes
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', async function() {
                const productId = this.dataset.id;
                let quantity = parseInt(this.value) || 1;

                if (quantity < 1) {
                    quantity = 1;
                    this.value = 1;
                }

                await updateCartItem(productId, quantity);
            });
        });

        // Remove item buttons
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', async function() {
                const productId = this.dataset.id;
                await updateCartItem(productId, 0); // 0 quantity removes the item
            });
        });

        // Clear cart button
        document.getElementById('clear-cart').addEventListener('click', async function() {
            if (confirm('Are you sure you want to clear your cart?')) {
                try {
                    const response = await fetch('update_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            clear_cart: true
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        location.reload();
                    } else {
                        alert(result.message || 'Failed to clear cart');
                    }
                } catch (error) {
                    alert('Network error. Please try again.');
                }
            }
        });

        async function updateCartItem(productId, quantity) {
            try {
                const response = await fetch('update_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                });

                const result = await response.json();

                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message || 'Failed to update cart');
                }
            } catch (error) {
                alert('Network error. Please try again.');
            }
        }
    });
    </script>
</body>

</html>