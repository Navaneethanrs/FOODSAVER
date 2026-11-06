<?php
header('Content-Type: application/json');

try {
    $conn = new mysqli("localhost", "root", "");
    $conn->query("CREATE DATABASE IF NOT EXISTS food_saver");
    $conn->select_db("food_saver");
    
    $conn->query("CREATE TABLE IF NOT EXISTS donations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        donor_name VARCHAR(255),
        food_type VARCHAR(255),
        quantity VARCHAR(255),
        location TEXT,
        donor_contact VARCHAR(20),
        pickup_time VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    if ($_POST) {
        $name = $conn->real_escape_string($_POST['donor_name']);
        $food = $conn->real_escape_string($_POST['food_type']);
        $qty = $conn->real_escape_string($_POST['quantity']);
        $loc = $conn->real_escape_string($_POST['location']);
        $contact = $conn->real_escape_string($_POST['donor_contact']);
        $time = $conn->real_escape_string($_POST['pickup_time']);
        
        $sql = "INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time) 
                VALUES ('$name', '$food', '$qty', '$loc', '$contact', '$time')";
        
        $conn->query($sql);
        echo json_encode(['success' => true, 'message' => 'Saved successfully!']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error occurred']);
}
?>