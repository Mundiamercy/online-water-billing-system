<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_dashboard.php");
    exit();
}

$adminId = $_SESSION['admin_id'];
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Basic validations
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $msg = "All password fields are required.";
    header("Location: admin_dashboard.php?message=" . urlencode($msg) . "&type=error");
    exit();
}

if ($new_password !== $confirm_password) {
    $msg = "New password and confirmation do not match.";
    header("Location: admin_dashboard.php?message=" . urlencode($msg) . "&type=error");
    exit();
}

// Fetch the admin's current hashed password from DB
$stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

if (!$admin) {
    $msg = "Admin not found.";
    header("Location: admin_dashboard.php?message=" . urlencode($msg) . "&type=error");
    exit();
}

// Verify current password
if (!password_verify($current_password, $admin['password'])) {
    $msg = "Current password is incorrect.";
    header("Location: admin_dashboard.php?message=" . urlencode($msg) . "&type=error");
    exit();
}

// Hash new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password in DB
$update = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
$update->execute([$hashed_password, $adminId]);

$msg = "Password changed successfully!";
header("Location: admin_dashboard.php?message=" . urlencode($msg) . "&type=success");
exit();
