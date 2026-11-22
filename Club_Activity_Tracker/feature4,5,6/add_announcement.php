<?php
session_start();
if (!isset($_SESSION['Member_id'])) { header("Location: login.php"); exit(); }
include 'db_conn.php';

$msg = "";
// Fetch events for the dropdown
$events = $conn->query("SELECT Event_id, name FROM events ORDER BY event_date DESC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $event_id = isset($_POST['event_id']) && $_POST['event_id'] != '' ? (int)$_POST['event_id'] : null;
    $member_id = $_SESSION['Member_id'];

    if ($event_id) {
        $stmt = $conn->prepare("INSERT INTO announcement (title, content, Member_id, Event_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $title, $content, $member_id, $event_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO announcement (title, content, Member_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $content, $member_id);
    }

    if ($stmt->execute()) {
        $msg = "<div class='msg'>✅ Announcement published!</div>";
    } else {
        $msg = "<div class='msg msg-error'>❌ Error publishing announcement.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Announcement</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-content-card {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0px 0px 24px #5C5696;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #4C489D;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 26px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            background: #5D54A4;
            color: #fff;
            border: none;
        }
        .btn:hover {
            background: #4C489D;
        }
        .msg {
            padding: 12px;
            background: #d4edda;
            color: #155724;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .msg-error {
            background: #f8d7da;
            color: #721c24;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #64748b;
            font-weight: 600;
        }
        .back-link:hover {
            color: #4C489D;
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php include 'sidebar.php'; ?>
    <div class="dashboard-main">
        <div class="dashboard-content-card">
            <h1>📢 Post New Announcement</h1>
            <?php echo $msg; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" placeholder="e.g., Club Meeting on Friday" required>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" rows="4" placeholder="Write your announcement details..." required></textarea>
                </div>
                <div class="form-group">
                    <label>Link to Event (Optional)</label>
                    <select name="event_id">
                        <option value="">-- No event --</option>
                        <?php while ($ev = $events->fetch_assoc()): ?>
                            <option value="<?php echo $ev['Event_id']; ?>"><?php echo htmlspecialchars($ev['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Publish Announcement</button>
                <a href="view_announcements.php" class="back-link">← Back to Announcements</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>