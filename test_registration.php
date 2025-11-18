<?php
// Test script to manually insert NGO data and check if it works

echo "<h2>Testing NGO Registration Process</h2>";

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_saver";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select the database
$conn->select_db($dbname);

echo "<h3>1. Testing Manual Insert</h3>";

// Test data
$test_ngo_name = "Test NGO " . date('Y-m-d H:i:s');
$test_contact_person = "Test Contact";
$test_phone_number = "1234567890";
$test_email = "test" . time() . "@example.com";
$test_address = "Test Address";
$test_operating_hours = "9 AM - 5 PM";

// Insert test data
$sql = "INSERT INTO ngo_registrations (ngo_name, contact_person, phone_number, email, address, operating_hours) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $test_ngo_name, $test_contact_person, $test_phone_number, $test_email, $test_address, $test_operating_hours);

if ($stmt->execute()) {
    echo "<p style='color: green;'>✅ Test data inserted successfully!</p>";
    echo "<p>Inserted ID: " . $conn->insert_id . "</p>";
} else {
    echo "<p style='color: red;'>❌ Error inserting test data: " . $stmt->error . "</p>";
}

$stmt->close();

echo "<h3>2. Checking Current Data</h3>";

$result = $conn->query("SELECT COUNT(*) as count FROM ngo_registrations");
$row = $result->fetch_assoc();
echo "<p>Total records in table: <strong>" . $row['count'] . "</strong></p>";

if ($row['count'] > 0) {
    echo "<h4>All Records:</h4>";
    $result = $conn->query("SELECT * FROM ngo_registrations ORDER BY registration_date DESC");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>NGO Name</th><th>Contact</th><th>Email</th><th>Phone</th><th>Date</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['ngo_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['contact_person']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
        echo "<td>" . $row['registration_date'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>3. Testing Form Data Processing</h3>";

// Simulate POST data
$_POST['ngo_name'] = "Form Test NGO";
$_POST['contact_person'] = "Form Contact";
$_POST['phone_number'] = "9876543210";
$_POST['email'] = "formtest" . time() . "@example.com";
$_POST['address'] = "Form Test Address";
$_POST['operating_hours'] = "8 AM - 6 PM";

echo "<p>Simulating form submission with data:</p>";
echo "<ul>";
echo "<li>NGO Name: " . $_POST['ngo_name'] . "</li>";
echo "<li>Contact: " . $_POST['contact_person'] . "</li>";
echo "<li>Phone: " . $_POST['phone_number'] . "</li>";
echo "<li>Email: " . $_POST['email'] . "</li>";
echo "<li>Address: " . $_POST['address'] . "</li>";
echo "<li>Hours: " . $_POST['operating_hours'] . "</li>";
echo "</ul>";

// Process the data like the registration script
$ngo_name = $conn->real_escape_string($_POST['ngo_name']);
$contact_person = $conn->real_escape_string($_POST['contact_person']);
$phone_number = $conn->real_escape_string($_POST['phone_number']);
$email = $conn->real_escape_string($_POST['email']);
$address = $conn->real_escape_string($_POST['address']);
$operating_hours = $conn->real_escape_string($_POST['operating_hours']);

// Insert using the same method as registration script
$sql = "INSERT INTO ngo_registrations (ngo_name, contact_person, phone_number, email, address, operating_hours) 
        VALUES ('$ngo_name', '$contact_person', '$phone_number', '$email', '$address', '$operating_hours')";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✅ Form data processed and inserted successfully!</p>";
} else {
    echo "<p style='color: red;'>❌ Error processing form data: " . $conn->error . "</p>";
}

$conn->close();

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ul>";
echo "<li><a href='debug_database.php'>Check database again</a></li>";
echo "<li><a href='registerngo.html'>Try registration form</a></li>";
echo "<li><a href='view_ngos.php'>View NGOs page</a></li>";
echo "</ul>";
?>
