<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: _login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $phone = trim($_POST["phone"]);

    if (!$full_name || !$username || !$email || !$password) {
        die("Please fill in all required fields.");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, password, phone) VALUES (?, ?, ?, ?, ?)");

    try {
        $stmt->execute([$full_name, $username, $email, $hashed_password, $phone]);
        header("Location: admin_dashboard.php?success=1");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}
