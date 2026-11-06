<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "food_saver";
    $port = 3307;

    try {
        $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit();
    }

    // Get form data
    $donor_name = htmlspecialchars(trim($_POST['donor_name']));
    $food_type = htmlspecialchars(trim($_POST['food_type']));
    $quantity = htmlspecialchars(trim($_POST['quantity']));
    $location = htmlspecialchars(trim($_POST['location']));
    $donor_contact = htmlspecialchars(trim($_POST['donor_contact']));
    $pickup_time = htmlspecialchars(trim($_POST['pickup_time']));

    if (empty($donor_name) || empty($food_type) || empty($quantity) || empty($location) || empty($donor_contact) || empty($pickup_time)) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit();
    }

    try {
        // Save donation
        $stmt = $conn->prepare("INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time]);

        // Find matching NGOs
        $location_keywords = explode(',', strtolower($location));
        $location_keywords = array_map('trim', $location_keywords);
        
        $ngo_query = "SELECT * FROM ngo_registrations WHERE ";
        $conditions = [];
        $params = [];
        
        foreach ($location_keywords as $keyword) {
            if (!empty($keyword)) {
                $conditions[] = "LOWER(address) LIKE ?";
                $params[] = "%$keyword%";
            }
        }
        
        if (!empty($conditions)) {
            $ngo_query .= implode(' OR ', $conditions);
            $ngo_stmt = $conn->prepare($ngo_query);
            $ngo_stmt->execute($params);
            $matching_ngos = $ngo_stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $matching_ngos = [];
        }

        // Send emails using EmailJS API
        $emails_sent = 0;
        if (!empty($matching_ngos)) {
            foreach ($matching_ngos as $ngo) {
                if (sendEmailViaAPI($ngo, $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time)) {
                    $emails_sent++;
                }
            }
        }

        if ($emails_sent > 0) {
            echo json_encode(["success" => true, "message" => "Donation submitted! $emails_sent NGO(s) notified."]);
        } else {
            echo json_encode(["success" => true, "message" => "Donation submitted! No matching NGOs found."]);
        }

    } catch(PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error saving donation"]);
    }
}

function sendEmailViaAPI($ngo, $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time) {
    // Using EmailJS API (free service)
    $api_url = "https://api.emailjs.com/api/v1.0/email/send";
    
    $data = [
        'service_id' => 'default_service',
        'template_id' => 'template_donation',
        'user_id' => 'your_user_id',
        'template_params' => [
            'to_email' => $ngo['email'],
            'to_name' => $ngo['contact_person'],
            'donor_name' => $donor_name,
            'food_type' => $food_type,
            'quantity' => $quantity,
            'location' => $location,
            'donor_contact' => $donor_contact,
            'pickup_time' => $pickup_time
        ]
    ];
    
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $success = ($http_code == 200);
    
    // Log result
    $log_entry = date('Y-m-d H:i:s') . " - " . ($success ? "SUCCESS" : "FAILED") . ": API Email to {$ngo['email']} (HTTP: $http_code)\n";
    file_put_contents('email_log.txt', $log_entry, FILE_APPEND);
    
    return $success;
}

$conn = null;
?>