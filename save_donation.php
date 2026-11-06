<?php
// save_donation.php - Enhanced with NGO notification system

// Database connection
$servername = "127.0.0.1";
$username = "root"; // Change as per your setup
$password = ""; // Change as per your setup  
$dbname = "food_saver"; // Change as per your database name
$port = 3307; // XAMPP Control Panel shows MySQL on port 3307

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$donor_name = $_POST['donor_name'] ?? '';
$food_type = $_POST['food_type'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$location = $_POST['location'] ?? '';
$donor_contact = $_POST['donor_contact'] ?? '';
$pickup_time = $_POST['pickup_time'] ?? '';
$latitude = $_POST['latitude'] ?? '';
$longitude = $_POST['longitude'] ?? '';
$location_accuracy = $_POST['location_accuracy'] ?? '';
$exact_building = $_POST['exact_building'] ?? '';

// Validate required fields
if (empty($donor_name) || empty($food_type) || empty($quantity) || empty($location) || empty($donor_contact)) {
    $response = [
        'success' => false,
        'message' => 'All required fields must be filled.'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Insert donation into database
$sql = "INSERT INTO donations (donor_name, food_type, quantity, location, contact, pickup_time, latitude, longitude, location_accuracy, exact_building) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssddss", $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time, $latitude, $longitude, $location_accuracy, $exact_building);

if ($stmt->execute()) {
    $donation_id = $stmt->insert_id;
    
    // Find nearby NGOs and send notifications
    $notification_result = notifyNearbyNGOs($donation_id, $location, $conn);
    
    $response = [
        'success' => true,
        'message' => 'Donation submitted successfully! ' . $notification_result['message'],
        'donation_id' => $donation_id,
        'notified_ngos' => $notification_result['notified_count'],
        'emails_sent' => $notification_result['emails_sent']
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Error: ' . $stmt->error
    ];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);

// Function to find and notify nearby NGOs
function notifyNearbyNGOs($donation_id, $donation_location, $conn) {
    // Extract location keywords for matching
    $location_keywords = extractLocationKeywords($donation_location);
    
    // Find NGOs in the same area
    $ngos = findNearbyNGOs($location_keywords, $conn);
    
    $notified_count = 0;
    $emails_sent = 0;
    
    if (!empty($ngos)) {
        foreach ($ngos as $ngo) {
            if (sendEmailNotification($ngo, $donation_id, $conn)) {
                $emails_sent++;
            }
            $notified_count++;
            
            // Also log the notification in database
            logNGONotification($donation_id, $ngo['id'], $conn);
        }
    }
    
    return [
        'notified_count' => $notified_count,
        'emails_sent' => $emails_sent,
        'message' => $notified_count > 0 ? 
            "{$notified_count} nearby NGO(s) have been notified." : 
            "No nearby NGOs found. We'll notify you when we find matches."
    ];
}

// Function to extract location keywords for matching
function extractLocationKeywords($location) {
    $keywords = [];
    $location = strtolower($location);
    
    // Common location patterns for Tamil Nadu area
    $common_areas = [
        'perundurai', 'erode', 'salem', 'tamil nadu', 'india',
        'kongu engineering college', 'kongu', 'engineering college', 'kec',
        'annachanapatti', 'amadhanapatti', 'anna chanapatti', 'amad hanapatti'
    ];
    
    foreach ($common_areas as $area) {
        if (strpos($location, strtolower($area)) !== false) {
            $keywords[] = $area;
        }
    }
    
    // Also add individual words
    $words = explode(' ', $location);
    $keywords = array_merge($keywords, $words);
    
    return array_unique(array_filter($keywords));
}

// Function to find NGOs in nearby locations
function findNearbyNGOs($location_keywords, $conn) {
    if (empty($location_keywords)) {
        return [];
    }
    
    $conditions = [];
    $params = [];
    
    foreach ($location_keywords as $keyword) {
        $conditions[] = "address LIKE ?";
        $params[] = '%' . $keyword . '%';
    }
    
    $sql = "SELECT * FROM ngos WHERE " . implode(" OR ", $conditions) . " ORDER BY registration_date DESC";
    
    $stmt = $conn->prepare($sql);
    
    // Bind parameters for LIKE conditions
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $ngos = [];
    while ($row = $result->fetch_assoc()) {
        $ngos[] = $row;
    }
    
    $stmt->close();
    return $ngos;
}

// Function to send email notification to NGO
function sendEmailNotification($ngo, $donation_id, $conn) {
    // Get donation details
    $donation_sql = "SELECT * FROM donations WHERE id = ?";
    $donation_stmt = $conn->prepare($donation_sql);
    $donation_stmt->bind_param("i", $donation_id);
    $donation_stmt->execute();
    $donation_result = $donation_stmt->get_result();
    $donation = $donation_result->fetch_assoc();
    $donation_stmt->close();
    
    // Include PHPMailer
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    require 'PHPMailer/src/Exception.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'foodsaver71@gmail.com';
        $mail->Password = 'lwdc honl fimd nrgm';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('foodsaver71@gmail.com', 'Food Saver');
        $mail->addAddress($ngo['email'], $ngo['contact_person']);
        $mail->isHTML(true);
        $mail->Subject = '🚨 New Food Donation Available - Food Saver';
        
        // Create Google Maps link
        $maps_url = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($donation['location']);
        
        $message = "Hello {$ngo['contact_person']},<br><br>New donation: {$donation['food_type']} - {$donation['quantity']}<br>Location: <a href='{$maps_url}' style='color: #1a73e8; text-decoration: none;'>{$donation['location']}</a><br>Contact: {$donation['contact']}";
        $mail->Body = $message;
        
        $mail->send();
        file_put_contents('email_debug.log', "SENT to: {$ngo['email']}\n", FILE_APPEND);
        return true;
        
    } catch (Exception $e) {
        file_put_contents('email_debug.log', "FAILED: {$ngo['email']} - " . $e->getMessage() . "\n", FILE_APPEND);
        return false;
    }
}

// Function to log NGO notifications
function logNGONotification($donation_id, $ngo_id, $conn) {
    $sql = "INSERT INTO ngo_notifications (donation_id, ngo_id, notified_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $donation_id, $ngo_id);
    $stmt->execute();
    $stmt->close();
}
?>