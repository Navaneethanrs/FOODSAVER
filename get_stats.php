<?php
$conn = new mysqli("localhost", "root", "", "food_saver", 3307);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

// Get total donations (Meals Served)
$meals_result = $conn->query("SELECT COUNT(*) as count FROM donations");
$meals = $meals_result ? $meals_result->fetch_assoc()['count'] : 0;

// Get accepted donations (Food Saved)
$food_result = $conn->query("SELECT COUNT(*) as count FROM donations WHERE status = 'accepted'");
$food_saved = $food_result ? $food_result->fetch_assoc()['count'] : 0;

// Get total NGOs (Volunteers Joined) - try both table names
$volunteers = 0;
$ngo_result = $conn->query("SELECT COUNT(*) as count FROM ngo_registrations");
if ($ngo_result) {
    $volunteers = $ngo_result->fetch_assoc()['count'];
} else {
    $ngo_result = $conn->query("SELECT COUNT(*) as count FROM ngos");
    if ($ngo_result) {
        $volunteers = $ngo_result->fetch_assoc()['count'];
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode([
    'meals' => (int)$meals,
    'food_saved' => (int)$food_saved,
    'volunteers' => (int)$volunteers
]);
?>
