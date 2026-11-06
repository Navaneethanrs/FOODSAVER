<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_saver";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor_name = $_POST['donor_name'];
    $food_type = $_POST['food_type'];
    $quantity = $_POST['quantity'];
    $location = $_POST['location'];
    $donor_contact = $_POST['donor_contact'];
    $pickup_time = $_POST['pickup_time'];

    // Save donation
    $stmt = $conn->prepare("INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time]);

    // Find NGOs
    $location_parts = explode(',', strtolower($location));
    $search_term = '%' . trim($location_parts[0]) . '%';
    
    $ngo_stmt = $conn->prepare("SELECT * FROM ngo_registrations WHERE LOWER(address) LIKE ?");
    $ngo_stmt->execute([$search_term]);
    $ngos = $ngo_stmt->fetchAll(PDO::FETCH_ASSOC);

    $emails_sent = 0;
    foreach ($ngos as $ngo) {
        if (sendEmail($ngo['email'], $ngo['ngo_name'], $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time)) {
            $emails_sent++;
        }
    }

    echo "<script>alert('Donation submitted! $emails_sent NGO(s) found and notified.'); window.location='donate.html';</script>";
}

function sendEmail($to, $ngo_name, $donor_name, $food_type, $quantity, $location, $donor_contact, $pickup_time) {
    $subject = "New Food Donation Available - Food Saver";
    
    $message = "Dear $ngo_name,\n\n";
    $message .= "A new food donation is available in your area:\n\n";
    $message .= "Donor: $donor_name\n";
    $message .= "Contact: $donor_contact\n";
    $message .= "Food Type: $food_type\n";
    $message .= "Quantity: $quantity\n";
    $message .= "Location: $location\n";
    $message .= "Pickup Time: $pickup_time\n\n";
    $message .= "Please contact the donor directly to coordinate pickup.\n\n";
    $message .= "Thank you,\nFood Saver Team";

    $headers = "From: foodsaver81@gmail.com\r\n";
    $headers .= "Reply-To: foodsaver81@gmail.com\r\n";

    return mail($to, $subject, $message, $headers);
}
?>