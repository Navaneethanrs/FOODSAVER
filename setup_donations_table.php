<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_saver";

try {
    $conn = new PDO("mysql:host=$servername;port=3307;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "CREATE TABLE IF NOT EXISTS donations (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        donor_name VARCHAR(255) NOT NULL,
        food_type VARCHAR(255) NOT NULL,
        quantity VARCHAR(255) NOT NULL,
        location TEXT NOT NULL,
        donor_contact VARCHAR(20) NOT NULL,
        pickup_time VARCHAR(50) NOT NULL,
        latitude DECIMAL(10, 8) NULL,
        longitude DECIMAL(11, 8) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    echo "✓ Donations table created successfully!";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>