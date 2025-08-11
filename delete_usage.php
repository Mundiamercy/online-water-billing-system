<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: _login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM water_usage WHERE id = ?");
    try {
        $stmt->execute([$id]);
        header("Location: admin_dashboard.php?msg=Usage record deleted successfully");
        exit();
    } catch (PDOException $e) {
        // Could log error or show a message on dashboard
        header("Location: admin_dashboard.php?msg=Error deleting usage record");
        exit();
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}

