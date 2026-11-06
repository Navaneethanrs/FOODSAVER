<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_saver";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $stmt = $conn->prepare("INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time) VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt = $conn->prepare("INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time) VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['donor_name'],
            $_POST['food_type'],
            $_POST['quantity'],
            $_POST['location'],
            $_POST['donor_contact'],
            $_POST['pickup_time']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Donation submitted successfully']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>