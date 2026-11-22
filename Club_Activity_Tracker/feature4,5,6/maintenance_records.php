<?php
session_start();
if (!isset($_SESSION['Member_id'])) { header("Location: login.php"); exit(); }
include 'db_conn.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Records</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include 'sidebar.php'; ?>
    <div class="dashboard-main">
        <div class="dashboard-content-card">
            <div class="header">
                <h1>Maintenance Records</h1>
                <a href="add_maintenance.php" class="btn btn-primary">+ New Record</a>
            </div>
            <table style="width:100%; border-collapse: collapse; margin-top:20px;">
                <thead style="background:#f1f1f1;">
                    <tr><th>Equipment</th><th>Cost</th><th>Date</th><th>Type</th><th>Remark</th></tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT m.*, e.name AS equipment_name 
                        FROM Maintenance m 
                        JOIN Equipment e ON m.E_id = e.E_id 
                        ORDER BY m.date DESC";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['equipment_name']) . "</td>";
                    echo "<td>TK" . number_format($row['cost'], 2) . "</td>";
                    echo "<td>" . $row['date'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['remark']) . "</td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>