<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: _login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_usage'])) {
    $user_id = intval($_POST['user_id'] ?? 0);
    $units_used = intval($_POST['units_used'] ?? -1);
    $amount_paid = intval($_POST['amount_paid'] ?? -1);
    $reading_date = $_POST['reading_date'] ?? '';

    if ($user_id <= 0 || $units_used < 0 || $amount_paid < 0 || empty($reading_date)) {
        $message = "Please fill all water usage fields correctly.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO water_usage (user_id, units_used, amount_paid, reading_date) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$user_id, $units_used, $amount_paid, $reading_date]);
            $message = "Water usage record added successfully!";
        } catch (PDOException $e) {
            $message = "Error saving water usage: " . $e->getMessage();
        }
    }
}

$userQuery = $pdo->query("SELECT * FROM users ORDER BY full_name ASC");
$users = $userQuery->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Water Usage - H2O Billing</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        nav {
            background: #007BFF;
            padding: 10px 20px;
            color: white;
        }
        nav a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
            font-weight: bold;
        }
        .container {
            max-width: 400px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px #ccc;
            border-radius: 5px;
        }
        h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .btn {
            background: #007BFF;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .success { background-color: #28a745; color: white; }
        .error { background-color: #dc3545; color: white; }
    </style>
</head>
<body>

<nav>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="add_usage.php">Add Water Usage</a>
    <a href="admin_users.php">Manage Users</a>
    <a href="admin_logout.php">Logout</a>
</nav>

<div class="container">
    <h2>Add Water Usage</h2>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="add_usage" value="1">

        <div class="form-group">
            <label for="user_id">User:</label>
            <select id="user_id" name="user_id" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= htmlspecialchars($user['id']) ?>">
                        <?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars($user['username']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="units_used">Units Used:</label>
            <input type="number" id="units_used" name="units_used" placeholder="Units Used" min="0" required>
        </div>

        <div class="form-group">
            <label for="amount_paid">Amount Paid:</label>
            <input type="number" id="amount_paid" name="amount_paid" placeholder="Amount Paid" min="0" required>
        </div>

        <div class="form-group">
            <label for="reading_date">Reading Date:</label>
            <input type="date" id="reading_date" name="reading_date" required>
        </div>

        <button class="btn" type="submit">Add Usage</button>
    </form>
</div>

</body>
</html>
