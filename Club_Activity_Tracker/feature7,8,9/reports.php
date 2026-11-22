<?php
session_start();
if (!isset($_SESSION['Member_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_conn.php';

$upcoming_events = $conn->query("SELECT COUNT(*) as count FROM events WHERE event_date >= CURDATE()")->fetch_assoc()['count'];
$overdue_equipment = $conn->query("SELECT COUNT(*) as count FROM equipment WHERE status = 'Borrowed' AND expected_return_date < CURDATE()")->fetch_assoc()['count'];
$available_items = $conn->query("SELECT COUNT(*) as count FROM equipment WHERE status = 'Available'")->fetch_assoc()['count'];
$damaged_items = $conn->query("SELECT COUNT(*) as count FROM equipment WHERE status = 'Damaged'")->fetch_assoc()['count'];

$total_expenses = $conn->query("SELECT SUM(amount) as total FROM expense")->fetch_assoc()['total'] ?? 0;

$active_members = $conn->query("
    SELECT m.full_name, COUNT(t.T_id) as task_count 
    FROM member m 
    LEFT JOIN task t ON m.Member_id = t.Member_id 
    GROUP BY m.Member_id 
    ORDER BY task_count DESC 
    LIMIT 5
");

$upcoming_list = $conn->query("SELECT name, event_date, venue FROM events WHERE event_date >= CURDATE() ORDER BY event_date LIMIT 5");

$expense_by_category = $conn->query("SELECT category, SUM(amount) as total FROM expense GROUP BY category ORDER BY total DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reports & Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* === Flex layout to push footer down === */
        .dashboard-content-card {
            display: flex;
            flex-direction: column;
            min-height: 85vh;
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 24px #5C5696;
        }
        .content-area {
            flex: 1;
        }
        .footer-link {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #4C489D;
            padding: 10px 20px;
            border-radius: 26px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            transition: 0.2s;
        }
        .btn-secondary:hover {
            background: #cbd5e1;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px,1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8f8ff;
            padding: 15px;
            border-radius: 12px;
            border-left: 5px solid #5D54A4;
        }
        .stat-card h4 {
            color: #7875B5;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .stat-card p {
            font-size: 24px;
            font-weight: 700;
            color: #4C489D;
        }
        .section {
            margin-top: 30px;
        }
        .section h2 {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .badge {
            background: #5D54A4;
            color: #fff;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div style="max-width:1100px; margin:40px auto;">
    <div class="dashboard-content-card">

        <div class="header">
            <h1>📊 Reports & Dashboard</h1>
            <span class="user-badge">👋 <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
        </div>
        <div class="content-area">

            <div class="stats-grid">
                <div class="stat-card"><h4>📅 Upcoming Events</h4><p><?php echo $upcoming_events; ?></p></div>
                <div class="stat-card" style="border-left-color:#e74c3c;"><h4>⏰ Overdue Equipment</h4><p><?php echo $overdue_equipment; ?></p></div>
                <div class="stat-card" style="border-left-color:#2ecc71;"><h4>✅ Available Items</h4><p><?php echo $available_items; ?></p></div>
                <div class="stat-card" style="border-left-color:#f39c12;"><h4>⚠️ Damaged Items</h4><p><?php echo $damaged_items; ?></p></div>
                <div class="stat-card" style="border-left-color:#3498db;"><h4>💰 Total Event Expenses</h4><p>$<?php echo number_format($total_expenses, 2); ?></p></div>
            </div>

            <div class="section">
                <h2>📅 Upcoming Events</h2>
                <?php if ($upcoming_list->num_rows > 0): ?>
                    <ul>
                        <?php while ($ev = $upcoming_list->fetch_assoc()): ?>
                            <li><strong><?php echo htmlspecialchars($ev['name']); ?></strong> – <?php echo $ev['event_date']; ?> (<?php echo htmlspecialchars($ev['venue']); ?>)</li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p><em>No upcoming events.</em></p>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2>👥 Most Active Members (by tasks)</h2>
                <?php if ($active_members->num_rows > 0): ?>
                    <table>
                        <thead><tr><th>Member</th><th>Tasks Assigned</th></tr></thead>
                        <tbody>
                        <?php while ($row = $active_members->fetch_assoc()): ?>
                            <tr><td><?php echo htmlspecialchars($row['full_name']); ?></td><td><?php echo $row['task_count']; ?></td></tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p><em>No task assignments yet.</em></p>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2>📊 Expenses by Category</h2>
                <?php if ($expense_by_category->num_rows > 0): ?>
                    <table>
                        <thead><tr><th>Category</th><th>Total Amount</th></tr></thead>
                        <tbody>
                        <?php while ($cat = $expense_by_category->fetch_assoc()): ?>
                            <tr><td><?php echo htmlspecialchars($cat['category']); ?></td><td>$<?php echo number_format($cat['total'], 2); ?></td></tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p><em>No expenses recorded.</em></p>
                <?php endif; ?>
            </div>

        </div> 

        <div class="footer-link">
            <a href="dashboard.php" class="btn-secondary">← Back to Dashboard</a>
        </div>

    </div> 
</div>
</body>
</html>