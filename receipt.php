<?php
session_start();

if(!isset($_GET['order_id'])) {
    header('Location: orders.php');
    exit;
}

$order_id = (int) $_GET['order_id'];

$host = 'localhost';
$dbname = 'fashion_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$order) die("Order not found");

    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$order['user_id']]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("ERROR: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - #<?= $order_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
    /* Your existing CSS unchanged */
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .receipt-container {
        max-width: 800px;
        margin: 30px auto;
        background: white;
        padding: 30px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #eee;
        padding-bottom: 20px;
    }

    .receipt-title {
        color: #e83e8c;
        font-weight: 700;
    }

    .receipt-subtitle {
        color: #6c757d;
        font-size: 1rem;
    }

    .receipt-logo {
        max-width: 150px;
        margin-bottom: 15px;
    }

    .receipt-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }

    .receipt-info-box {
        flex: 1;
        padding: 15px;
    }

    .receipt-table th {
        background-color: #f1f1f1;
        padding: 12px;
        text-align: left;
    }

    .receipt-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }

    .receipt-totals {
        margin-left: auto;
        width: 300px;
        border-top: 2px solid #eee;
        padding-top: 20px;
    }

    .receipt-footer {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #eee;
        font-size: 0.9rem;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }

    .btn-custom {
        background: #e83e8c;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        font-weight: 600;
    }

    .btn-custom:hover {
        background: #d62d7a;
    }

    @media print {
        .action-buttons {
            display: none;
        }
    }
    </style>
</head>

<body>
    <div class="receipt-container" id="receiptContent">
        <div class="receipt-header">
            <img src="/assets/logo.png" alt="Company Logo" class="receipt-logo">
            <h2 class="receipt-title">Order Receipt</h2>
            <p class="receipt-subtitle">Thank you for your purchase!</p>
        </div>

        <div class="receipt-info">
            <div class="receipt-info-box">
                <h5>Order Information</h5>
                <p><strong>Order #:</strong> <?= $order_id ?></p>
                <p><strong>Date:</strong> <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
                <p><strong>Status:</strong> <span class="badge bg-success"><?= ucfirst($order['status']) ?></span></p>
            </div>
            <div class="receipt-info-box">
                <h5>Billing Information</h5>
                <p><strong>Name:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
            </div>
            <div class="receipt-info-box">
                <h5>Shipping Information</h5>
                <p><?= htmlspecialchars($order['address']) ?></p>
                <p><?= htmlspecialchars($order['city'] . ', ' . $order['postcode']) ?></p>
                <p><?= htmlspecialchars($order['country']) ?></p>
            </div>
        </div>

        <table class="receipt-table w-100">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td>MMK <?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>MMK <?= number_format($item['total'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="receipt-totals">
            <div><strong>Subtotal:</strong> MMK <?= number_format($order['subtotal'], 2) ?></div>
            <div><strong>Shipping:</strong> MMK <?= number_format($order['shipping'], 2) ?></div>
            <div><strong>Tax:</strong> MMK <?= number_format($order['tax'], 2) ?></div>
            <div style="font-size: 1.1rem; margin-top:10px; border-top:1px solid #eee; padding-top:5px;">
                <strong>Total:</strong> <span style="color:#e83e8c;">MMK <?= number_format($order['total'], 2) ?></span>
            </div>
        </div>

        <div class="receipt-footer">
            <p>Payment Method: <?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></p>
            <p>Contact us: support@yourstore.com</p>
            <p>Thank you for shopping with us!</p>
        </div>
    </div>

    <div class="action-buttons">
        <button class="btn-custom" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
        <button class="btn-custom" onclick="downloadTXT()"><i class="fas fa-file-alt"></i> TXT</button>
        <button class="btn-custom" onclick="downloadPDF()"><i class="fas fa-file-pdf"></i> PDF</button>
    </div>

    <script>
    function downloadTXT() {
        const text = document.getElementById("receiptContent").innerText;
        const blob = new Blob([text], {
            type: "text/plain"
        });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "receipt_order_<?= $order_id ?>.txt";
        link.click();
    }

    function downloadPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF();
        doc.setFontSize(12);
        doc.text(document.getElementById("receiptContent").innerText, 10, 10);
        doc.save("receipt_order_<?= $order_id ?>.pdf");
    }
    </script>
</body>

</html>