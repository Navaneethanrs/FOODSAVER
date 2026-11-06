<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Donations - Food Saver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>All Donations</h2>
        
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "food_saver";

        try {
            $conn = new PDO("mysql:host=$servername;port=3307;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $conn->prepare("SELECT * FROM donations ORDER BY created_at DESC");
            $stmt->execute();
            $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($donations) > 0) {
                echo "<table class='table table-striped'>";
                echo "<thead><tr><th>ID</th><th>Donor Name</th><th>Food Type</th><th>Quantity</th><th>Location</th><th>Contact</th><th>Pickup Time</th><th>Date</th></tr></thead>";
                echo "<tbody>";
                
                foreach ($donations as $row) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['donor_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['food_type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['donor_contact']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['pickup_time']) . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "</tr>";
                }
                
                echo "</tbody></table>";
            } else {
                echo "<p>No donations found.</p>";
            }
            
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
        
        <a href="donate.html" class="btn btn-primary">Add New Donation</a>
        <a href="index.html" class="btn btn-secondary">Back to Home</a>
    </div>
</body>
</html>