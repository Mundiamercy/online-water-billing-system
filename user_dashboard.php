<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM water_usage WHERE user_id = ? ORDER BY reading_date DESC");
$stmt->execute([$userId]);
$records = $stmt->fetchAll();

$totalUnits = 0;
$totalPaid = 0;
$latestDue = 0;

foreach ($records as $row) {
    $totalUnits += $row['units_used'];
    $totalPaid += $row['amount_paid'];
}

if (!empty($records)) {
    $latest = $records[0];
    $latestDue = ($latest['units_used'] * 100) - $latest['amount_paid'];
}

$message = $_GET['message'] ?? '';
$message_type = $_GET['type'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>H2O Billing - User Dashboard</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 0; }
        .company-header {
            background: #003f7f;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
        }

        nav {
            background: #007BFF;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        nav .nav-left {
            font-weight: bold;
        }

        nav .nav-right a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        nav .nav-right a:hover {
            text-decoration: underline;
        }

        .container {
            padding: 20px;
            max-width: 1000px;
            margin: auto;
        }

        .card-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 8px #ccc;
            flex: 1 1 250px;
            text-align: center;
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .card p {
            font-size: 22px;
            font-weight: bold;
            color: #007BFF;
        }

        .message {
            max-width: 500px;
            margin: 20px auto;
            padding: 12px;
            border-radius: 5px;
            color: white;
            text-align: center;
        }

        .message.success { background-color: #28a745; }
        .message.error { background-color: #dc3545; }

        /* Removed change password form styles as form is removed */
    </style>
</head>
<body>

<div class="company-header">
    Nairobi Water & Sewage Company Customer Panel - H2O Billing
</div>

<nav>
    <div class="nav-left">
        Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>
    </div>
    <div class="nav-right">
        <!-- Updated to open on its own page -->
        <a href="usage_usage.php">Your Water Usage</a>
        <!-- Change Password link goes to separate page -->
        <a href="change_password.php">Change Password</a>
        <!-- Logout link -->
        <a href="user_logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <?php if ($message): ?>
        <div class="message <?= $message_type === 'success' ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Dashboard Cards -->
    <div class="card-group">
        <div class="card">
            <h3>Total Units Used</h3>
            <p><?= $totalUnits ?></p>
        </div>
        <div class="card">
            <h3>Total Amount Paid</h3>
            <p>KES <?= $totalPaid ?></p>
        </div>
        <div class="card">
            <h3>Latest Due Balance</h3>
            <p style="color: <?= $latestDue <= 0 ? 'green' : 'red' ?>;">KES <?= $latestDue ?></p>
        </div>
    </div>

</div>

</body>
</html>

