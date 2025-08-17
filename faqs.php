<?php
$host = 'localhost';      
$dbname = 'fashion_db'; 
$username = 'root';       
$password = '';          

try {
    // 1. Establish Database Connection
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 2. Create FAQ Table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS faqs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category VARCHAR(50) NOT NULL,
        question VARCHAR(255) NOT NULL,
        answer TEXT NOT NULL
    )");
    
    // 3. Insert Sample Data if table is empty
    $count = $db->query("SELECT COUNT(*) FROM faqs")->fetchColumn();
    if ($count == 0) {
        $sampleData = [
            ['orders', 'How long does shipping take?', 'Typically 3-5 business days'],
            ['orders', 'Can I modify my order?', 'You can modify within 1 hour of placing'],
            ['delivery', 'Do you offer express shipping?', 'Yes, 2-day express available'],
            ['payments', 'What payment methods do you accept?', 'We accept Visa, Mastercard, PayPal'],
            ['returns', 'What is your return policy?', '30-day free returns']
        ];
        
        $stmt = $db->prepare("INSERT INTO faqs (category, question, answer) VALUES (?, ?, ?)");
        foreach ($sampleData as $data) {
            $stmt->execute($data);
        }
    }
    
    // 4. Get FAQs from database
    $stmt = $db->query("SELECT * FROM faqs ORDER BY category, id");
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 5. Organize by category
    $categories = [];
    foreach ($faqs as $faq) {
        $categories[$faq['category']][] = $faq;
    }
    
} catch (PDOException $e) {
    die("<div style='padding:20px;background:#ffebee;border:1px solid #f44336;border-radius:5px;'>
        <h2 style='color:#d32f2f;'>Database Connection Error</h2>
        <p><strong>Error:</strong> {$e->getMessage()}</p>
        <h3>Troubleshooting:</h3>
        <ol>
            <li>Make sure MySQL server is running</li>
            <li>Verify username/password in the code</li>
            <li>Check if database '{$dbname}' exists</li>
            <li>Try using '127.0.0.1' instead of 'localhost'</li>
        </ol>
        </div>");
}
?>

<?php if (file_exists('includes/navbar.php')) include 'includes/navbar.php'; ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Male Fashion - FAQs</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/faq.css">
    <script src="/assets/js/faq.js"></script>
</head>

<body>
    <div class="faq-container">
        <header class="faq-header">
            <h1>Male Fashion</h1>
            <h2>Frequently Asked Questions</h2>
        </header>

        <div class="faq-search">
            <input type="text" placeholder="Search questions..." class="search-input">
            <button class="search-btn"><i class="fas fa-search"></i></button>
        </div>

        <div class="faq-categories">
            <div class="category active" data-category="all">All Questions</div>
            <div class="category" data-category="orders">Orders</div>
            <div class="category" data-category="delivery">Delivery</div>
            <div class="category" data-category="payments">Payments</div>
            <div class="category" data-category="returns">Returns</div>
        </div>

        <div class="faq-items">
            <?php foreach ($categories as $category => $items): ?>
            <div class="faq-section" data-category="<?= strtolower($category) ?>">
                <h3><?= htmlspecialchars(ucfirst($category)) ?></h3>
                <?php foreach ($items as $item): ?>
                <div class="faq-item">
                    <div class="faq-question">
                        <h4><?= htmlspecialchars($item['question']) ?></h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars($item['answer']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="support-section">
            <p>Still have questions? Our style consultants are here to help.</p>
            <div class="support-options">
                <a href="#" class="support-option"><i class="fas fa-comment-alt"></i> Live Chat</a>
                <a href="#" class="support-option"><i class="fas fa-phone"></i> Call Us</a>
                <a href="#" class="support-option"><i class="fas fa-envelope"></i> Email Us</a>
            </div>
        </div>
    </div>


</body>

</html>

<?php if (file_exists('includes/footer.php')) include 'includes/footer.php'; ?>