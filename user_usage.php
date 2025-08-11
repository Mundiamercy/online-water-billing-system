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
    <title>Your Water Usage - H2O Billing</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 0; }
        .header {
            background: #003f7f;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table thead {
            background: #007BFF;
            color: white;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            background: #007BFF;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-link:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="header">
    Your Water Usage - <?= htmlspecialchars($_SESSION['user_name']) ?>
</div>

<div class="container">
    <?php if (count($records) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Date of Reading</th>
                    <th>Units Used</th>
                    <th>Amount Paid (KES)</th>
                    <th>Balance (KES)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['reading_date']) ?></td>
                        <td><?= $row['units_used'] ?></td>
                        <td><?= $row['amount_paid'] ?></td>
                        <td style="color: <?= ($row['units_used'] * 100 - $row['amount_paid']) > 0 ? 'red' : 'green' ?>;">
                            <?= ($row['units_used'] * 100 - $row['amount_paid']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No usage records found.</p>
    <?php endif; ?>

    <a class="back-link" href="user_dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
