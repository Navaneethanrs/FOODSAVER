<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "food_saver";
    $port = 3307; // Your XAMPP MySQL port

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

    // Validate required fields
    if (empty($donor_name) || empty($food_type) || empty($quantity) || empty($location) || empty($donor_contact) || empty($pickup_time)) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit();
    }

    try {
        // Save donation to database
        $stmt = $conn->prepare("INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time]);
        $donation_id = $conn->lastInsertId();

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

        // Send emails to matching NGOs
        $emails_sent = 0;
        if (!empty($matching_ngos)) {
            require_once 'PHPMailer/PHPMailer-master/src/PHPMailer.php';
            require_once 'PHPMailer/PHPMailer-master/src/SMTP.php';
            require_once 'PHPMailer/PHPMailer-master/src/Exception.php';

            foreach ($matching_ngos as $ngo) {
                if (sendEmailToNGO($ngo, $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time)) {
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
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}

function sendEmailToNGO($ngo, $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'foodsaver71@gmail.com'; // Your Gmail
        $mail->Password = 'hpvgedgupcruhtxc'; // Your 16-character app password (no spaces)
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email content
        $mail->setFrom('foodsaver71@gmail.com', 'Food Saver');
        $mail->addAddress($ngo['email'], $ngo['contact_person']);
        $mail->isHTML(true);
        $mail->Subject = '🍽️ New Food Donation Available - Food Saver';
        
        $maps_url = 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($location);
        
        $mail->Body = "
        <h2>New Food Donation Available!</h2>
        <p>Dear {$ngo['contact_person']},</p>
        <p>A new food donation is available in your area:</p>
        <ul>
            <li><strong>Donor:</strong> $donor_name</li>
            <li><strong>Food Type:</strong> $food_type</li>
            <li><strong>Quantity:</strong> $quantity</li>
            <li><strong>Location:</strong> <a href='$maps_url' style='color: #1a73e8; text-decoration: none;'>$location</a></li>
            <li><strong>Contact:</strong> $donor_contact</li>
            <li><strong>Pickup Time:</strong> $pickup_time</li>
        </ul>
        <p>Please contact the donor directly to arrange pickup.</p>
        <p>Best regards,<br>Food Saver Team</p>
        ";

        $mail->send();
        
        // Log success
        file_put_contents('email_log.txt', date('Y-m-d H:i:s') . " - SUCCESS: Email sent to {$ngo['email']}\n", FILE_APPEND);
        return true;
        
    } catch (Exception $e) {
        // Log error
        file_put_contents('email_log.txt', date('Y-m-d H:i:s') . " - ERROR: {$e->getMessage()}\n", FILE_APPEND);
        return false;
    }
}

$conn = null;
?>