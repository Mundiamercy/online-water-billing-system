<?php
session_start();
require 'config.php';

$message = "";
$validToken = false;
$token = htmlspecialchars($_GET['token'] ?? '');

if (!$token) {
    // Redirect user back to forgot password page if no token is present
    header("Location: forgot_password.php");
    exit();
}

// Token exists, check if valid
$stmt = $pdo->prepare("SELECT * FROM admin WHERE reset_token = ?");
$stmt->execute([$token]);
$admin = $stmt->fetch();

if ($admin) {
    $validToken = true;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword === $confirmPassword && strlen($newPassword) >= 6) {
            $hashedPwd = password_hash($newPassword, PASSWORD_DEFAULT);

            $update = $pdo->prepare("UPDATE admin SET password = ?, reset_token = NULL WHERE id = ?");
            $update->execute([$hashedPwd, $admin['id']]);

            $message = "✅ Password reset successful! Redirecting to login page in 5 seconds...";
            $validToken = false;
            header("refresh:5;url=admin_login.php");
            exit();
        } else {
            $message = "❌ Passwords do not match or are too short (min 6 chars).";
        }
    }
} else {
    $message = "❌ Invalid reset link.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <style>
        /* Your existing styles here */
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Reset Password</h2>

        <?php if ($validToken): ?>
            <form method="POST" novalidate>
                <input type="password" name="password" placeholder="New password (min 6 chars)" required minlength="6" autocomplete="new-password">
                <input type="password" name="confirm_password" placeholder="Confirm new password" required minlength="6" autocomplete="new-password">
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>

        <p><?= htmlspecialchars($message) ?></p>
    </div>
</body>
</html>


