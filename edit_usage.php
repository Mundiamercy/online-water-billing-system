<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: _login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch the usage record
$stmt = $pdo->prepare("SELECT * FROM water_usage WHERE id = ?");
$stmt->execute([$id]);
$usage = $stmt->fetch();

if (!$usage) {
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all users for dropdown
$users = $pdo->query("SELECT id, full_name FROM users ORDER BY full_name")->fetchAll();

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id'] ?? 0);
    $units_used = intval($_POST['units_used'] ?? -1);
    $amount_paid = intval($_POST['amount_paid'] ?? -1);
    $reading_date = $_POST['reading_date'] ?? '';

    if ($user_id <= 0 || $units_used < 0 || $amount_paid < 0 || empty($reading_date)) {
        $message = "Please fill all fields correctly.";
    } else {
        $update = $pdo->prepare("UPDATE water_usage SET user_id = ?, units_used = ?, amount_paid = ?, reading_date = ? WHERE id = ?");
        try {
            $update->execute([$user_id, $units_used, $amount_paid, $reading_date, $id]);
            header("Location: admin_dashboard.php?msg=Usage record updated successfully");
            exit();
        } catch (PDOException $e) {
            $message = "Update failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Water Usage Record</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        form { background: white; padding: 20px; border-radius: 5px; max-width: 400px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { background: #007BFF; color: white; padding: 10px; border: none; cursor: pointer; width: 100%; }
        .message { margin-bottom: 15px; color: red; }
        a { display: inline-block; margin-top: 15px; text-decoration: none; color: #007BFF; }
    </style>
</head>
<body>

<h2>Edit Water Usage Record</h2>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label for="user_id">User</label>
        <select name="user_id" id="user_id" required>
            <option value="">-- Select User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>" <?= ($user['id'] == $usage['user_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="units_used">Units Used</label>
        <input type="number" name="units_used" id="units_used" min="0" required value="<?= (int)$usage['units_used'] ?>">
    </div>

    <div class="form-group">
        <label for="amount_paid">Amount Paid</label>
        <input type="number" name="amount_paid" id="amount_paid" min="0" required value="<?= (int)$usage['amount_paid'] ?>">
    </div>

    <div class="form-group">
        <label for="reading_date">Reading Date</label>
        <input type="date" name="reading_date" id="reading_date" required value="<?= htmlspecialchars($usage['reading_date']) ?>">
    </div>

    <button type="submit" class="btn">Update Record</button>
</form>

<a href="admin_dashboard.php">← Back to Dashboard</a>

</body>
</html>
