<?php
session_start();
if (!isset($_SESSION['Member_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
include 'db_conn.php';

// Fetch all members from the database
$members = $conn->query("SELECT Member_id, full_name, email, role, email_verified FROM Member ORDER BY role ASC, full_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Members List - Club Tracker</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #f8f8ff; color: #4C489D; }
        .role-badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .role-admin { background: #fee2e2; color: #dc2626; }
        .role-member { background: #d1fae5; color: #059669; }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="dashboard-main">
            <div class="dashboard-content-card">
                <div class="header">
                    <h1>👥 Club Members</h1>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $members->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['Member_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="role-badge <?php echo ($row['role'] === 'admin') ? 'role-admin' : 'role-member'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($row['role'])); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>