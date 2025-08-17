<?php
session_start();

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

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted email and password
    $input_email = trim($_POST['email']);
    $input_password = trim($_POST['password']);

    // SQL query to fetch user by email only
    $sql = "SELECT id, username, email, password FROM users WHERE email = :email";
    
    if ($stmt = $pdo->prepare($sql)) {
        // Bind parameters
        $stmt->bindParam(':email', $input_email, PDO::PARAM_STR);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Check if user exists
            if ($stmt->rowCount() == 1) {
                if ($row = $stmt->fetch()) {
                    $id = $row['id'];
                    $username = $row['username'];
                    $email = $row['email'];
                    $stored_hashed_password = $row['password'];

                    // Verify password
                    if (password_verify($input_password, $stored_hashed_password)) {
                        // Password is correct, start session
                        session_regenerate_id(true);
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        $_SESSION["email"] = $email;

                        // Redirect to index
                        header("Location: index.php");
                        exit;
                    } else {
                        // Invalid password
                        $login_err = "Invalid email or password";
                    }
                }
            } else {
                // User not found
                $login_err = "Invalid email or password";
            }
        } else {
            $login_err = "Oops! Something went wrong. Please try again later.";
        }
        unset($stmt);
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .login-container {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
    }

    .login-container h2 {
        text-align: center;
        margin-bottom: 1rem;
        color: #333;
    }

    .login-container p {
        text-align: center;
        margin-bottom: 2rem;
        color: #666;
    }

    .form-control {
        margin-bottom: 1rem;
    }

    .btn-primary {
        width: 100%;
        padding: 0.5rem;
        margin-top: 1rem;
    }

    .register-link {
        text-align: center;
        margin-top: 1.5rem;
    }

    .invalid-feedback {
        color: #dc3545;
        margin-top: -0.5rem;
        margin-bottom: 1rem;
    }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Welcome Back</h2>
        <p>Please log in to your account</p>

        <?php 
        if (!empty($login_err)) {
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary">Log In</button>
            <div class="register-link mt-3">
                Don't have an account? <a href="/register.php">Sign up here</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>