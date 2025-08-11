<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: _login.php");
    exit();
}

$stmt = $pdo->query("
    SELECT wu.id, u.full_name, wu.units_used, wu.amount_paid, wu.reading_date
    FROM water_usage wu
    JOIN users u ON wu.user_id = u.id
    ORDER BY wu.reading_date DESC
");
$records = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Water Usage Records - Admin - H2O Billing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #007BFF;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .nav-links a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-weight: bold;
        }
        .container {
            padding: 20px;
            max-width: 900px;
            margin: auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px #ccc;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007BFF;
            color: white;
        }
        .actions a {
            margin: 0 5px;
            font-size: 16px;
            text-decoration: none;
            color: #007BFF;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .invoice-btn {
            background-color: #007BFF;
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .invoice-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div><strong>Admin Panel - H2O Billing</strong></div>
    <div class="nav-links">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="add_usage.php">Add Water Usage</a>
        <a href="admin_users.php">Manage Users</a>
        <a href="admin_usage_records.php">Water Usage Records</a>
        <a href="admin_logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Water Usage Records</h2>

    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Units Used</th>
                <th>Current Balance</th>
                <th>Due Amount</th>
                <th>Status</th>
                <th>Reading Date</th>
                <th>Actions</th>
                <th>Invoice</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($records): ?>
                <?php foreach ($records as $row): ?>
                    <?php
                        $due_amount = (int)$row['units_used'] * 100;
                        $current_balance = (int)$row['amount_paid'] - $due_amount;
                        $status = ($current_balance >= 0) ? "Paid" : "Not Paid";
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= (int)$row['units_used'] ?></td>
                        <td><?= number_format($current_balance) ?></td>
                        <td><?= number_format($due_amount) ?></td>
                        <td><?= $status ?></td>
                        <td><?= htmlspecialchars($row['reading_date']) ?></td>
                        <td class="actions">
                            <a href="edit_usage.php?id=<?= $row['id'] ?>" title="Edit Record">✏️</a>
                            <a href="delete_usage.php?id=<?= $row['id'] ?>" title="Delete Record" onclick="return confirm('Delete this record?');">🗑️</a>
                        </td>
                        <td>
                            <a href="invoice.php?id=<?= $row['id'] ?>" title="View Invoice" target="_blank" class="invoice-btn">Invoice</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">No records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
