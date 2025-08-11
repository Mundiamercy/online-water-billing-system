<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash the password
    $phone = $_POST['phone'];

    $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, password, phone) VALUES (?, ?, ?, ?, ?)");
    try {
        $stmt->execute([$full_name, $username, $email, $password, $phone]);
        header("Location: admin_dashboard.php?success=1");
        exit();
    } catch (PDOException $e) {
        header("Location: admin_dashboard.php?error=1");
        exit();
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}
