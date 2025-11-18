<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_saver";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "<h2>Food Saver Database Setup</h2>";
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Database '$dbname' created successfully or already exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating database: " . $conn->error . "</p>";
    }
    
    // Select database
    $conn->select_db($dbname);
    
    // Create donations table
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
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Table 'donations' created successfully or already exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating donations table: " . $conn->error . "</p>";
    }
    
    // Create NGO registrations table
    $sql = "CREATE TABLE IF NOT EXISTS ngo_registrations (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        ngo_name VARCHAR(255) NOT NULL,
        contact_person VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        address TEXT NOT NULL,
        operating_hours VARCHAR(100) NOT NULL,
        latitude DECIMAL(10, 8) NULL,
        longitude DECIMAL(11, 8) NULL,
        accuracy DECIMAL(10, 2) NULL,
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Table 'ngo_registrations' created successfully or already exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating ngo_registrations table: " . $conn->error . "</p>";
    }
    
    // Create contact messages table
    $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Table 'contact_messages' created successfully or already exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating contact_messages table: " . $conn->error . "</p>";
    }
    
    echo "<br><h3>Database setup completed!</h3>";
    echo "<p><a href='donate.html'>Test Donation Form</a> | <a href='view_donations.php'>View Donations</a> | <a href='registerngo.html'>Register NGO</a> | <a href='view_ngos.php'>View NGOs</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>