<?php
session_start();
if (!isset($_SESSION['Member_id'])) { header("Location: login.php"); exit(); }
include 'db_conn.php';

// Fetch all announcements with author name
$sql = "SELECT a.*, m.full_name AS author_name 
        FROM announcement a 
        JOIN member m ON a.Member_id = m.Member_id 
        ORDER BY a.posted_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-content-card {
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0px 0px 24px #5C5696;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-new {
            text-decoration: none;
            background: #5D54A4;
            color: #fff;
            padding: 10px 20px;
            border-radius: 26px;
            font-weight: 700;
        }
        .announcement-card {
            background: #f8f8ff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            border-left: 5px solid #5D54A4;
        }
        .announcement-title {
            font-size: 18px;
            font-weight: 700;
            color: #4C489D;
            margin-bottom: 8px;
        }
        .announcement-meta {
            font-size: 13px;
            color: #7875B5;
            margin-bottom: 10px;
        }
        .announcement-content {
            color: #333;
        }
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #64748b;
            background: #f8f8ff;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php include 'sidebar.php'; ?>
    <div class="dashboard-main">
        <div class="dashboard-content-card">
            <div class="header">
                <h1>📢 Club Announcements</h1>
                <a href="add_announcement.php" class="btn-new">+ New Announcement</a>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="announcement-card">
                        <div class="announcement-title"><?php echo htmlspecialchars($row['title']); ?></div>
                        <div class="announcement-meta">
                            Posted by <strong><?php echo htmlspecialchars($row['author_name']); ?></strong>
                            on <?php echo date('M d, Y', strtotime($row['posted_date'])); ?>
                            <?php if ($row['Event_id']): ?>
                                – related to Event ID #<?php echo $row['Event_id']; ?>
                            <?php endif; ?>
                        </div>
                        <div class="announcement-content"><?php echo nl2br(htmlspecialchars($row['content'])); ?></div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>No announcements yet. Be the first to post one!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
