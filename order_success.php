<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'fashion_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect to the database. " . $e->getMessage());
}

// Get order ID from the URL and validate it
$order_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$order_id) {
    // If no valid ID is provided, redirect to the home page or an error page.
    header('Location: index.php');
    exit;
}

// Fetch order details
$sql_order = "SELECT * FROM orders WHERE id = ?";
$stmt_order = $pdo->prepare($sql_order);
$stmt_order->execute([$order_id]);
$order = $stmt_order->fetch(PDO::FETCH_ASSOC);

// If the order doesn't exist, redirect
if (!$order) {
    header('Location: index.php');
    exit;
}

// Fetch order items
$sql_items = "SELECT * FROM order_items WHERE order_id = ?";
$stmt_items = $pdo->prepare($sql_items);
$stmt_items->execute([$order_id]);
$order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= htmlspecialchars($order['id']) ?> - Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .receipt-container {
        max-width: 800px;
        margin: 50px auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 30px;
    }

    .receipt-header {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }

    .receipt-header h1 {
        color: #212529;
        font-weight: 700;
    }

    .receipt-header p {
        color: #6c757d;
    }

    .address-box {
        background-color: #f1f3f5;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .item-table th,
    .item-table td {
        vertical-align: middle;
    }

    .item-table th {
        border-top: 0;
        border-bottom: 2px solid #dee2e6;
    }

    .item-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .totals-summary {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
    }

    .totals-summary h5 {
        font-weight: 600;
    }

    .totals-summary .list-group-item {
        border: 0;
        background-color: transparent;
        padding: 5px 0;
    }

    .print-btn-container {
        text-align: center;
        margin-top: 30px;
    }

    @media print {
        body {
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
            background-color: #fff;
        }

        .receipt-container {
            box-shadow: none;
            margin: 0;
            padding: 0;
        }

        .print-btn-container,
        .navbar {
            display: none;
        }
    }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="receipt-container">
        <div class="receipt-header text-center">
            <h1>Thank You for Your Order!</h1>
            <p class="lead">Your order has been placed successfully.</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="address-box">
                    <strong>Order Details</strong><br>
                    Order Number: <strong>#<?= htmlspecialchars($order['id']) ?></strong><br>
                    Date: <?= htmlspecialchars(date('F j, Y', strtotime($order['created_at']))) ?><br>
                    Payment Method: <?= htmlspecialchars(ucwords(str_replace('_', ' ', $order['payment_method']))) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="address-box">
                    <strong>Shipping Address</strong><br>
                    <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?><br>
                    <?= htmlspecialchars($order['address']) ?><br>
                    <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['postcode']) ?><br>
                    <?= htmlspecialchars($order['country']) ?><br>
                    <?= htmlspecialchars($order['email']) ?>
                </div>
            </div>
        </div>

        <h4 class="mt-4 mb-3">Order Items</h4>
        <div class="table-responsive">
            <table class="table item-table">
                <thead>
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col" class="text-center">Quantity</th>
                        <th scope="col" class="text-end">Price</th>
                        <th scope="col" class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($item['quantity']) ?></td>
                        <td class="text-end">MMK <?= number_format($item['price'], 2) ?></td>
                        <td class="text-end">MMK <?= number_format($item['total'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end mt-4">
            <div class="col-md-6">
                <div class="totals-summary">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Subtotal</span>
                            <strong>MMK <?= number_format($order['subtotal'], 2) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Shipping</span>
                            <span>MMK <?= number_format($order['shipping'], 2) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Tax (5%)</span>
                            <span>MMK <?= number_format($order['tax'], 2) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                            <h5>Grand Total</h5>
                            <h5>MMK <?= number_format($order['total'], 2) ?></h5>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="print-btn-container">
            <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-2"></i> Print
                Receipt</button>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-home me-2"></i> Continue Shopping</a>
        </div>
    </div>

    <?php if (file_exists('includes/footer.php')) include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>