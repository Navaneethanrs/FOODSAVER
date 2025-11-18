<?php
// Test data storage for Food Saver
$host = 'localhost';
$dbname = 'food_saver';
$username = 'root';
$password = '';

echo "<h2>🧪 Testing Data Storage</h2>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} catch(PDOException $e) {
    die("<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>");
}

// Check if tables exist
$tables = ['donations', 'ngo_registrations'];
foreach ($tables as $table) {
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "<p style='color: green;'>✅ Table '$table' exists with $count records</p>";
    } catch(PDOException $e) {
        echo "<p style='color: red;'>❌ Table '$table' not found</p>";
    }
}

// Test donation insertion
echo "<h3>🍽️ Testing Donation Storage</h3>";
try {
    $stmt = $pdo->prepare("INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute(['Test Donor', 'Test Food', '10 plates', 'Test Location', '1234567890', 'anytime']);
    echo "<p style='color: green;'>✅ Test donation inserted successfully</p>";
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Donation insertion failed: " . $e->getMessage() . "</p>";
}

// Test NGO insertion
echo "<h3>🏢 Testing NGO Storage</h3>";
try {
    $stmt = $pdo->prepare("INSERT INTO ngo_registrations (ngo_name, contact_person, phone_number, email, address, operating_hours) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute(['Test NGO', 'Test Person', '1234567890', 'test@ngo.com', 'Test Address', '9 AM - 5 PM']);
    echo "<p style='color: green;'>✅ Test NGO inserted successfully</p>";
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ NGO insertion failed: " . $e->getMessage() . "</p>";
}

// Show recent data
echo "<h3>📊 Recent Data</h3>";
try {
    $donations = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    echo "<h4>Recent Donations:</h4>";
    foreach ($donations as $donation) {
        echo "<p>• {$donation['donor_name']} - {$donation['food_type']} - {$donation['location']}</p>";
    }
    
    $ngos = $pdo->query("SELECT * FROM ngo_registrations ORDER BY registration_date DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    echo "<h4>Recent NGOs:</h4>";
    foreach ($ngos as $ngo) {
        echo "<p>• {$ngo['ngo_name']} - {$ngo['contact_person']} - {$ngo['email']}</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Error fetching data: " . $e->getMessage() . "</p>";
}

echo "<h3>🔧 Quick Actions</h3>";
echo "<a href='donate.html' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Test Donation Form</a>";
echo "<a href='registerngo.html' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Test NGO Form</a>";
echo "<a href='check_data.php' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Check All Data</a>";
?>