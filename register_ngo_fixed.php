<?php
header('Content-Type: application/json');

// Ensure any PHP warnings/notices are returned as JSON, not HTML
error_reporting(E_ALL);
ini_set('display_errors', '0');
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
    exit;
});

// Try different MySQL connection methods for XAMPP
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "food_saver";

$conn = null;

// Try multiple connection methods
try {
    // Method 1: Default connection (explicit port 3307 for XAMPP)
    $conn = new mysqli($servername, $username, $password, '', 3307);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if it doesn't exist
    $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->select_db($dbname);
    
    // Create table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS ngo_registrations (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        ngo_name VARCHAR(255) NOT NULL,
        contact_person VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        email VARCHAR(255) NOT NULL,
        address TEXT NOT NULL,
        operating_hours VARCHAR(100) NOT NULL,
        latitude DECIMAL(10, 8) NULL,
        longitude DECIMAL(11, 8) NULL,
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($createTable)) {
        throw new Exception("Error creating table: " . $conn->error);
    }

    // Ensure latitude/longitude columns exist (older setups may miss them)
    $dbNameEsc = $conn->real_escape_string($dbname);
    $checkCols = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='{$dbNameEsc}' AND TABLE_NAME='ngo_registrations'";
    $colsRes = $conn->query($checkCols);
    if ($colsRes) {
        $cols = [];
        while ($r = $colsRes->fetch_assoc()) { $cols[strtolower($r['COLUMN_NAME'])] = true; }
        if (!isset($cols['latitude'])) {
            if (!$conn->query("ALTER TABLE ngo_registrations ADD COLUMN latitude DECIMAL(10,8) NULL")) {
                throw new Exception("Failed adding latitude column: " . $conn->error);
            }
        }
        if (!isset($cols['longitude'])) {
            if (!$conn->query("ALTER TABLE ngo_registrations ADD COLUMN longitude DECIMAL(11,8) NULL")) {
                throw new Exception("Failed adding longitude column: " . $conn->error);
            }
        }
    }
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Database setup failed: " . $e->getMessage()]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $ngo_name = $conn->real_escape_string($_POST['ngo_name'] ?? '');
    $contact_person = $conn->real_escape_string($_POST['contact_person'] ?? '');
    $phone_number = $conn->real_escape_string($_POST['phone_number'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');
    $operating_hours = $conn->real_escape_string($_POST['operating_hours'] ?? '');
    $latitude = isset($_POST['latitude']) && $_POST['latitude'] !== '' ? floatval($_POST['latitude']) : 'NULL';
    $longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? floatval($_POST['longitude']) : 'NULL';
    
    // Validate required fields
    if (empty($ngo_name) || empty($contact_person) || empty($phone_number) || empty($email) || empty($address) || empty($operating_hours)) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }
    
    // Check if email already exists
    $check_query = "SELECT id FROM ngo_registrations WHERE email = '$email'";
    $result = $conn->query($check_query);
    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "An NGO with this email is already registered"]);
        exit;
    }
    
    // Insert data
    $lat_val = ($latitude === 'NULL') ? 'NULL' : $latitude;
    $lon_val = ($longitude === 'NULL') ? 'NULL' : $longitude;
    
    $insert_query = "INSERT INTO ngo_registrations (ngo_name, contact_person, phone_number, email, address, operating_hours, latitude, longitude) 
                     VALUES ('$ngo_name', '$contact_person', '$phone_number', '$email', '$address', '$operating_hours', $lat_val, $lon_val)";
    
    if ($conn->query($insert_query)) {
        echo json_encode([
            "success" => true, 
            "message" => "NGO registered successfully!",
            "data" => ["id" => $conn->insert_id]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}

$conn->close();
?>