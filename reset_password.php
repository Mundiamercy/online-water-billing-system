<?php
session_start();
require 'config.php';

$message = "";
$validToken = false;
$token = htmlspecialchars($_GET['token'] ?? '');

if ($token) {
    // Check if the token is valid and not expired
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE reset_token = ? AND reset_expiry > NOW()");
    $stmt->execute([$token]);
    $admin = $stmt->fetch();

    if ($admin) {
        $validToken = true;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newPassword = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($newPassword === $confirmPassword && strlen($newPassword) >= 6) {
                $hashedPwd = password_hash($newPassword, PASSWORD_DEFAULT);

                $update = $pdo->prepare("UPDATE admins SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
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
        // Check if token exists but expired
        $checkToken = $pdo->prepare("SELECT * FROM admins WHERE reset_token = ?");
        $checkToken->execute([$token]);
        if ($checkToken->fetch()) {
            $message = "⚠️ Reset link has expired. Please request a new one.";
        } else {
            $message = "❌ Invalid reset link.";
        }
    }
} else {
    $message = "❌ No token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #007BFF 30%, #00C6FF 100%);
            padding: 50px;
            margin: 0;
            overflow: hidden;
        }
        .form-container {
            background: white;
            padding: 25px;
            width: 360px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            text-align: center;
            position: relative;
            z-index: 2;
        }
        h2 {
            margin-bottom: 20px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        p {
            color: #444;
            margin-top: 10px;
            white-space: pre-wrap;
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }

        /* SVG background shapes */
        svg {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            opacity: 0.1;
        }
    </style>
</head>
<body>
    <svg viewBox="0 0 1440 320"><path fill="#ffffff" fill-opacity="1" d="M0,224L48,202.7C96,181,192,139,288,128C384,117,480,139,576,138.7C672,139,768,117,864,128C960,139,1056,181,1152,176C1248,171,1344,117,1392,90.7L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>

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

