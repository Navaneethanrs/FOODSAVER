<?php
echo "<h2>Database Connection Test</h2>";

$servername = "localhost";
$username = "root";
$password = "";

try {
    $conn = new mysqli($servername, $username, $password);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>Connection failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✓ Connected to MySQL successfully!</p>";
        
        // Test database creation
        if ($conn->query("CREATE DATABASE IF NOT EXISTS food_saver")) {
            echo "<p style='color: green;'>✓ Database 'food_saver' created/exists</p>";
        } else {
            echo "<p style='color: red;'>Error creating database: " . $conn->error . "</p>";
        }
        
        // Select database
        if ($conn->select_db("food_saver")) {
            echo "<p style='color: green;'>✓ Database selected successfully</p>";
        } else {
            echo "<p style='color: red;'>Error selecting database: " . $conn->error . "</p>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Exception: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>Make sure XAMPP is running</li>";
echo "<li>Start Apache and MySQL services in XAMPP Control Panel</li>";
echo "<li>Both services should show green status</li>";
echo "<li>If you see connection errors, check MySQL is running on port 3306</li>";
echo "</ol>";
?>