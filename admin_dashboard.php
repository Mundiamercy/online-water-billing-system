<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: _login.php");
    exit();
}

// Fetch dashboard summary stats
// Total users
$stmtUsers = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmtUsers->fetchColumn();

// Total water usage records
$stmtUsage = $pdo->query("SELECT COUNT(*) FROM water_usage");
$totalUsageRecords = $stmtUsage->fetchColumn();

// Total units used
$stmtUnits = $pdo->query("SELECT SUM(units_used) FROM water_usage");
$totalUnitsUsed = $stmtUnits->fetchColumn();
$totalUnitsUsed = $totalUnitsUsed ?: 0; // in case null

// Total payments made
$stmtPayments = $pdo->query("SELECT SUM(amount_paid) FROM water_usage");
$totalPayments = $stmtPayments->fetchColumn();
$totalPayments = $totalPayments ?: 0; // in case null
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - H2O Billing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .header-title {
            background: #003366;
            color: white;
            padding: 20px;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
        }
        .navbar {
            background: #007BFF;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: center;
            gap: 25px;
            font-weight: bold;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 6px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .navbar a:hover {
            background-color: #0056b3;
        }
        .container {
            padding: 30px 20px;
            max-width: 900px;
            margin: auto;
        }
        h2.welcome {
            text-align: center;
            margin-bottom: 40px;
            color: #003366;
            font-weight: 700;
        }
        .cards {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 40px;
        }
        .card {
            flex: 1 1 200px;
            background: white;
            padding: 25px 20px;
            border-radius: 8px;
            box-shadow: 0 3px 7px rgba(0,0,0,0.1);
            text-align: center;
            color: #003366;
        }
        .card h3 {
            font-size: 24px;
            margin: 0 0 10px;
        }
        .card p {
            font-size: 40px;
            font-weight: bold;
            margin: 0;
            color: #007BFF;
        }
        .btn-view-records {
            display: block;
            max-width: 220px;
            margin: 0 auto;
            background-color: #007BFF;
            color: white;
            text-align: center;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s;
        }
        .btn-view-records:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<!-- Company name and admin panel title -->
<div class="header-title">
    Nairobi Water &amp; Sewage Company Admin Panel - H2O Billing
</div>

<!-- Navigation bar -->
<div class="navbar">
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="add_usage.php">Add Water Usage</a>
    <a href="admin_users.php">Manage Users</a>
    <a href="admin_usage_records.php">Water Usage Records</a>
    <a href="admin_logout.php">Logout</a>
</div>

<div class="container">
    <h2 class="welcome">Welcome to Admin Dashboard</h2>

    <!-- Summary cards -->
    <div class="cards">
        <div class="card">
            <h3>Total Users</h3>
            <p><?= htmlspecialchars($totalUsers) ?></p>
        </div>
        <div class="card">
            <h3>Total Usage Records</h3>
            <p><?= htmlspecialchars($totalUsageRecords) ?></p>
        </div>
        <div class="card">
            <h3>Total Units Used</h3>
            <p><?= htmlspecialchars($totalUnitsUsed) ?></p>
        </div>
        <div class="card">
            <h3>Total Payments (Ksh)</h3>
            <p><?= number_format($totalPayments) ?></p>
        </div>
    </div>

    <!-- Button linking to Water Usage Records page -->
    <a href="admin_usage_records.php" class="btn-view-records">View Water Usage Records</a>
</div>

</body>
</html>
