<?php
// Admin Dashboard for Food Saver
$host = 'localhost';
$dbname = 'food_saver';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get statistics
$stats = [];
$stats['total_donations'] = $pdo->query("SELECT COUNT(*) FROM donations")->fetchColumn();
$stats['pending_donations'] = $pdo->query("SELECT COUNT(*) FROM donations WHERE status = 'pending'")->fetchColumn();
$stats['total_ngos'] = $pdo->query("SELECT COUNT(*) FROM ngos WHERE status = 'active'")->fetchColumn();
$stats['total_notifications'] = $pdo->query("SELECT COUNT(*) FROM ngo_notifications")->fetchColumn();

// Get recent donations
$recent_donations = $pdo->query("
    SELECT d.*, 
           (SELECT COUNT(*) FROM ngo_notifications WHERE donation_id = d.id) as notifications_count
    FROM donations d 
    ORDER BY d.created_at DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Get recent notifications
$recent_notifications = $pdo->query("
    SELECT n.*, d.donor_name, d.food_type, d.location 
    FROM ngo_notifications n
    JOIN donations d ON n.donation_id = d.id
    ORDER BY n.sent_at DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Food Saver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff6347;
            --secondary-color: #ff7f50;
            --accent-color: #ffd700;
        }
        
        body {
            background: linear-gradient(135deg, #fff8f2 0%, #ffe6d6 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(90deg, var(--secondary-color), var(--primary-color)) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d1edff; color: #0c5460; }
        .status-sent { background: #d4edda; color: #155724; }
        
        .refresh-btn {
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .refresh-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-tachometer-alt"></i> Food Saver Admin
            </a>
            <div class="ms-auto">
                <button class="btn refresh-btn" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Donations</h6>
                            <div class="stat-number"><?php echo $stats['total_donations']; ?></div>
                        </div>
                        <div class="text-primary fs-1">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Pending Donations</h6>
                            <div class="stat-number"><?php echo $stats['pending_donations']; ?></div>
                        </div>
                        <div class="text-warning fs-1">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Active NGOs</h6>
                            <div class="stat-number"><?php echo $stats['total_ngos']; ?></div>
                        </div>
                        <div class="text-success fs-1">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Notifications Sent</h6>
                            <div class="stat-number"><?php echo $stats['total_notifications']; ?></div>
                        </div>
                        <div class="text-info fs-1">
                            <i class="fas fa-bell"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="table-container">
            <h4 class="mb-3">
                <i class="fas fa-donate text-primary"></i> Recent Donations
            </h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Donor</th>
                            <th>Food Type</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>NGOs Notified</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_donations as $donation): ?>
                        <tr>
                            <td><strong>#<?php echo $donation['id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($donation['donor_name']); ?></td>
                            <td><?php echo htmlspecialchars($donation['food_type']); ?></td>
                            <td><?php echo htmlspecialchars($donation['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($donation['location']); ?></td>
                            <td><?php echo htmlspecialchars($donation['donor_contact']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $donation['status']; ?>">
                                    <?php echo ucfirst($donation['status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo $donation['notifications_count']; ?></span>
                            </td>
                            <td><?php echo date('M j, Y H:i', strtotime($donation['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="table-container">
            <h4 class="mb-3">
                <i class="fas fa-bell text-primary"></i> Recent NGO Notifications
            </h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Donation ID</th>
                            <th>NGO Name</th>
                            <th>NGO Contact</th>
                            <th>Donor</th>
                            <th>Food Type</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Sent At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_notifications as $notification): ?>
                        <tr>
                            <td><strong>#<?php echo $notification['donation_id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($notification['ngo_name']); ?></td>
                            <td>
                                <?php if ($notification['ngo_phone']): ?>
                                    <i class="fas fa-phone text-success"></i> <?php echo $notification['ngo_phone']; ?><br>
                                <?php endif; ?>
                                <?php if ($notification['ngo_email']): ?>
                                    <i class="fas fa-envelope text-primary"></i> <?php echo $notification['ngo_email']; ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($notification['donor_name']); ?></td>
                            <td><?php echo htmlspecialchars($notification['food_type']); ?></td>
                            <td><?php echo htmlspecialchars($notification['location']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $notification['status']; ?>">
                                    <?php echo ucfirst($notification['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y H:i', strtotime($notification['sent_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-6">
                <div class="table-container">
                    <h5 class="mb-3"><i class="fas fa-tools text-primary"></i> Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <a href="view_ngos.php" class="btn btn-outline-primary">
                            <i class="fas fa-users"></i> View All NGOs
                        </a>
                        <a href="setup_database.php" class="btn btn-outline-secondary">
                            <i class="fas fa-database"></i> Database Setup
                        </a>
                        <a href="donate.html" class="btn btn-outline-success">
                            <i class="fas fa-plus"></i> Test Donation Form
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="table-container">
                    <h5 class="mb-3"><i class="fas fa-info-circle text-primary"></i> System Info</h5>
                    <ul class="list-unstyled">
                        <li><strong>Database:</strong> food_saver</li>
                        <li><strong>Server:</strong> localhost</li>
                        <li><strong>Last Updated:</strong> <?php echo date('M j, Y H:i:s'); ?></li>
                        <li><strong>Status:</strong> <span class="text-success">Active</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(() => {
            location.reload();
        }, 30000);
        
        // Add loading animation to refresh button
        document.querySelector('.refresh-btn').addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
        });
    </script>
</body>
</html>
