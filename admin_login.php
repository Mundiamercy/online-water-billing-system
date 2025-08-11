<?php
session_start();
require 'config.php';

$message = "";  // Initialize to avoid undefined variable warning

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $message = "❌ Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
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

        h1.company-name {
            text-align: center;
            color: #222;
            margin-bottom: 10px;
            font-weight: 700;
            font-size: 22px;
            letter-spacing: 1px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        input, button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        button {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #007BFF;
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .message {
            text-align: center;
            color: red;
            margin-top: 10px;
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
    <!-- SVG decorative background -->
    <div class="svg-bg">
        <svg class="svg1" width="200" height="200" viewBox="0 0 200 200">
            <circle cx="100" cy="100" r="100" fill="#fff"/>
        </svg>
        <svg class="svg2" width="150" height="150" viewBox="0 0 150 150">
            <rect width="150" height="150" fill="#fff" rx="20"/>
        </svg>
    </div>

    <div class="form-container">
        <h1 class="company-name">Nairobi Water &amp; Sewage Company</h1>
        <h2>Admin Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a class="forgot-link" href="forgot_password.php">Forgot Password?</a>
        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
