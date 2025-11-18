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
        // Validate required fields
        if (empty($_POST['ngo_name']) || empty($_POST['contact_person']) || empty($_POST['phone_number']) || empty($_POST['email']) || empty($_POST['address']) || empty($_POST['operating_hours'])) {
            echo json_encode(["success" => false, "message" => "All fields are required"]);
            exit;
        }
        
        // Validate email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["success" => false, "message" => "Invalid email format"]);
            exit;
        }
        
        // Check if email already exists
        $check_stmt = $conn->prepare("SELECT id FROM ngo_registrations WHERE email = ?");
        $check_stmt->execute([$_POST['email']]);
        if ($check_stmt->rowCount() > 0) {
            echo json_encode(["success" => false, "message" => "An NGO with this email is already registered"]);
            exit;
        }
        
        // Get GPS coordinates if available
        $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
        $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;
        
        // Insert data into database
        $stmt = $conn->prepare("INSERT INTO ngo_registrations (ngo_name, contact_person, phone_number, email, address, operating_hours, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['ngo_name'],
            $_POST['contact_person'],
            $_POST['phone_number'],
            $_POST['email'],
            $_POST['address'],
            $_POST['operating_hours'],
            $latitude,
            $longitude
        ]);
        
        echo json_encode(["success" => true, "message" => "NGO registered successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
    }
} catch(PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
