<?php
// Debug script to check database status

echo "<h2>Database Debug Information</h2>";

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_saver";

echo "<h3>1. Testing Database Connection</h3>";

// Test connection without database first
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Connection failed: " . $conn->connect_error . "</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ MySQL connection successful</p>";
}

// Check if database exists
$result = $conn->query("SHOW DATABASES LIKE '$dbname'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Database '$dbname' exists</p>";
} else {
    echo "<p style='color: red;'>❌ Database '$dbname' does not exist</p>";
    echo "<p>Creating database...</p>";
    $conn->query("CREATE DATABASE $dbname");
    echo "<p style='color: green;'>✅ Database created</p>";
}

// Select the database
$conn->select_db($dbname);

echo "<h3>2. Checking Table</h3>";

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'ngo_registrations'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Table 'ngo_registrations' exists</p>";
} else {
    echo "<p style='color: red;'>❌ Table 'ngo_registrations' does not exist</p>";
    echo "<p>Creating table...</p>";
    
    $sql = "CREATE TABLE ngo_registrations (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        ngo_name VARCHAR(255) NOT NULL,
        contact_person VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        address TEXT NOT NULL,
        operating_hours VARCHAR(100) NOT NULL,
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✅ Table created successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Error creating table: " . $conn->error . "</p>";
    }
}

echo "<h3>3. Checking Table Structure</h3>";

$result = $conn->query("DESCRIBE ngo_registrations");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>4. Checking Data</h3>";

$result = $conn->query("SELECT COUNT(*) as count FROM ngo_registrations");
$row = $result->fetch_assoc();
echo "<p>Total records in table: <strong>" . $row['count'] . "</strong></p>";

if ($row['count'] > 0) {
    echo "<h4>Recent Registrations:</h4>";
    $result = $conn->query("SELECT * FROM ngo_registrations ORDER BY registration_date DESC LIMIT 5");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>NGO Name</th><th>Contact</th><th>Email</th><th>Date</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['ngo_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['contact_person']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . $row['registration_date'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ No data found in the table</p>";
}

$conn->close();

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ul>";
echo "<li><a href='registerngo.html'>Try registering a new NGO</a></li>";
echo "<li><a href='view_ngos.php'>View NGOs page</a></li>";
echo "<li><a href='setup_database.php'>Database setup page</a></li>";
echo "</ul>";
?>
