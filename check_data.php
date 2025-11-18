<?php
// Quick Data Checker for Food Saver
$host = 'localhost';
$dbname = 'food_saver';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h2>✅ Database Connection: SUCCESS</h2>";
} catch(PDOException $e) {
    die("<h2>❌ Database Connection: FAILED</h2><p>" . $e->getMessage() . "</p>");
}

echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.success { color: #28a745; }
.warning { color: #ffc107; }
.danger { color: #dc3545; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background: #f8f9fa; }
.btn { padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; display: inline-block; }
.btn-primary { background: #007bff; color: white; }
.btn-success { background: #28a745; color: white; }
</style>";

echo "<div class='container'>";
echo "<h1>🔍 Food Saver Data Checker</h1>";

// Check tables exist
$tables = ['donations', 'ngos', 'ngo_registrations', 'ngo_notifications'];
echo "<h3>📊 Table Status</h3>";

foreach ($tables as $table) {
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "<p class='success'>✅ $table: $count records</p>";
    } catch(PDOException $e) {
        echo "<p class='danger'>❌ $table: Table not found</p>";
    }
}

// Show recent donations
echo "<h3>🍽️ Recent Donations (Last 5)</h3>";
try {
    $donations = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    if (count($donations) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Donor</th><th>Food Type</th><th>Quantity</th><th>Location</th><th>Contact</th><th>Status</th><th>Date</th></tr>";
        foreach ($donations as $donation) {
            echo "<tr>";
            echo "<td>" . $donation['id'] . "</td>";
            echo "<td>" . htmlspecialchars($donation['donor_name']) . "</td>";
            echo "<td>" . htmlspecialchars($donation['food_type']) . "</td>";
            echo "<td>" . htmlspecialchars($donation['quantity']) . "</td>";
            echo "<td>" . htmlspecialchars($donation['location']) . "</td>";
            echo "<td>" . htmlspecialchars($donation['donor_contact']) . "</td>";
            echo "<td>" . $donation['status'] . "</td>";
            echo "<td>" . $donation['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No donations found. Try submitting a test donation.</p>";
    }
} catch(PDOException $e) {
    echo "<p class='danger'>❌ Error fetching donations: " . $e->getMessage() . "</p>";
}

// Show recent NGO registrations
echo "<h3>🏢 Recent NGO Registrations (Last 5)</h3>";
try {
    $ngos = $pdo->query("SELECT * FROM ngo_registrations ORDER BY registration_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    if (count($ngos) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>NGO Name</th><th>Contact Person</th><th>Phone</th><th>Email</th><th>Date</th></tr>";
        foreach ($ngos as $ngo) {
            echo "<tr>";
            echo "<td>" . $ngo['id'] . "</td>";
            echo "<td>" . htmlspecialchars($ngo['ngo_name']) . "</td>";
            echo "<td>" . htmlspecialchars($ngo['contact_person']) . "</td>";
            echo "<td>" . htmlspecialchars($ngo['phone_number']) . "</td>";
            echo "<td>" . htmlspecialchars($ngo['email']) . "</td>";
            echo "<td>" . $ngo['registration_date'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No NGO registrations found. Try registering a test NGO.</p>";
    }
} catch(PDOException $e) {
    echo "<p class='danger'>❌ Error fetching NGO registrations: " . $e->getMessage() . "</p>";
}

// Show recent notifications
echo "<h3>📢 Recent Notifications (Last 5)</h3>";
try {
    $notifications = $pdo->query("SELECT * FROM ngo_notifications ORDER BY sent_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    if (count($notifications) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Donation ID</th><th>NGO Name</th><th>Phone</th><th>Status</th><th>Sent At</th></tr>";
        foreach ($notifications as $notification) {
            echo "<tr>";
            echo "<td>" . $notification['id'] . "</td>";
            echo "<td>" . $notification['donation_id'] . "</td>";
            echo "<td>" . htmlspecialchars($notification['ngo_name']) . "</td>";
            echo "<td>" . htmlspecialchars($notification['ngo_phone']) . "</td>";
            echo "<td>" . $notification['status'] . "</td>";
            echo "<td>" . $notification['sent_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No notifications found. Submit a donation to trigger notifications.</p>";
    }
} catch(PDOException $e) {
    echo "<p class='danger'>❌ Error fetching notifications: " . $e->getMessage() . "</p>";
}

echo "<h3>🔧 Quick Actions</h3>";
echo "<a href='donate.html' class='btn btn-success'>Test Donation Form</a>";
echo "<a href='registerngo.html' class='btn btn-primary'>Test NGO Registration</a>";
echo "<a href='admin_dashboard.php' class='btn btn-primary'>Full Admin Dashboard</a>";
echo "<a href='setup_database.php' class='btn btn-primary'>Setup Database</a>";

echo "<h3>📝 Test Instructions</h3>";
echo "<ol>";
echo "<li><strong>Test Donation:</strong> Go to <a href='donate.html'>donate.html</a> and submit a food donation</li>";
echo "<li><strong>Test NGO Registration:</strong> Go to <a href='registerngo.html'>registerngo.html</a> and register an NGO</li>";
echo "<li><strong>Check Results:</strong> Refresh this page to see if data was stored</li>";
echo "<li><strong>View Full Dashboard:</strong> Go to <a href='admin_dashboard.php'>admin_dashboard.php</a> for detailed view</li>";
echo "</ol>";

echo "</div>";
?>