<!-- sidebar.php -->
<div class="dashboard-sidebar">
    <h2>🏫 Club Tracker</h2>
    <a href="dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'class="active"' : ''; ?>>📊 Dashboard</a>
    <a href="view_announcements.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'view_announcements.php' || basename($_SERVER['PHP_SELF']) == 'add_announcement.php') ? 'class="active"' : ''; ?>>📢 Announcements</a>
    <a href="events_list.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'events_list.php' || basename($_SERVER['PHP_SELF']) == 'add_event.php') ? 'class="active"' : ''; ?>>📅 Events</a>
    <a href="event_team.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'event_team.php') ? 'class="active"' : ''; ?>>🗂️ Event Team</a>
    <a href="event_expenses.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'event_expenses.php') ? 'class="active"' : ''; ?>>💰 Expenses</a>
    <a href="reports.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'class="active"' : ''; ?>>📈 Reports</a>
    <a href="equipment_list.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'equipment_list.php' || basename($_SERVER['PHP_SELF']) == 'request_equipment.php') ? 'class="active"' : ''; ?>>🔧 Equipment</a>
    
    <!-- NEW: Members can check their request status here -->
    <a href="my_requests.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'my_requests.php') ? 'class="active"' : ''; ?>>📦 My Requests</a>

    <a href="maintenance_records.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'maintenance_records.php' || basename($_SERVER['PHP_SELF']) == 'add_maintenance.php') ? 'class="active"' : ''; ?>>🛠️ Maintenance</a>
    
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="manage_requests.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_requests.php') ? 'class="active"' : ''; ?>>📋 Admin Requests</a>
    <?php endif; ?>
    
    <a href="members.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'members.php') ? 'class="active"' : ''; ?>>👥 Members</a>
    
    <a href="logout.php" class="logout">🚪 Logout</a>
</div>