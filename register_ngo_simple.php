<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_saver";

try {
    $conn = new PDO("mysql:host=$servername;port=3307;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    // Create database connection
    $conn_create = new PDO("mysql:host=$servername", $username, $password);
    $conn_create->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn_create->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn_create = null;
    
    // Reconnect to the specific database
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create NGO registrations table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS ngo_registrations (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        ngo_name VARCHAR(255) NOT NULL,
        contact_person VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        address TEXT NOT NULL,
        operating_hours VARCHAR(100) NOT NULL,
        latitude DECIMAL(10, 8) NULL,
        longitude DECIMAL(11, 8) NULL,
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($createTable);
    
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
        $latitude = isset($_POST['latitude']) && $_POST['latitude'] !== '' ? floatval($_POST['latitude']) : null;
        $longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? floatval($_POST['longitude']) : null;
        
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
        
        echo json_encode([
            "success" => true, 
            "message" => "NGO registered successfully!",
            "data" => [
                "id" => $conn->lastInsertId(),
                "coordinates" => $latitude && $longitude ? "GPS: $latitude, $longitude" : "No GPS data"
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
    }
} catch(PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>