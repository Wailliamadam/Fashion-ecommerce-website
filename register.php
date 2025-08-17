<?php
// Database credentials
$host = 'localhost';
$dbname = 'fashion_db'; 
$username = 'root'; 
$password = ''; 

// Attempt to connect to MySQL database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

$error_message = '';
$success_message = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = trim($_POST['username']);
    $input_email = trim($_POST['email']);
    $input_password = $_POST['password'];

    // Validate inputs
    if (empty($input_username) || empty($input_email) || empty($input_password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Check if username or email already exists
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(1, $input_username, PDO::PARAM_STR);
            $stmt->bindParam(2, $input_email, PDO::PARAM_STR);
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $error_message = "This username or email is already taken.";
                } else {
                    // Hash the password securely
                    $hashed_password = password_hash($input_password, PASSWORD_DEFAULT);

                    // Insert the new user into the database
                    $sql_insert = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                    if ($stmt_insert = $pdo->prepare($sql_insert)) {
                        $stmt_insert->bindParam(1, $input_username, PDO::PARAM_STR);
                        $stmt_insert->bindParam(2, $input_email, PDO::PARAM_STR);
                        $stmt_insert->bindParam(3, $hashed_password, PDO::PARAM_STR);

                        if ($stmt_insert->execute()) {
                            $success_message = "Registration successful! You can now <a href='login.php'>log in</a>.";
                        } else {
                            $error_message = "Something went wrong. Please try again later.";
                        }
                    }
                    unset($stmt_insert);
                }
            } else {
                $error_message = "Oops! Something went wrong. Please try again.";
            }
            unset($stmt);
        }
    }
}
unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="/assets/css/Register.css">

</head>

<body>
    <div class="register-container">
        <h2>Create an Account</h2>
        <?php if (!empty($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
        <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form action="register.php" method="post">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required>
            </div>
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Choose a password" required>
            </div>
            <button type="submit" class="register-button">Register</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="login.php">Log in here</a>
        </div>
    </div>
</body>

</html>