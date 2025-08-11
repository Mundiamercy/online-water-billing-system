<?php
require 'config.php';

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
        body { font-family: Arial; background: #f4f4f4; padding: 30px; }
        .invoice-box {
            background: white;
            padding: 20px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 0 10px #ccc;
        }
        h2 { text-align: center; margin-top: 10px; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .company-info {
            text-align: right;
        }
        .logo {
            max-height: 80px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        .footer {
            margin-top: 30px;
            font-size: 13px;
        }
        .print-btn {
            display: block;
            margin: 20px auto;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-align: center;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .print-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="invoice-box" id="invoiceContent">
    <div class="header">
        <div>
            <img src="images/logo.png" alt="Company Logo" class="logo">
        </div>
        <div class="company-info">
            <strong>Nairobi Water & Sewage Company</strong><br>
            123 Aqua Lane, Nairobi<br>
            Phone: +254 700 123 456<br>
            Email: info@nairobiwater.co.ke
        </div>
    </div>

    <h2>Water Billing Invoice</h2>

    <p><strong>Customer:</strong> <?= htmlspecialchars($data['full_name']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($data['phone']) ?></p>
    <p><strong>Reading Date:</strong> <?= htmlspecialchars($data['reading_date']) ?></p>

    <table>
        <tr>
            <th>Description</th>
            <th>Units</th>
            <th>Rate</th>
            <th>Total</th>
        </tr>
        <tr>
            <td>Water Usage</td>
            <td><?= $units_used ?></td>
            <td>Ksh <?= number_format($rate_per_unit) ?></td>
            <td>Ksh <?= number_format($total_due) ?></td>
        </tr>
        <tr>
            <td colspan="3"><strong>Amount Paid</strong></td>
            <td>Ksh <?= number_format($amount_paid) ?></td>
        </tr>
        <tr>
            <td colspan="3"><strong>Balance</strong></td>
            <td>Ksh <?= number_format($balance) ?></td>
        </tr>
    </table>

    <div class="footer">
        <p><strong>Payment Terms:</strong> Please pay any outstanding balance within 7 days.</p>
        <p><strong>Thank you for your business!</strong></p>
    </div>
</div>

<button onclick="window.print();" class="print-btn">🖨️ Print or Save as PDF</button>

</body>
</html>
