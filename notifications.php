<?php
session_start();
require_once 'simple_db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['type'];

// Mark notifications as read if requested
if (isset($_POST['mark_read']) && isset($_POST['notification_id'])) {
    $db->markNotificationRead((int)$_POST['notification_id']);
    header("Location: notifications.php");
    exit();
}

if (isset($_POST['mark_all_read'])) {
    $all_notifications = $db->getUserNotifications($user_id);
    foreach ($all_notifications as $notification) {
        $db->markNotificationRead($notification['id']);
    }
    header("Location: notifications.php");
    exit();
}

// Get notifications
$notifications = $db->getUserNotifications($user_id);
$unread_count = count(array_filter($notifications, function($n) { return !$n['is_read']; }));

// Sort notifications by creation date (newest first)
usort($notifications, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - Library Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, <?php echo $user_type == 'admin' ? '#667eea 0%, #764ba2 100%' : '#a8edea 0%, #fed6e3 100%'; ?>); margin: 0; padding: 0; min-height: 100vh; }
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
        .dashboard { max-width: 1000px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 30px; }
        .notification-stats { display: flex; justify-content: center; gap: 20px; margin-bottom: 30px; }
        .stat-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; text-align: center; min-width: 150px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .stat-box h3 { margin: 0; font-size: 24px; font-weight: bold; }
        .stat-box p { margin: 5px 0 0 0; font-size: 14px; }
        .actions { text-align: center; margin-bottom: 30px; }
        .btn-mark-all { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; transition: transform 0.3s, box-shadow 0.3s; border: none; cursor: pointer; }
        .btn-mark-all:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .notification-list { margin-top: 30px; }
        .notification-item { border: 1px solid #e9ecef; border-radius: 8px; padding: 20px; margin-bottom: 15px; transition: box-shadow 0.3s, border-color 0.3s; position: relative; }
        .notification-item.unread { border-left: 4px solid #007bff; background-color: #f8f9ff; }
        .notification-item:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-color: #007bff; }
        .notification-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .notification-title { font-weight: 600; color: #2c3e50; margin: 0; }
        .notification-date { color: #6c757d; font-size: 14px; }
        .notification-message { color: #495057; line-height: 1.5; margin-bottom: 15px; }
        .notification-actions { text-align: right; }
        .btn-mark-read { background: #007bff; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; transition: background 0.3s; border: none; cursor: pointer; font-size: 14px; }
        .btn-mark-read:hover { background: #0056b3; }
        .unread-indicator { position: absolute; top: 15px; right: 15px; width: 10px; height: 10px; background: #007bff; border-radius: 50%; }
        .empty-state { text-align: center; color: #6c757d; padding: 50px 20px; }
        .empty-state i { font-size: 48px; margin-bottom: 20px; opacity: 0.5; }
        .back-link { display: inline-block; margin-top: 20px; color: #667eea; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .back-link:hover { color: #764ba2; }
        @media (max-width: 768px) {
            .navbar { flex-direction: column; text-align: center; }
            .navbar .right { margin-top: 10px; }
            .navmenu ul { flex-direction: column; }
            .navmenu li { margin: 0; }
            .dashboard { padding: 20px; }
            .notification-stats { flex-direction: column; align-items: center; }
            .stat-box { width: 100%; max-width: 250px; }
            .notification-header { flex-direction: column; align-items: flex-start; gap: 5px; }
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
            <?php if ($user_type == 'admin'): ?>
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
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <?php else: ?>
                <li><a href="user_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="dropdown">
                    <a href="#"><i class="fas fa-book"></i> Books <i class="fas fa-caret-down"></i></a>
                    <div class="dropdown-content">
                        <a href="view_books.php"><i class="fas fa-eye"></i> View Books</a>
                        <a href="search_books.php"><i class="fas fa-search"></i> Search Books</a>
                    </div>
                </li>
                <li><a href="my_issued.php"><i class="fas fa-book-open"></i> My Issued Books</a></li>
                <li><a href="reserve_book.php"><i class="fas fa-calendar-check"></i> Reserve Books</a></li>
            <?php endif; ?>
            <li><a href="notifications.php" class="active"><i class="fas fa-bell"></i> Notifications <?php if ($unread_count > 0): ?><span style="background: #dc3545; color: white; padding: 2px 6px; border-radius: 10px; font-size: 12px;"><?php echo $unread_count; ?></span><?php endif; ?></a></li>
        </ul>
    </nav>
    <div class="dashboard">
        <h1><i class="fas fa-bell"></i> Notifications</h1>

        <div class="notification-stats">
            <div class="stat-box">
                <h3><?php echo count($notifications); ?></h3>
                <p>Total Notifications</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $unread_count; ?></h3>
                <p>Unread</p>
            </div>
        </div>

        <?php if ($unread_count > 0): ?>
            <div class="actions">
                <form method="post" style="display: inline;">
                    <button type="submit" name="mark_all_read" class="btn-mark-all">
                        <i class="fas fa-check-double"></i> Mark All as Read
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <div class="notification-list">
            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <h3>No notifications yet</h3>
                    <p>You'll receive notifications about book reservations, due dates, and other important updates here.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo !$notification['is_read'] ? 'unread' : ''; ?>">
                        <?php if (!$notification['is_read']): ?>
                            <div class="unread-indicator"></div>
                        <?php endif; ?>

                        <div class="notification-header">
                            <h4 class="notification-title">
                                <i class="fas fa-<?php
                                    echo $notification['type'] == 'success' ? 'check-circle' :
                                         ($notification['type'] == 'warning' ? 'exclamation-triangle' :
                                         ($notification['type'] == 'error' ? 'times-circle' : 'info-circle'));
                                ?>"></i>
                                <?php echo htmlspecialchars($notification['title']); ?>
                            </h4>
                            <span class="notification-date">
                                <?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?>
                            </span>
                        </div>

                        <div class="notification-message">
                            <?php echo htmlspecialchars($notification['message']); ?>
                        </div>

                        <?php if (!$notification['is_read']): ?>
                            <div class="notification-actions">
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                    <button type="submit" name="mark_read" class="btn-mark-read">
                                        <i class="fas fa-check"></i> Mark as Read
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <a href="<?php echo $user_type == 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</body>
</html>