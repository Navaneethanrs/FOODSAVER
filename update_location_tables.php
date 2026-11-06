<?php
// Update database tables to include latitude and longitude columns
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_saver";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🗺️ Updating Database for Location Coordinates</h2>";
    
    // Add latitude and longitude to ngo_registrations table
    $sql = "ALTER TABLE ngo_registrations 
            ADD COLUMN latitude DECIMAL(10, 8) NULL,
            ADD COLUMN longitude DECIMAL(11, 8) NULL";
    
    try {
        $conn->exec($sql);
        echo "<p style='color: green;'>✅ Added latitude/longitude columns to ngo_registrations table</p>";
    } catch(PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<p style='color: blue;'>ℹ️ Latitude/longitude columns already exist in ngo_registrations table</p>";
        } else {
            echo "<p style='color: red;'>❌ Error updating ngo_registrations: " . $e->getMessage() . "</p>";
        }
    }
    
    // Add latitude and longitude to donations table
    $sql = "ALTER TABLE donations 
            ADD COLUMN latitude DECIMAL(10, 8) NULL,
            ADD COLUMN longitude DECIMAL(11, 8) NULL";
    
    try {
        $conn->exec($sql);
        echo "<p style='color: green;'>✅ Added latitude/longitude columns to donations table</p>";
    } catch(PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<p style='color: blue;'>ℹ️ Latitude/longitude columns already exist in donations table</p>";
        } else {
            echo "<p style='color: red;'>❌ Error updating donations: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<h3>📊 Updated Table Structure</h3>";
    
    // Show ngo_registrations structure
    $result = $conn->query("DESCRIBE ngo_registrations");
    echo "<h4>NGO Registrations Table:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show donations structure
    $result = $conn->query("DESCRIBE donations");
    echo "<h4>Donations Table:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>✅ Database Update Complete!</h3>";
    echo "<p>Both forms now support automatic location detection with GPS coordinates.</p>";
    echo "<p><strong>Features:</strong></p>";
    echo "<ul>";
    echo "<li>📍 Automatic GPS coordinate capture</li>";
    echo "<li>🗺️ Precise location storage in database</li>";
    echo "<li>📱 Works on mobile and desktop browsers</li>";
    echo "<li>🔒 User permission required for location access</li>";
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}
?>