<!DOCTYPE html>
<html>
<head>
    <title>Donation Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <?php
        if ($_POST) {
            $conn = new mysqli("localhost", "root", "", "", 3307);
            
            if ($conn->connect_error) {
                echo "<div class='alert alert-danger'>Connection failed. Make sure MySQL is running in XAMPP.</div>";
            } else {
                $conn->query("CREATE DATABASE IF NOT EXISTS food_saver");
                $conn->select_db("food_saver");
                
                $conn->query("CREATE TABLE IF NOT EXISTS donations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    donor_name VARCHAR(255),
                    food_type VARCHAR(255),
                    quantity VARCHAR(255),
                    location TEXT,
                    donor_contact VARCHAR(20),
                    pickup_time VARCHAR(50),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                
                $name = $conn->real_escape_string($_POST['donor_name']);
                $food = $conn->real_escape_string($_POST['food_type']);
                $qty = $conn->real_escape_string($_POST['quantity']);
                $loc = $conn->real_escape_string($_POST['location']);
                $contact = $conn->real_escape_string($_POST['donor_contact']);
                $time = $conn->real_escape_string($_POST['pickup_time']);
                
                $sql = "INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time, status) 
                        VALUES ('$name', '$food', '$qty', '$loc', '$contact', '$time', 'pending')";
                
                if ($conn->query($sql)) {
                    $donation_id = $conn->insert_id;
                    // Find matching NGOs
                    $location_keywords = explode(',', strtolower($loc));
                    $location_keywords = array_map('trim', $location_keywords);
                    
                    $ngo_query = "SELECT * FROM ngo_registrations WHERE ";
                    $conditions = [];
                    
                    foreach ($location_keywords as $keyword) {
                        if (!empty($keyword)) {
                            $conditions[] = "LOWER(address) LIKE '%" . $conn->real_escape_string($keyword) . "%'";
                        }
                    }
                    
                    $emails_sent = 0;
                    if (!empty($conditions)) {
                        $ngo_query .= implode(' OR ', $conditions);
                        $ngo_result = $conn->query($ngo_query);
                        
                        if ($ngo_result && $ngo_result->num_rows > 0) {
                            require_once 'PHPMailer/PHPMailer-master/src/PHPMailer.php';
                            require_once 'PHPMailer/PHPMailer-master/src/SMTP.php';
                            require_once 'PHPMailer/PHPMailer-master/src/Exception.php';
                            
                            while ($ngo = $ngo_result->fetch_assoc()) {
                                if (sendEmail($ngo, $name, $food, $qty, $loc, $contact, $time, $donation_id)) {
                                    $emails_sent++;
                                }
                            }
                        }
                    }
                    
                    echo "<div class='alert alert-success'>
                            <h4>✅ Donation Saved Successfully!</h4>
                            <p>Thank you <strong>$name</strong> for your donation!</p>
                            <p><strong>Food:</strong> $food</p>
                            <p><strong>Quantity:</strong> $qty</p>
                            <p><strong>Location:</strong> $loc</p>";
                    
                    if ($emails_sent > 0) {
                        echo "<p><strong>📧 Emails sent to $emails_sent NGO(s) in your area!</strong></p>";
                    } else {
                        echo "<p><strong>ℹ️ No matching NGOs found in your area.</strong></p>";
                    }
                    
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error saving donation: " . $conn->error . "</div>";
                }
                
                $conn->close();
            }
        }
        
        function sendEmail($ngo, $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time, $donation_id) {
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
                $mail->Subject = 'New Food Donation Available - ' . $location;
                
                $maps_url = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($location);
                
                $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: #ff6347; color: white; padding: 20px; text-align: center;'>
                        <h1>🍽️ New Food Donation Available!</h1>
                    </div>
                    <div style='padding: 20px; background: #f9f9f9;'>
                        <p>Dear <strong>{$ngo['contact_person']}</strong>,</p>
                        <p>A new food donation is available in your area:</p>
                        
                        <div style='background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #ff6347;'>
                            <h3>Donation Details:</h3>
                            <p><strong>👤 Donor:</strong> $donor_name</p>
                            <p><strong>🍽️ Food Type:</strong> $food_type</p>
                            <p><strong>📦 Quantity:</strong> $quantity</p>
                            <p><strong>📍 Location:</strong> <a href='$maps_url' style='color: #1a73e8; text-decoration: none; font-weight: bold;'>$location 📍</a></p>
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
        
        <a href="donate_basic.html" class="btn btn-primary">Make Another Donation</a>
        <a href="view_donations.php" class="btn btn-secondary">View All Donations</a>
        <a href="view_ngos.php" class="btn btn-info">View Registered NGOs</a>
    </div>
</body>
</html>