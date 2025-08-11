<?php
// config.php - Database connection settings

$host = "localhost";         // Your database host, usually localhost
$dbname = "online_h2o_billing_system";  // Your database name
$username = "root";          // Your MySQL username (change if different)
$password = "";              // Your MySQL password (change if different)

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Set PDO error mode to exception for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // If connection fails, stop the script and show error message
    die("Database connection failed: " . $e->getMessage());
}
?>
