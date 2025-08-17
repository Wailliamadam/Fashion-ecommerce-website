<?php
// Start session and include navbar at the top
session_start();

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



$success = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Validate and sanitize inputs
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// Validate inputs
if (empty($name)) {
$errors['name'] = 'Name is required';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
$errors['email'] = 'Valid email is required';
}
if (empty($message)) {
$errors['message'] = 'Message is required';
}

// If no errors, proceed with database insertion
if (empty($errors)) {
try {
$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, $message]);
$success = "Message sent successfully!";
} catch (PDOException $e) {
$errors['database'] = "Error sending message: " . $e->getMessage();
}
}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Fashion Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/contact.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Map Section -->
    <div class="map">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d111551.9926412813!2d-90.27317134641879!3d38.606612219170856!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x54eab584e432360b%3A0x1c3bb99243deb742!2sUnited%20States!5e0!3m2!1sen!2sbd!4v1597926938024!5m2!1sen!2sbd"
            height="500" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0">
        </iframe>
    </div>

    <!-- Contact Section -->
    <section class="contact spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="contact__text">
                        <div class="section-title">
                            <span>Information</span>
                            <h2>Contact Us</h2>
                            <p>As you might expect of a company that began as a high-end interiors contractor, we pay
                                strict attention.</p>
                        </div>
                        <ul>
                            <li>
                                <h4>Yangon</h4>
                                <p>195 E Parker Square Dr, Parker, CO 801 <br />+959886731871</p>
                            </li>
                            <li>
                                <h4>Mandalay</h4>
                                <p>109 Avenue Léon, 63 Clermont-Ferrand <br />+959457476693</p>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="contact__form">
                        <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <form action="contact.php" method="POST">
                            <div class="row">
                                <div class="col-lg-6">
                                    <input type="text" name="name" placeholder="Name"
                                        value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                                    <?php if (isset($errors['name'])): ?>
                                    <small class="text-danger"><?php echo $errors['name']; ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" name="email" placeholder="Email"
                                        value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                                    <?php if (isset($errors['email'])): ?>
                                    <small class="text-danger"><?php echo $errors['email']; ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-12">
                                    <textarea name="message"
                                        placeholder="Message"><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                                    <?php if (isset($errors['message'])): ?>
                                    <small class="text-danger"><?php echo $errors['message']; ?></small>
                                    <?php endif; ?>
                                    <button type="submit" class="site-btn">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>