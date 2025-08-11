<?php
require 'config.php';

if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$id = $_GET['id'];

// Select only columns you use (excluding email)
$stmt = $pdo->prepare("SELECT id, full_name, username, phone FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: admin_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);

    // Update query without email
    $update = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, phone = ? WHERE id = ?");
    $update->execute([$full_name, $username, $phone, $id]);

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        form { background: white; padding: 20px; max-width: 500px; margin: auto; box-shadow: 0 0 10px #ccc; }
        input, button { width: 100%; padding: 10px; margin: 10px 0; }
        button { background: #007BFF; color: white; border: none; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Edit User</h2>
        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        <!-- Email removed -->
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
        <button type="submit">Update User</button>
    </form>
</body>
</html>
