<?php
require_once 'PHPMailer/PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer/PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer/PHPMailer-master/src/Exception.php';

function sendRejectionEmails($donation, $accepting_ngo, $conn) {
    $location_keywords = explode(',', strtolower($donation['location']));
    $location_keywords = array_map('trim', $location_keywords);
    
    $ngo_query = "SELECT * FROM ngo_registrations WHERE id != ? AND (";
    $conditions = [];
    foreach ($location_keywords as $keyword) {
        if (!empty($keyword)) {
            $conditions[] = "LOWER(address) LIKE '%" . $conn->real_escape_string($keyword) . "%'";
        }
    }
    $ngo_query .= implode(' OR ', $conditions) . ")";
    
    $stmt = $conn->prepare($ngo_query);
    $stmt->bind_param("i", $accepting_ngo['id']);
    $stmt->execute();
    $other_ngos = $stmt->get_result();
    
    while ($ngo = $other_ngos->fetch_assoc()) {
        sendRejectionEmail($ngo, $donation, $accepting_ngo);
    }
}

function sendRejectionEmail($ngo, $donation, $accepting_ngo) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'foodsaver71@gmail.com';
        $mail->Password = 'hpvgedgupcruhtxc';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        $mail->setFrom('foodsaver71@gmail.com', 'Food Saver System');
        $mail->addAddress($ngo['email'], $ngo['contact_person']);
        
        $mail->isHTML(true);
        $mail->Subject = 'Donation Already Accepted - ' . $donation['location'];
        
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #dc3545; color: white; padding: 20px; text-align: center;'>
                <h1>🚫 Donation No Longer Available</h1>
            </div>
            <div style='padding: 20px; background: #f9f9f9;'>
                <p>Dear <strong>{$ngo['contact_person']}</strong>,</p>
                <p>The food donation in <strong>{$donation['location']}</strong> has been accepted by another NGO.</p>
                
                <div style='background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #dc3545;'>
                    <h3>Donation Details:</h3>
                    <p><strong>👤 Donor:</strong> {$donation['donor_name']}</p>
                    <p><strong>🍽️ Food Type:</strong> {$donation['food_type']}</p>
                    <p><strong>📦 Quantity:</strong> {$donation['quantity']}</p>
                    <p><strong>✅ Accepted by:</strong> {$accepting_ngo['ngo_name']}</p>
                </div>
                
                <p>Thank you for your quick response. We'll notify you of future donations in your area.</p>
            </div>
        </div>
        ";

        $mail->send();
        return true;
        
    } catch (Exception $e) {
        return false;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>NGO Response</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <?php
        if (isset($_GET['action']) && isset($_GET['donation_id']) && isset($_GET['ngo_id'])) {
            $action = $_GET['action'];
            $donation_id = $_GET['donation_id'];
            $ngo_id = $_GET['ngo_id'];
            
            $conn = new mysqli("localhost", "root", "", "food_saver", 3307);
            
            if ($conn->connect_error) {
                echo "<div class='alert alert-danger'>Connection failed: " . $conn->connect_error . "</div>";
            } else {
                // Get donation details
                $donation_query = "SELECT * FROM donations WHERE id = ?";
                $stmt = $conn->prepare($donation_query);
                $stmt->bind_param("i", $donation_id);
                $stmt->execute();
                $donation = $stmt->get_result()->fetch_assoc();
                
                // Get NGO details
                $ngo_query = "SELECT * FROM ngo_registrations WHERE id = ?";
                $stmt = $conn->prepare($ngo_query);
                $stmt->bind_param("i", $ngo_id);
                $stmt->execute();
                $ngo = $stmt->get_result()->fetch_assoc();
                
                if ($action == 'accept' && $donation && $donation['status'] == 'pending') {
                    // Update donation status
                    $update_sql = "UPDATE donations SET status = 'accepted', accepted_by = ?, accepted_at = NOW() WHERE id = ?";
                    $stmt = $conn->prepare($update_sql);
                    $stmt->bind_param("si", $ngo['ngo_name'], $donation_id);
                    $stmt->execute();
                    
                    // Send rejection emails to other NGOs
                    sendRejectionEmails($donation, $ngo, $conn);
                    
                    echo "<div class='alert alert-success'>
                            <h4>✅ Donation Accepted Successfully!</h4>
                            <p>Thank you <strong>{$ngo['ngo_name']}</strong> for accepting this donation.</p>
                            <p><strong>Donor Contact:</strong> {$donation['donor_contact']}</p>
                            <p><strong>Location:</strong> {$donation['location']}</p>
                            <p>Other NGOs have been automatically notified that this donation is no longer available.</p>
                          </div>";
                    
                } elseif ($action == 'reject') {
                    echo "<div class='alert alert-info'>
                            <h4>ℹ️ Donation Rejected</h4>
                            <p>You have rejected this donation. Thank you for your response.</p>
                          </div>";
                } else {
                    echo "<div class='alert alert-warning'>
                            <h4>⚠️ Donation No Longer Available</h4>
                            <p>This donation has already been accepted by another NGO.</p>
                          </div>";
                }
                
                $conn->close();
            }
        } else {
            echo "<div class='alert alert-danger'>Invalid request parameters.</div>";
        }
        ?>
        
        <div class="mt-4">
            <a href="view_donation_status.php" class="btn btn-primary">View All Donations</a>
            <a href="index.html" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</body>
</html>