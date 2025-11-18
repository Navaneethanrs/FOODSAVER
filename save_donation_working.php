<?php
header('Content-Type: application/json');
error_reporting(0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Database connection
        $conn = new mysqli("localhost", "root", "");
        if ($conn->connect_error) {
            throw new Exception("Connection failed");
        }
        
        // Create database if it doesn't exist
        $conn->query("CREATE DATABASE IF NOT EXISTS food_saver");
        $conn->select_db("food_saver");
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Create table if it doesn't exist
    $conn->query("CREATE TABLE IF NOT EXISTS donations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        donor_name VARCHAR(255),
        food_type VARCHAR(255),
        quantity VARCHAR(255),
        location TEXT,
        donor_contact VARCHAR(20),
        pickup_time VARCHAR(50),
        latitude DECIMAL(10,8),
        longitude DECIMAL(11,8),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Get form data
    $donor_name = $_POST['donor_name'];
    $food_type = $_POST['food_type'];
    $quantity = $_POST['quantity'];
    $location = $_POST['location'];
    $donor_contact = $_POST['donor_contact'];
    $pickup_time = $_POST['pickup_time'];
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    
    // Insert data
    $stmt = $conn->prepare("INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssdd", $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time, $latitude, $longitude);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Donation saved successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
    
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>