<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: _login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');  // Optional phone field

    if (empty($full_name) || empty($username) || empty($password)) {
        // Handle error - required fields missing
        echo "Full Name, Username and Password are required.";
        exit();
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert the new user
    $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password, phone, created_at) VALUES (?, ?, ?, ?, NOW())");

    try {
        $stmt->execute([$full_name, $username, $hashed_password, $phone]);
        // Redirect back to admin dashboard after successful insertion
        header("Location: admin_dashboard.php");
        exit();
    } catch (PDOException $e) {
        // Handle duplicate username or other errors
        if ($e->getCode() == 23000) { // Integrity constraint violation (like duplicate username)
            echo "Username already exists. Please choose another username.";
        } else {
            echo "Error: " . $e->getMessage();
        }
        exit();
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}
