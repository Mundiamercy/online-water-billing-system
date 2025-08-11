<?php
session_start();
require 'config.php';

$message = "";
$resetLink = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);

    // Check if username exists in the admin table
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin) {
        // Generate a secure random token
        $token = bin2hex(random_bytes(16));

        // Save token to database for this admin user
        $update = $pdo->prepare("UPDATE admin SET reset_token = ? WHERE id = ?");
        $update->execute([$token, $admin['id']]);

        // Construct the reset link
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $resetLink = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";

        $message = "✅ Reset link generated! (In a real app, this would be emailed to you.)";
    } else {
        $message = "❌ Username not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <style>
        /* Your existing styles here, or add styling you like */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #007BFF, #00C6FF);
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
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
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
        <h2>Forgot Password</h2>
        <form method="POST" novalidate>
            <input type="text" name="username" placeholder="Enter your username" required>
            <button type="submit">Send Reset Link</button>
        </form>

        <p><?= htmlspecialchars($message) ?></p>

        <?php if ($resetLink): ?>
            <p><strong>Reset Link:</strong><br><a href="<?= htmlspecialchars($resetLink) ?>" target="_blank"><?= htmlspecialchars($resetLink) ?></a></p>
        <?php endif; ?>

        <p><a href="admin_login.php">Back to Login</a></p>
    </div>
</body>
</html>
