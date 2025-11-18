<?php
$conn = new mysqli("localhost", "root", "", "food_saver", 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add status and accepted_by columns to donations table
$sql = "ALTER TABLE donations 
        ADD COLUMN status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        ADD COLUMN accepted_by VARCHAR(255) NULL,
        ADD COLUMN accepted_at TIMESTAMP NULL";

if ($conn->query($sql) === TRUE) {
    echo "✅ Donations table updated successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>