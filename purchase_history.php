<?php 
session_start();
// Database connection
$host = 'localhost';
$dbname = 'fashion_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch all orders with customer info
try {
    $stmt = $conn->prepare("
        SELECT 
            o.id, 
            o.created_at, 
            o.total, 
            o.status,
            o.first_name,
            o.last_name,
            o.email,
            o.phone,
            o.payment_method,
            o.address,
            o.city,
            o.postcode,
            o.country,
            COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching orders: " . $e->getMessage();
}

// Fetch order details when selected
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    try {
        // Get order items - using p.name instead of p.product_name
        $stmt = $conn->prepare("
            SELECT 
                oi.*, 
                p.name,  /* Changed from product_name to name */
                p.image,
                p.price as unit_price
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error fetching order items: " . $e->getMessage();
    }
}

// Handle cancel order action
if (isset($_POST['cancel_order'])) {
    $order_id = $_POST['order_id'];
    try {
        $stmt = $conn->prepare("
            UPDATE orders SET status = 'cancelled' 
            WHERE id = :order_id AND status IN ('pending', 'processing')
        ");
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $success = "Order #$order_id has been cancelled successfully.";
            // Refresh the page to show updated status
            header("Location: purchase-history.php?order_id=$order_id");
            exit();
        }
    } catch (PDOException $e) {
        $error = "Error cancelling order: " . $e->getMessage();
    }
}
?>

<?php if (file_exists('includes/navbar.php')) include 'includes/navbar.php'; ?>


    <br>
    <br>
<link rel="stylesheet" href="/assets/css/purchase_history.css">

<div class="container purchase-history-container">
    <h2 class="page-title"><i class="fas fa-history me-2"></i> Purchase History</h2>
    
    <br>
    <br>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (empty($orders)): ?>
        <div class="no-orders">
            <i class="fas fa-box-open"></i>
            <h4>No orders found</h4>
            <a href="shop.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header" onclick="toggleOrderDetails(this)">
                        <div>
                            <h5 class="mb-1">Order #<?php echo $order['id']; ?></h5>
                            <small class="text-muted">Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?></small>
                        </div>
                        <div class="text-end">
                            <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                            <div class="order-total mt-1">$<?php echo number_format($order['total'], 2); ?></div>
                        </div>
                    </div>
                    
                    <div class="order-body">
                        <!-- Customer Information Section -->
                        <div class="customer-info">
                            <h5><i class="fas fa-user-circle me-2"></i>Customer Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> <?php echo $order['first_name'] . ' ' . $order['last_name']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
                                    <p><strong>Payment:</strong> <?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></p>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p><strong>Address:</strong> 
                                    <?php echo $order['address'] . ', ' . $order['city'] . ', ' . $order['postcode'] . ', ' . $order['country']; ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Order Items Section -->
                        <h6 class="mt-4 mb-3">Order Items (<?php echo $order['item_count']; ?>)</h6>
                        
                        <?php if (isset($_GET['order_id']) && $_GET['order_id'] == $order['id'] && isset($order_items)): ?>
                            <?php foreach ($order_items as $item): ?>
                                <div class="order-item">
                                    <img src="/assets/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="order-item-img">
                                    <div class="order-item-details">
                                        <h6 class="order-item-title"><?php echo $item['name']; ?></h6>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="text-muted">Qty: <?php echo $item['quantity']; ?></span>
                                                <?php if (!empty($item['size'])): ?>
                                                    <span class="mx-2">|</span>
                                                    <span class="text-muted">Size: <?php echo $item['size']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="order-item-price">
                                                $<?php echo number_format($item['price'], 2); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-3">
                                <a href="?order_id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i> View All Items
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Order Actions -->
                        <div class="d-flex justify-content-end mt-3">
                            <a href="invoice.php?order_id=<?php echo $order['id']; ?>" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-file-invoice me-1"></i> Invoice
                            </a>
                            <?php if ($order['status'] == 'pending' || $order['status'] == 'processing'): ?>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" name="cancel_order" class="btn btn-outline-danger" 
                                        onclick="return confirm('Are you sure you want to cancel this order?')">
                                        <i class="fas fa-times-circle me-1"></i> Cancel Order
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>


<script>
function toggleOrderDetails(element) {
    const orderBody = element.nextElementSibling;
    orderBody.classList.toggle('show');
    
    // Update URL without reload when opening
    if (orderBody.classList.contains('show')) {
        const orderId = element.querySelector('.order-number').textContent.replace('Order #', '');
        history.pushState(null, null, `?order_id=${orderId}`);
    }
}

// Initialize open orders based on URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('order_id');
    
    if (orderId) {
        const orderHeaders = document.querySelectorAll('.order-header');
        orderHeaders.forEach(header => {
            if (header.querySelector('.order-number').textContent.includes(orderId)) {
                header.nextElementSibling.classList.add('show');
            }
        });
    }
});
</script>

<br>
<br>
<br>
<br>
<br>

<?php if (file_exists('includes/footer.php')) include 'includes/footer.php'; ?>