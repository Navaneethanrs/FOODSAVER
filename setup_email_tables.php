<?php
// Setup database tables for email functionality
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_saver";
$port = 3307;

try {
    $conn = new PDO("mysql:host=$servername;port=$port", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->exec("USE $dbname");
    
    // Create donations table
    $conn->exec("CREATE TABLE IF NOT EXISTS donations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        donor_name VARCHAR(255) NOT NULL,
        food_type VARCHAR(255) NOT NULL,
        quantity VARCHAR(255) NOT NULL,
        location TEXT NOT NULL,
        donor_contact VARCHAR(20) NOT NULL,
        pickup_time VARCHAR(50) NOT NULL,
        latitude DECIMAL(10,8),
        longitude DECIMAL(11,8),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create NGO registrations table
    $conn->exec("CREATE TABLE IF NOT EXISTS ngo_registrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ngo_name VARCHAR(255) NOT NULL,
        contact_person VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        address TEXT NOT NULL,
        operating_hours VARCHAR(100),
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    echo "<h2>✅ Database Setup Complete!</h2>";
    echo "<p>Both tables created successfully:</p>";
    echo "<ul>";
    echo "<li>donations - for storing food donations</li>";
    echo "<li>ngo_registrations - for storing NGO information</li>";
    echo "</ul>";
    echo "<p><a href='test_email_simple.php'>Test Email Configuration</a></p>";
    echo "<p><a href='donate.html'>Go to Donation Form</a></p>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>