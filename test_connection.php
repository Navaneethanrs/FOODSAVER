<?php
// Test database connection
$servername = "localhost";
$username = "root";
$password = "";

try {
    $conn = new mysqli($servername, $username, $password);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "✅ MySQL connection successful!<br>";
    
    // Test database creation
    $dbname = "food_saver";
    $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->select_db($dbname);
    
    echo "✅ Database 'food_saver' ready!<br>";
    
    // Test table creation
    $sql = "CREATE TABLE IF NOT EXISTS test_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        test_data VARCHAR(255)
    )";
    
    if ($conn->query($sql)) {
        echo "✅ Table creation works!<br>";
    } else {
        echo "❌ Table creation failed: " . $conn->error . "<br>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>