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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Water Usage</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 0; }
        .company-header {
            background: #003f7f;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
        }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px #ccc;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007BFF;
            color: white;
        }
    </style>
</head>
<body>
<div class="company-header">Your Water Usage</div>
<?php include 'user_navbar.php'; ?>

<table>
    <thead>
        <tr>
            <th>Units Used</th>
            <th>Amount Paid</th>
            <th>Due Amount</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($records): foreach ($records as $row): 
            $due = ($row['units_used'] * 100) - $row['amount_paid'];
            $status = $due <= 0 ? 'Paid' : 'Not Paid';
        ?>
        <tr>
            <td><?= $row['units_used'] ?></td>
            <td><?= $row['amount_paid'] ?></td>
            <td><?= $due ?></td>
            <td style="color: <?= $status === 'Paid' ? 'green' : 'red' ?>; font-weight: bold;"><?= $status ?></td>
            <td><?= $row['reading_date'] ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="5">No records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</body>
</html>
