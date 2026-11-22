<?php
session_start();
// Security check: Only admins can access this page
if (!isset($_SESSION['Member_id']) || $_SESSION['role'] !== 'admin') { 
    header("Location: dashboard.php"); 
    exit(); 
}
include 'db_conn.php';

// Handle Action Links (Approve, Reject, Mark Returned)
if (isset($_GET['action']) && isset($_GET['req_id'])) {
    $req_id = (int)$_GET['req_id'];
    $action = $_GET['action'];
    
    // Get the request details
    $req = $conn->query("SELECT * FROM equipment_requests WHERE req_id = $req_id")->fetch_assoc();

    if ($req) {
        $e_id = $req['E_id'];
        $m_id = $req['Member_id'];
        $exp_return = $req['expected_return_date'];
        $today = date('Y-m-d');

        if ($action === 'approve' && $req['status'] === 'Pending') {
            // 1. Mark request as Approved
            $conn->query("UPDATE equipment_requests SET status = 'Approved' WHERE req_id = $req_id");
            // 2. Update actual Equipment item to borrowed so teammates' trackers work
            $conn->query("UPDATE equipment SET status = 'Borrowed', Member_id = $m_id, borrow_date = '$today', expected_return_date = '$exp_return', actual_return_date = NULL WHERE E_id = $e_id");
        } 
        elseif ($action === 'reject' && $req['status'] === 'Pending') {
            // 1. Just mark the request as Rejected
            $conn->query("UPDATE equipment_requests SET status = 'Rejected' WHERE req_id = $req_id");
        } 
        elseif ($action === 'return' && $req['status'] === 'Approved') {
            // 1. Mark request as completed/returned
            $conn->query("UPDATE equipment_requests SET status = 'Returned' WHERE req_id = $req_id");
            // 2. Make the Equipment available again and log the return date
            $conn->query("UPDATE equipment SET status = 'Available', actual_return_date = '$today' WHERE E_id = $e_id");
        }
    }
    // Refresh to clear the URL parameters
    header("Location: manage_requests.php");
    exit();
}

// Fetch all requests, linking Member names and Equipment names
$requests = $conn->query("
    SELECT r.*, m.full_name, e.name as equip_name 
    FROM equipment_requests r
    JOIN member m ON r.Member_id = m.Member_id
    JOIN equipment e ON r.E_id = e.E_id
    ORDER BY r.req_id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Requests - Club Tracker</title>
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
                    <h1>📋 Manage Equipment Requests</h1>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Req ID</th>
                            <th>Member</th>
                            <th>Equipment</th>
                            <th>Expected Return</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($requests && $requests->num_rows > 0): ?>
                            <?php while ($row = $requests->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['req_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['equip_name']); ?></strong></td>
                                <td><?php echo date('M d, Y', strtotime($row['expected_return_date'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $row['status']; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'Pending'): ?>
                                        <a href="?action=approve&req_id=<?php echo $row['req_id']; ?>" style="color: #10b981; font-weight: bold; margin-right: 15px; text-decoration: none;">Approve</a>
                                        <a href="?action=reject&req_id=<?php echo $row['req_id']; ?>" style="color: #ef4444; font-weight: bold; text-decoration: none;">Reject</a>
                                    <?php elseif ($row['status'] === 'Approved'): ?>
                                        <a href="?action=return&req_id=<?php echo $row['req_id']; ?>" class="btn btn-primary" style="padding: 4px 10px; font-size: 12px; text-decoration: none;">Mark Returned</a>
                                    <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 13px; font-weight: 600;">Completed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #64748b; padding: 30px; background: #f8f8ff;">No equipment requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>