<!DOCTYPE html>
<html>
<head>
    <title>Donation Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>📊 Donation Status</h2>
        
        <?php
        $conn = new mysqli("localhost", "root", "", "food_saver", 3307);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $sql = "SELECT * FROM donations ORDER BY created_at DESC LIMIT 20";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            echo "<table class='table table-striped'>";
            echo "<thead><tr><th>Donor</th><th>Food</th><th>Location</th><th>Status</th><th>Accepted By</th><th>Date</th></tr></thead>";
            echo "<tbody>";
            
            while($row = $result->fetch_assoc()) {
                $status_badge = '';
                if ($row['status'] == 'pending') {
                    $status_badge = '<span class="badge bg-warning">⏳ Pending</span>';
                } elseif ($row['status'] == 'accepted') {
                    $status_badge = '<span class="badge bg-success">✅ Accepted</span>';
                } else {
                    $status_badge = '<span class="badge bg-danger">❌ Rejected</span>';
                }
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['donor_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['food_type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                echo "<td>" . $status_badge . "</td>";
                echo "<td>" . htmlspecialchars($row['accepted_by'] ?? '-') . "</td>";
                echo "<td>" . date('M d, H:i', strtotime($row['created_at'])) . "</td>";
                echo "</tr>";
            }
            
            echo "</tbody></table>";
        } else {
            echo "<div class='alert alert-info'>No donations found.</div>";
        }
        
        $conn->close();
        ?>
        
        <a href="index.html" class="btn btn-primary">Back to Home</a>
    </div>
</body>
</html>