<?php
// Always start the session at the beginning of the script
session_start();

// --- INSECURE - FOR DEMO ONLY ---
// In a real application, you would fetch user data from a database
// and use password_verify() to check a hashed password.
$correct_username = 'admin';
$correct_password = 'admin123'; // This is insecure; use hashed passwords in production
// --------------------------------

// Get the form data
$username = $_POST['username'];
$password = $_POST['password'];

// Check if the credentials are correct
if ($username === $correct_username && $password === $correct_password) {
    // Credentials are correct, so set session variables
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username;

    // Redirect to the protected dashboard page
    header('Location: dashboard.php');
    exit;
} else {
    // Credentials are incorrect, set an error message and redirect back to login
    $_SESSION['error'] = 'Invalid username or password.';
    header('Location: login.php');
    exit;
}
?>