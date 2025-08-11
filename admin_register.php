<?php
require 'config.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");

    try {
        $stmt->execute([$username, $email, $password]);
        // Redirect to login page after successful registration
        header("Location: admin_login.php");
        exit();
    } catch (PDOException $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
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

        .company-name {
            text-align: center;
            font-weight: 700;
            font-size: 1.4rem;
            color: #007BFF;
            margin-bottom: 10px;
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

        p.message {
            text-align: center;
            color: green;
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
        <div class="company-name">Nairobi Water &amp; Sewage Company</div>
        <h2>Admin Registration</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
