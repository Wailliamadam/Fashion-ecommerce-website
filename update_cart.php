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

    // Get the posted data
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['product_id'] ?? null;
    $quantity = $data['quantity'] ?? 1;
    $clear_cart = $data['clear_cart'] ?? false;

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if ($clear_cart) {
        // Clear the entire cart
        $_SESSION['cart'] = [];
    } elseif ($product_id) {
        if ($quantity <= 0) {
            // Remove item from cart
            unset($_SESSION['cart'][$product_id]);
        } else {
            // Validate product exists and has sufficient stock
            $stmt = $db->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception('Product not found');
            }

            if ($quantity > $product['stock']) {
                throw new Exception('Not enough stock available');
            }

            // Update quantity
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
    }

    // Calculate total items in cart
    $cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated',
        'cart_count' => $cart_count
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'cart_count' => isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0
    ]);
}
?>