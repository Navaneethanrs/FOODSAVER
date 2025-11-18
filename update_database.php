<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_saver";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Updating Food Saver Database...</h2>";
    
    // Check if donations table exists, if not create it
    $stmt = $conn->prepare("SHOW TABLES LIKE 'donations'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $sql = "CREATE TABLE donations (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            donor_name VARCHAR(255) NOT NULL,
            food_type VARCHAR(255) NOT NULL,
            quantity VARCHAR(255) NOT NULL,
            location TEXT NOT NULL,
            donor_contact VARCHAR(20) NOT NULL,
            pickup_time VARCHAR(50) NOT NULL,
            latitude DECIMAL(10, 8) NULL,
            longitude DECIMAL(11, 8) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql);
        echo "<p style='color: green;'>✓ Created donations table with GPS coordinates</p>";
    } else {
        // Add latitude and longitude columns if they don't exist
        try {
            $conn->exec("ALTER TABLE donations ADD COLUMN latitude DECIMAL(10, 8) NULL");
            echo "<p style='color: green;'>✓ Added latitude column to donations table</p>";
        } catch(PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "<p style='color: orange;'>⚠ Latitude column already exists in donations table</p>";
            } else {
                throw $e;
            }
        }
        
        try {
            $conn->exec("ALTER TABLE donations ADD COLUMN longitude DECIMAL(11, 8) NULL");
            echo "<p style='color: green;'>✓ Added longitude column to donations table</p>";
        } catch(PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "<p style='color: orange;'>⚠ Longitude column already exists in donations table</p>";
            } else {
                throw $e;
            }
        }
    }
    
    // Check if ngo_registrations table exists, if not create it
    $stmt = $conn->prepare("SHOW TABLES LIKE 'ngo_registrations'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $sql = "CREATE TABLE ngo_registrations (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            ngo_name VARCHAR(255) NOT NULL,
            contact_person VARCHAR(255) NOT NULL,
            phone_number VARCHAR(20) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            address TEXT NOT NULL,
            operating_hours VARCHAR(100) NOT NULL,
            latitude DECIMAL(10, 8) NULL,
            longitude DECIMAL(11, 8) NULL,
            registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql);
        echo "<p style='color: green;'>✓ Created ngo_registrations table with GPS coordinates</p>";
    } else {
        // Add latitude and longitude columns if they don't exist
        try {
            $conn->exec("ALTER TABLE ngo_registrations ADD COLUMN latitude DECIMAL(10, 8) NULL");
            echo "<p style='color: green;'>✓ Added latitude column to ngo_registrations table</p>";
        } catch(PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "<p style='color: orange;'>⚠ Latitude column already exists in ngo_registrations table</p>";
            } else {
                throw $e;
            }
        }
        
        try {
            $conn->exec("ALTER TABLE ngo_registrations ADD COLUMN longitude DECIMAL(11, 8) NULL");
            echo "<p style='color: green;'>✓ Added longitude column to ngo_registrations table</p>";
        } catch(PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "<p style='color: orange;'>⚠ Longitude column already exists in ngo_registrations table</p>";
            } else {
                throw $e;
            }
        }
    }
    
    echo "<h3 style='color: green;'>✅ Database update completed successfully!</h3>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li>Go to <a href='donate.html'>donate.html</a> to test donation with GPS location</li>";
    echo "<li>Go to <a href='registerngo.html'>registerngo.html</a> to test NGO registration with GPS location</li>";
    echo "<li>Both forms now capture exact GPS coordinates automatically</li>";
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

$conn = null;
?>