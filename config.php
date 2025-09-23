<?php
// config.php
$host = 'localhost';      // Usually 'localhost'
$db   = 'databasename'; // Your database name
$user = 'databaseusername'; // Your database username
$pass = 'yourdatabasepassword';  // Your database password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "✅ Database connected successfully!";
} catch (PDOException $e) {
    // Display the exact error message
    die("❌ Database connection failed: " . $e->getMessage());
}
