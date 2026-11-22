<?php
session_start();
if (!isset($_SESSION['Member_id'])) { header("Location: login.php"); exit(); }
include 'db_conn.php';

$member_id = $_SESSION['Member_id'];

// Fetch only the logged-in member's requests
$requests = $conn->query("
    SELECT r.*, e.name as equip_name 
    FROM equipment_requests r
    JOIN equipment e ON r.E_id = e.E_id
    WHERE r.Member_id = $member_id
    ORDER BY r.req_id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Requests - Club Tracker</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #f8f8ff; color: #4C489D; }
        .badge { padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; }
        .badge-Pending { background: #fef3c7; color: #d97706; }
        .badge-Approved { background: #d1fae5; color: #059669; }
        .badge-Rejected { background: #fee2e2; color: #dc2626; }
        .badge-Returned { background: #e0e7ff; color: #4338ca; }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="dashboard-main">
            <div class="dashboard-content-card">
                <div class="header">
                    <h1>📦 My Equipment Requests</h1>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Req ID</th>
                            <th>Equipment</th>
                            <th>Request Date</th>
                            <th>Expected Return</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($requests && $requests->num_rows > 0): ?>
                            <?php while ($row = $requests->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['req_id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['equip_name']); ?></strong></td>
                                <td><?php echo date('M d, Y', strtotime($row['request_date'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['expected_return_date'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $row['status']; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #64748b; padding: 30px; background: #f8f8ff;">
                                    You haven't made any equipment requests yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>