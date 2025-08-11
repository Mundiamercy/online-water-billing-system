<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$current_password || !$new_password || !$confirm_password) {
        $message = 'Please fill in all fields.';
        $message_type = 'error';
    } elseif ($new_password !== $confirm_password) {
        $message = 'New passwords do not match.';
        $message_type = 'error';
    } elseif (strlen($new_password) < 6) {
        $message = 'New password must be at least 6 characters.';
        $message_type = 'error';
    } else {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user && password_verify($current_password, $user['password'])) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($update->execute([$new_password_hash, $userId])) {
                header("Location: user_dashboard.php?message=Password updated successfully.&type=success");
                exit();
            } else {
                $message = 'Failed to update password. Please try again.';
                $message_type = 'error';
            }
        } else {
            $message = 'Current password is incorrect.';
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password - H2O Billing</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6fb1fc, #4364f7);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .form-container {
            background: white;
            padding: 30px;
            width: 380px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
            z-index: 10;
            position: relative;
        }

        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 22px;
            letter-spacing: 1px;
        }

        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            margin-top: 10px;
            width: 100%;
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 6px;
            padding: 12px;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 15px;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            color: white;
            font-weight: 600;
        }

        .message.error {
            background-color: #dc3545;
        }

        .message.success {
            background-color: #28a745;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            color:rgb(33, 193, 22);
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* SVG decorative background */
        .svg-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
            top: 0;
            left: 0;
            overflow: hidden;
        }

        .svg-bg svg {
            position: absolute;
            opacity: 0.15;
        }

        .svg1 {
            top: -50px;
            left: -60px;
        }

        .svg2 {
            bottom: -40px;
            right: -40px;
        }
    </style>
</head>
<body>

<div class="svg-bg">
    <svg class="svg1" width="200" height="200" viewBox="0 0 200 200">
        <circle cx="100" cy="100" r="100" fill="#fff"/>
    </svg>
    <svg class="svg2" width="150" height="150" viewBox="0 0 150 150">
        <rect width="150" height="150" fill="#fff" rx="20"/>
    </svg>
</div>

<div class="form-container">
    <h2>Change Password</h2>

    <?php if ($message): ?>
        <div class="message <?= $message_type === 'success' ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form action="change_password.php" method="POST" novalidate>
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required minlength="6">

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">

        <button type="submit">Update Password</button>
    </form>

    <a href="user_dashboard.php" class="back-link">&larr; Back to Dashboard</a>
</div>

</body>
</html>
