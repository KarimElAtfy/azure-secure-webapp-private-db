<?php
/**
 * Simple connectivity test between Azure Web App and a private MySQL database.
 * This file is intentionally minimal and used only to validate
 * DNS resolution and network connectivity via Azure VNet Integration.
 */

// Database connection parameters
$host = "db.internal.cloud";
$db   = "appdb";
$user = "appuser";
$pass = "CHANGE_ME"; // Password is stored securely in Azure App Settings

echo "<h1>Azure Web App → Private MySQL</h1>";

try {
    // Build DSN (Data Source Name)
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

    // Attempt database connection
    $pdo = new PDO($dsn, $user, $pass);

    echo "<p style='color:green;'>Connected to DB successfully</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>Database connection failed</p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}

// Diagnostic info
echo "<p>Database host: $host</p>";
echo "<p>Timestamp: " . date("Y-m-d H:i:s") . "</p>";
?>
