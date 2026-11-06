<?php
$conn = new mysqli("localhost", "root", "", "food_saver", 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add columns if they don't exist
$conn->query("ALTER TABLE donations ADD COLUMN IF NOT EXISTS status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending'");
$conn->query("ALTER TABLE donations ADD COLUMN IF NOT EXISTS accepted_by VARCHAR(255) NULL");
$conn->query("ALTER TABLE donations ADD COLUMN IF NOT EXISTS accepted_at TIMESTAMP NULL");

echo "Database updated successfully!";
$conn->close();
?>