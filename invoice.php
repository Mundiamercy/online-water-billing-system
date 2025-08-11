<?php
require 'config.php';

// Validate and fetch invoice as you already do
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Invalid or missing invoice ID.");
}
$invoice_id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT wu.*, u.full_name, u.phone 
    FROM water_usage wu
    JOIN users u ON wu.user_id = u.id
    WHERE wu.id = ?
");
$stmt->execute([$invoice_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("❌ No invoice found for ID: " . htmlspecialchars($invoice_id));
}

$rate_per_unit = 100;
$units_used = (int)$data['units_used'];
$amount_paid = (int)$data['amount_paid'];
$total_due = $units_used * $rate_per_unit;
$balance = $amount_paid - $total_due;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice #<?= $invoice_id ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 30px; }
        .invoice-box { background: white; padding: 30px; max-width: 700px; margin: auto; box-shadow: 0 0 15px #ccc; }
        h2, h3 { text-align: center; margin: 0; }
        .company-info, .customer-info, .invoice-info {
            margin-bottom: 20px;
        }
        .company-info strong {
            font-size: 20px;
            color: #007BFF;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        .total-row td {
            font-weight: bold;
            background: #f0f0f0;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .btn-pdf {
            display: inline-block;
            margin-top: 20px;
            background-color: #007BFF;
            color: white;
            padding: 10px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-pdf:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="company-info">
        <strong>H2O Billing Company</strong><br>
        123 Water Street, Nairobi, Kenya<br>
        Phone: +254 700 000 000<br>
        Email: info@h2obilling.co.ke
    </div>

    <div class="customer-info">
        <strong>Invoice To:</strong><br>
        <?= htmlspecialchars($data['full_name']) ?><br>
        Phone: <?= htmlspecialchars($data['phone']) ?>
    </div>

    <div class="invoice-info">
        <strong>Invoice #: </strong> <?= $invoice_id ?><br>
        <strong>Date: </strong> <?= htmlspecialchars($data['reading_date']) ?><br>
        <strong>Payment Terms: </strong> Due on receipt
    </div>

    <table>
        <tr>
            <th>Description</th>
            <th>Units</th>
            <th>Rate (KES)</th>
            <th>Total (KES)</th>
        </tr>
        <tr>
            <td>Water Usage</td>
            <td><?= $units_used ?></td>
            <td><?= number_format($rate_per_unit, 2) ?></td>
            <td><?= number_format($total_due, 2) ?></td>
        </tr>
        <tr class="total-row">
            <td colspan="3">Amount Paid</td>
            <td><?= number_format($amount_paid, 2) ?></td>
        </tr>
        <tr class="total-row">
            <td colspan="3">Balance</td>
            <td><?= number_format($balance, 2) ?></td>
        </tr>
    </table>

    <a href="invoice_pdf.php?id=<?= $invoice_id ?>" target="_blank" class="btn-pdf">Download PDF</a>

    <div class="footer">
        Thank you for your business! Please pay by the due date.<br>
        H2O Billing © <?= date('Y') ?>
    </div>
</div>

</body>
</html>
