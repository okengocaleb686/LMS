<?php
session_start();
require_once 'security.php';
require_admin();

require_once 'simple_db.php';

$advanced_stats = $db->getAdvancedStats();
$stats = $db->getStats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Library Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; padding: 0; min-height: 100vh; }
        .navbar { background-color: #2c3e50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .navbar .logo img { height: 50px; }
        .navbar .welcome { font-size: 18px; font-weight: 500; }
        .navbar .right { display: flex; align-items: center; }
        .navbar .profile { position: relative; margin-right: 20px; }
        .navbar .profile button { background: none; border: none; color: white; cursor: pointer; font-size: 16px; padding: 8px 12px; border-radius: 4px; transition: background 0.3s; }
        .navbar .profile button:hover { background: rgba(255,255,255,0.1); }
        .navbar .profile .dropdown { display: none; position: absolute; right: 0; background-color: white; min-width: 180px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; border-radius: 4px; }
        .navbar .profile .dropdown a { color: #333; padding: 12px 16px; text-decoration: none; display: block; transition: background 0.3s; }
        .navbar .profile .dropdown a:hover { background-color: #f8f9fa; }
        .navbar .profile:hover .dropdown { display: block; }
        .navbar .logout a { color: white; text-decoration: none; font-size: 16px; padding: 8px 12px; border-radius: 4px; transition: background 0.3s; }
        .navbar .logout a:hover { background: rgba(255,255,255,0.1); }
        .navmenu { background-color: #34495e; padding: 0; border-bottom: none; }
        .navmenu ul { list-style: none; margin: 0; padding: 0; display: flex; justify-content: center; flex-wrap: wrap; }
        .navmenu li { position: relative; margin: 0; }
        .navmenu a { color: white; text-decoration: none; padding: 15px 20px; display: block; transition: background 0.3s; }
        .navmenu a:hover, .navmenu a.active { background-color: #2c3e50; }
        .navmenu .dropdown { position: relative; }
        .navmenu .dropdown-content { display: none; position: absolute; background-color: #34495e; min-width: 200px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; top: 100%; left: 0; border-radius: 0 0 4px 4px; }
        .navmenu .dropdown-content a { color: white; padding: 12px 16px; text-decoration: none; display: block; transition: background 0.3s; }
        .navmenu .dropdown-content a:hover { background-color: #2c3e50; }
        .navmenu .dropdown:hover .dropdown-content { display: block; }
        .dashboard { max-width: 1400px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .stat-card h3 { margin: 0; font-size: 32px; font-weight: bold; }
        .stat-card p { margin: 10px 0 0 0; font-size: 16px; opacity: 0.9; }
        .charts-section { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; margin-bottom: 40px; }
        .chart-container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .chart-container h3 { color: #2c3e50; margin-bottom: 20px; text-align: center; }
        .table-section { margin-top: 40px; }
        .table-container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .table-container h3 { color: #2c3e50; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #e9ecef; }
        .back-link { display: inline-block; margin-top: 20px; color: #667eea; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .back-link:hover { color: #764ba2; }
        @media (max-width: 768px) {
            .navbar { flex-direction: column; text-align: center; }
            .navbar .right { margin-top: 10px; }
            .navmenu ul { flex-direction: column; }
            .navmenu li { margin: 0; }
            .dashboard { padding: 20px; }
            .charts-section { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo"><img src="IMAGES/LOGO.PNG" alt="LMS Logo"></div>
        <div class="welcome">Welcome: <?php echo $_SESSION['user_name']; ?>, Email: <?php echo $_SESSION['user']; ?></div>
        <div class="right">
            <div class="profile">
                <button>My Profile ▼</button>
                <div class="dropdown">
                    <a href="view_profile.php">View Profile</a>
                    <a href="edit_profile.php">Edit Profile</a>
                    <a href="change_password.php">Change Password</a>
                </div>
            </div>
            <div class="logout">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
    <nav class="navmenu">
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-book"></i> Books <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="manage_books.php"><i class="fas fa-cogs"></i> Manage Books</a>
                    <a href="add_book.php"><i class="fas fa-plus"></i> Add Book</a>
                </div>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-users"></i> Users <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="view_registered_users.php"><i class="fas fa-eye"></i> View Users</a>
                    <a href="add_user.php"><i class="fas fa-plus"></i> Add User</a>
                </div>
            </li>
            <li><a href="reports.php" class="active"><i class="fas fa-chart-bar"></i> Reports</a></li>
        </ul>
    </nav>
    <div class="dashboard">
        <h1><i class="fas fa-chart-bar"></i> Reports & Analytics</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $stats['total_books']; ?></h3>
                <p>Total Books</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['total_users']; ?></h3>
                <p>Registered Users</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['issued_books']; ?></h3>
                <p>Books Currently Issued</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['overdue_books']; ?></h3>
                <p>Overdue Books</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['active_reservations']; ?></h3>
                <p>Active Reservations</p>
            </div>
            <div class="stat-card">
                <h3>$<?php echo number_format($stats['total_fines'], 2); ?></h3>
                <p>Total Fines</p>
            </div>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <h3><i class="fas fa-calendar-alt"></i> Monthly Book Issues (Last 12 Months)</h3>
                <canvas id="monthlyIssuesChart" width="400" height="300"></canvas>
            </div>
            <div class="chart-container">
                <h3><i class="fas fa-tags"></i> Books by Category</h3>
                <canvas id="categoryChart" width="400" height="300"></canvas>
            </div>
        </div>

        <div class="table-section">
            <div class="table-container">
                <h3><i class="fas fa-tags"></i> Category Statistics</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Total Books</th>
                            <th>Books Issued</th>
                            <th>Availability Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($advanced_stats['category_stats'] as $cat): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                <td><?php echo $cat['total_books']; ?></td>
                                <td><?php echo $cat['issued_books']; ?></td>
                                <td>
                                    <?php
                                    $rate = $cat['total_books'] > 0 ? (($cat['total_books'] - $cat['issued_books']) / $cat['total_books']) * 100 : 0;
                                    echo number_format($rate, 1) . '%';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-2">
            <a href="admin_dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2024 Library Management System. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Us</a>
            </div>
        </div>
    </footer>

    <script>
        // Monthly Issues Chart
        const monthlyCtx = document.getElementById('monthlyIssuesChart').getContext('2d');
        const monthlyData = <?php echo json_encode($advanced_stats['monthly_issues']); ?>;

        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'Books Issued',
                    data: monthlyData.map(item => item.count),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Book Issues Trend'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryData = <?php echo json_encode($advanced_stats['category_stats']); ?>;

        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(item => item.name),
                datasets: [{
                    data: categoryData.map(item => item.total_books),
                    backgroundColor: [
                        '#667eea',
                        '#764ba2',
                        '#f093fb',
                        '#f5576c',
                        '#4facfe',
                        '#00f2fe'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Book Distribution by Category'
                    }
                }
            }
        });
    </script>
</body>
</html>