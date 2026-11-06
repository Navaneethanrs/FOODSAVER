<?php
header('Content-Type: application/json');

// For testing without actual emails, set this to true
$TEST_MODE = true; // Change to false when ready to send real emails

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_saver";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $donor_name = htmlspecialchars(trim($_POST['donor_name']));
    $food_type = htmlspecialchars(trim($_POST['food_type']));
    $quantity = htmlspecialchars(trim($_POST['quantity']));
    $location = htmlspecialchars(trim($_POST['location']));
    $donor_contact = htmlspecialchars(trim($_POST['donor_contact']));
    $pickup_time = htmlspecialchars(trim($_POST['pickup_time']));

    // Validate required fields
    if (empty($donor_name) || empty($food_type) || empty($quantity) || empty($location) || empty($donor_contact) || empty($pickup_time)) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit();
    }

    // Validate phone number
    if (!preg_match('/^[0-9]{10}$/', $donor_contact)) {
        echo json_encode(["success" => false, "message" => "Please enter a valid 10-digit phone number"]);
        exit();
    }

    try {
        // Save donation to database
        $stmt = $conn->prepare("INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time]);

        // Find matching NGOs by location
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

        // Process NGO notifications
        $emails_sent = 0;
        if (!empty($matching_ngos)) {
            foreach ($matching_ngos as $ngo) {
                if ($TEST_MODE) {
                    // In test mode, just log the email instead of sending
                    logEmailNotification($ngo, $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time);
                    $emails_sent++;
                } else {
                    // Send actual email (requires SMTP configuration)
                    if (sendEmailToNGO($ngo, $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time)) {
                        $emails_sent++;
                    }
                }
            }
        }

        if ($emails_sent > 0) {
            $message = $TEST_MODE ? 
                "Donation submitted! $emails_sent NGO(s) found (TEST MODE)" :
                "Donation submitted! $emails_sent NGO(s) notified.";
            echo json_encode(["success" => true, "message" => $message]);
        } else {
            echo json_encode(["success" => true, "message" => "Donation submitted! No matching NGOs found."]);
        }

    } catch(PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error saving donation"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}

function logEmailNotification($ngo, $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time) {
    $log_entry = date('Y-m-d H:i:s') . " - EMAIL NOTIFICATION\n";
    $log_entry .= "To: {$ngo['ngo_name']} ({$ngo['email']})\n";
    $log_entry .= "Donor: $donor_name ($donor_contact)\n";
    $log_entry .= "Food: $food_type - $quantity\n";
    $log_entry .= "Location: $location\n";
    $log_entry .= "Pickup Time: $pickup_time\n";
    $log_entry .= "---\n\n";
    
    file_put_contents('email_notifications.log', $log_entry, FILE_APPEND);
}

function sendEmailToNGO($ngo, $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time) {
    // This function would contain actual email sending logic
    // For now, it just returns true to simulate successful sending
    return true;
}

$conn = null;
?>