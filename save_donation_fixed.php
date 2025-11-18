<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_saver";

try {
    $conn = new PDO("mysql:host=$servername;port=3307;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST['donor_name']) || empty($_POST['food_type']) || empty($_POST['quantity']) || empty($_POST['location']) || empty($_POST['donor_contact']) || empty($_POST['pickup_time'])) {
            echo json_encode(["success" => false, "message" => "All fields are required"]);
            exit;
        }
        
        $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
        $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;
        
        $stmt = $conn->prepare("INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['donor_name'],
            $_POST['food_type'],
            $_POST['quantity'],
            $_POST['location'],
            $_POST['donor_contact'],
            $_POST['pickup_time'],
            $latitude,
            $longitude
        ]);
        
        echo json_encode(["success" => true, "message" => "Donation submitted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
    }
} catch(PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>