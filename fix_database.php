<?php
$conn = new mysqli("localhost", "root", "", "food_saver", 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if columns exist
$result = $conn->query("DESCRIBE donations");
$columns = [];
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
}

echo "<h3>Current columns:</h3>";
foreach ($columns as $col) {
    echo "- $col<br>";
}

// Add missing columns
if (!in_array('status', $columns)) {
    $sql = "ALTER TABLE donations ADD COLUMN status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending'";
    if ($conn->query($sql)) {
        echo "<br>✅ Added 'status' column<br>";
    } else {
        echo "<br>❌ Error adding 'status': " . $conn->error . "<br>";
    }
}

if (!in_array('accepted_by', $columns)) {
    $sql = "ALTER TABLE donations ADD COLUMN accepted_by VARCHAR(255) NULL";
    if ($conn->query($sql)) {
        echo "✅ Added 'accepted_by' column<br>";
    } else {
        echo "❌ Error adding 'accepted_by': " . $conn->error . "<br>";
    }
}

if (!in_array('accepted_at', $columns)) {
    $sql = "ALTER TABLE donations ADD COLUMN accepted_at TIMESTAMP NULL";
    if ($conn->query($sql)) {
        echo "✅ Added 'accepted_at' column<br>";
    } else {
        echo "❌ Error adding 'accepted_at': " . $conn->error . "<br>";
    }
}

$conn->close();
echo "<br><strong>Database update complete!</strong>";
?>