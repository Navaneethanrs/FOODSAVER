<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

$conn = mysqli_connect("localhost", "root", "");
if (!$conn) {
    echo '{"success":false,"message":"MySQL not running"}';
    exit;
}

mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS food_saver");
mysqli_close($conn);

$conn = mysqli_connect("localhost", "root", "", "food_saver");
if (!$conn) {
    echo '{"success":false,"message":"Database connection failed"}';
    exit;
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS donations (
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

if ($_POST) {
    $name = mysqli_real_escape_string($conn, $_POST['donor_name']);
    $food = mysqli_real_escape_string($conn, $_POST['food_type']);
    $qty = mysqli_real_escape_string($conn, $_POST['quantity']);
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $contact = mysqli_real_escape_string($conn, $_POST['donor_contact']);
    $time = mysqli_real_escape_string($conn, $_POST['pickup_time']);
    $lat = $_POST['latitude'] ?? 'NULL';
    $lng = $_POST['longitude'] ?? 'NULL';
    
    $sql = "INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time, latitude, longitude) 
            VALUES ('$name', '$food', '$qty', '$loc', '$contact', '$time', $lat, $lng)";
    
    if (mysqli_query($conn, $sql)) {
        echo '{"success":true,"message":"Donation saved successfully!"}';
    } else {
        echo '{"success":false,"message":"Save failed"}';
    }
} else {
    echo '{"success":false,"message":"No data received"}';
}

mysqli_close($conn);
?>