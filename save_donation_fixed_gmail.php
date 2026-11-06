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
        $stmt = $conn->prepare("INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
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

        // Send emails
        $emails_sent = 0;
        if (!empty($matching_ngos)) {
            require_once 'PHPMailer/PHPMailer-master/src/PHPMailer.php';
            require_once 'PHPMailer/PHPMailer-master/src/SMTP.php';
            require_once 'PHPMailer/PHPMailer-master/src/Exception.php';

            foreach ($matching_ngos as $ngo) {
                if (sendReliableEmail($ngo, $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time, $donation_id)) {
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

function sendReliableEmail($ngo, $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time, $donation_id) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Enhanced SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'foodsaver71@gmail.com';
        $mail->Password = 'hpvgedgupcruhtxc';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Additional settings for better delivery
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Timeout = 60;
        $mail->SMTPKeepAlive = true;

        // Email content with better formatting
        $mail->setFrom('foodsaver71@gmail.com', 'Food Saver System');
        $mail->addAddress($ngo['email'], $ngo['contact_person']);
        $mail->addReplyTo('foodsaver71@gmail.com', 'Food Saver');
        
        $mail->isHTML(true);
        $mail->Subject = 'New Food Donation Available - ' . $location;
        
        $maps_url = 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($location);
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #ff6347; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .details { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #ff6347; }
                .footer { text-align: center; padding: 20px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🍽️ New Food Donation Available!</h1>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$ngo['contact_person']}</strong>,</p>
                    <p>A new food donation is available in your area and needs immediate pickup:</p>
                    
                    <div class='details'>
                        <h3>Donation Details:</h3>
                        <p><strong>👤 Donor:</strong> $donor_name</p>
                        <p><strong>🍽️ Food Type:</strong> $food_type</p>
                        <p><strong>📦 Quantity:</strong> $quantity</p>
                        <p><strong>📍 Location:</strong> <a href='$maps_url' style='color: #1a73e8; text-decoration: none;'>$location</a></p>
                        <p><strong>📞 Contact:</strong> $donor_contact</p>
                        <p><strong>⏰ Pickup Time:</strong> $pickup_time</p>
                    </div>
                    
                    <div style='text-align: center; margin: 20px 0;'>
                        <a href='http://localhost/foodsaver/ngo_response.php?action=accept&donation_id=$donation_id&ngo_id={$ngo['id']}' 
                           style='background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>
                           ✅ ACCEPT DONATION
                        </a>
                        <a href='http://localhost/foodsaver/ngo_response.php?action=reject&donation_id=$donation_id&ngo_id={$ngo['id']}' 
                           style='background: #dc3545; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>
                           ❌ REJECT DONATION
                        </a>
                    </div>
                    
                    <p><strong>Action Required:</strong> Click ACCEPT if you can collect this donation, or contact the donor at <strong>$donor_contact</strong>.</p>
                    <p><small>⚠️ First NGO to accept gets the donation. Others will be automatically notified.</small></p>
                    
                    <p>Thank you for your service to the community!</p>
                </div>
                <div class='footer'>
                    <p>Best regards,<br><strong>Food Saver Team</strong></p>
                    <p><small>This is an automated notification. Please do not reply to this email.</small></p>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->send();
        
        // Log success with more details
        $log_entry = date('Y-m-d H:i:s') . " - SUCCESS: Email sent to {$ngo['email']} ({$ngo['contact_person']}) for donation in $location\n";
        file_put_contents('email_log.txt', $log_entry, FILE_APPEND);
        
        return true;
        
    } catch (Exception $e) {
        // Detailed error logging
        $log_entry = date('Y-m-d H:i:s') . " - ERROR: Failed to send to {$ngo['email']} - " . $e->getMessage() . "\n";
        file_put_contents('email_log.txt', $log_entry, FILE_APPEND);
        
        return false;
    }
}

$conn = null;
?>