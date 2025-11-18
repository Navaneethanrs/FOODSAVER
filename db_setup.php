<?php
// Simple Database Setup for Food Saver
// This file creates all necessary tables and sample data

echo "<!DOCTYPE html>";
echo "<html><head><title>Food Saver Database Setup</title>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style></head><body>";

echo "<h1>🍽️ Food Saver Database Setup</h1>";

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'food_saver';

try {
    // Connect to MySQL server (without selecting database)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✅ Connected to MySQL server</p>";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "<p class='success'>✅ Database '$dbname' created successfully</p>";
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create ngo_registrations table
    $sql = "CREATE TABLE IF NOT EXISTS ngo_registrations (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        ngo_name VARCHAR(255) NOT NULL,
        contact_person VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        address TEXT NOT NULL,
        operating_hours VARCHAR(100) NOT NULL,
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "<p class='success'>✅ Table 'ngo_registrations' created</p>";
    
    // Create donations table
    $sql = "CREATE TABLE IF NOT EXISTS donations (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        donor_name VARCHAR(255) NOT NULL,
        food_type VARCHAR(255) NOT NULL,
        quantity VARCHAR(255) NOT NULL,
        location TEXT NOT NULL,
        donor_contact VARCHAR(20) NOT NULL,
        pickup_time VARCHAR(50) NOT NULL,
        status ENUM('pending', 'assigned', 'picked_up', 'completed', 'cancelled') DEFAULT 'pending',
        notifications_sent INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "<p class='success'>✅ Table 'donations' created</p>";
    
    // Create ngo_notifications table
    $sql = "CREATE TABLE IF NOT EXISTS ngo_notifications (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        donation_id INT(11) NOT NULL,
        ngo_name VARCHAR(255) NOT NULL,
        ngo_phone VARCHAR(20),
        ngo_email VARCHAR(255),
        message TEXT NOT NULL,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('sent', 'delivered', 'failed') DEFAULT 'sent'
    )";
    $pdo->exec($sql);
    echo "<p class='success'>✅ Table 'ngo_notifications' created</p>";
    
    // Insert sample NGO data
    $sample_ngos = [
        ['Helping Hands NGO', 'Rajesh Kumar', '9876543210', 'contact@helpinghands.org', '123 Main Street, Salem', '9 AM - 6 PM'],
        ['Food for All', 'Priya Sharma', '9876543211', 'info@foodforall.org', '456 Park Avenue, Chennai', '8 AM - 8 PM'],
        ['Care Foundation', 'Amit Singh', '9876543212', 'care@foundation.org', '789 Gandhi Road, Salem', '10 AM - 5 PM']
    ];
    
    // Check if sample data already exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM ngo_registrations");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        echo "<p class='info'>📝 Inserting sample NGO data...</p>";
        foreach ($sample_ngos as $ngo) {
            $stmt = $pdo->prepare("INSERT INTO ngo_registrations (ngo_name, contact_person, phone_number, email, address, operating_hours) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute($ngo);
            echo "<p class='success'>✅ Added NGO: {$ngo[0]}</p>";
        }
    } else {
        echo "<p class='info'>ℹ️ Sample NGO data already exists ($count records)</p>";
    }
    
    // Display statistics
    echo "<hr><h2>📊 Database Statistics</h2>";
    
    $tables = ['ngo_registrations', 'donations', 'ngo_notifications'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "<p>📋 <strong>$table:</strong> $count records</p>";
    }
    
    echo "<hr><h2>🎉 Setup Complete!</h2>";
    echo "<p><strong>Your Food Saver database is ready!</strong></p>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Test NGO registration form</li>";
    echo "<li>Test donation form</li>";
    echo "<li>Check admin dashboard</li>";
    echo "</ol>";
    
    echo "<p><strong>Database Details:</strong></p>";
    echo "<ul>";
    echo "<li>Database: $dbname</li>";
    echo "<li>Host: $host</li>";
    echo "<li>Tables: ngo_registrations, donations, ngo_notifications</li>";
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p class='info'>Make sure XAMPP MySQL service is running!</p>";
}

echo "</body></html>";
?>
