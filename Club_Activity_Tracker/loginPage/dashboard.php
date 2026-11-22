<?php
session_start();
if (!isset($_SESSION['Member_id'])) { header("Location: login.php"); exit(); }
include 'db_conn.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Club Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <div class="dashboard-sidebar">
            <h2>🏫 Club Tracker</h2>
            <a href="dashboard.php" class="active">📊 Dashboard</a>
            <a href="events_list.php">📅 Events</a>
            <a href="event_team.php">🗂️ Event Team</a>
            <a href="event_expenses.php">💰 Expenses</a>
            <a href="reports.php">📈 Reports</a>
            <a href="equipment_list.php">🎤 Equipment</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="manage_requests.php">📋 Requests</a>
            <?php endif; ?>
            <a href="members.php">👥 Members</a>
            <a href="logout.php" class="logout">🚪 Logout</a>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main">
            <div class="dashboard-content-card">
                <div class="header">
                    <h1>Dashboard</h1>
                    <span class="user-badge">👋 <?php echo $_SESSION['full_name']; ?> (<?php echo $_SESSION['role']; ?>)</span>
                </div>

                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h4>Total Members</h4>
                        <p><?php echo $conn->query("SELECT COUNT(*) as total FROM Member")->fetch_assoc()['total']; ?></p>
                    </div>
                    <div class="stat-card" style="border-left-color: #f39c12;">
                        <h4>Upcoming Events</h4>
                        <p><?php echo $conn->query("SELECT COUNT(*) as total FROM Events WHERE event_date >= CURDATE()")->fetch_assoc()['total']; ?></p>
                    </div>
                    <div class="stat-card" style="border-left-color: #10b981;">
                        <h4>Equipment Items</h4>
                        <p><?php echo $conn->query("SELECT COUNT(*) as total FROM Equipment")->fetch_assoc()['total']; ?></p>
                    </div>
                </div>

                <!-- Two Cards for Frames 20 & 19 -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 24px;">
                    <div class="section-card">
                        <h3>📋 Recent Activity</h3>
                        <p style="color: #64748b; font-size: 14px;">Pending requests, recent member sign-ups, or upcoming deadlines will appear here.</p>
                    </div>
                    <div class="section-card">
                        <h3>⚙️ Quick Actions</h3>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <!-- FIXED: Changed from a JS button to an actual link -->
                            <a href="add_event.php" class="btn btn-primary" style="text-decoration:none;">+ New Event</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>