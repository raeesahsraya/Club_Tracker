<?php
session_start();
if (!isset($_SESSION['Member_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $event_id = intval($_POST['event_id']);
    $task_name = trim($_POST['task_name']);
    $member_id = intval($_POST['member_id']);
    $deadline = $_POST['deadline'] ?: null;
    $status = 'Pending';

    $stmt = $conn->prepare("INSERT INTO task (name, deadline, status, Event_id, Member_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $task_name, $deadline, $status, $event_id, $member_id);
    if ($stmt->execute()) {
        $msg = "Task assigned successfully.";
    } else {
        $msg = "Error: " . $conn->error;
    }
}

if (isset($_GET['delete_task'])) {
    $task_id = intval($_GET['delete_task']);
    $conn->query("DELETE FROM task WHERE T_id = $task_id");
    $msg = "Task removed.";
}

if (isset($_POST['update_status'])) {
    $task_id = intval($_POST['task_id']);
    $new_status = trim($_POST['new_status']);
    $conn->query("UPDATE task SET status = '$new_status' WHERE T_id = $task_id");
    $msg = "Status updated.";
}

$events = $conn->query("SELECT Event_id, name, event_date FROM events ORDER BY event_date DESC");
$members = $conn->query("SELECT Member_id, full_name FROM member ORDER BY full_name");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Event Team & Task Assignment</title>
    <link rel="stylesheet" href="style.css">
    <style>
        
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

        .event-block {
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
        }
        .event-block:last-child {
            border-bottom: none;
        }
        .task-list {
            margin: 10px 0;
        }
        .task-item {
            background: #f5f5ff;
            padding: 8px 12px;
            margin: 4px 0;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .btn {
            padding: 6px 14px;
            border-radius: 26px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
        }
        .btn-danger { background: #ff6b6b; color: #fff; }
        .btn-primary { background: #5D54A4; color: #fff; }
        .btn-success { background: #2ecc71; color: #fff; }
        .form-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin: 10px 0 20px 0;
            padding: 15px;
            background: #f8f8ff;
            border-radius: 12px;
        }
        .form-inline select,
        .form-inline input {
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .status-select {
            padding: 4px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .msg {
            padding: 12px 15px;
            border-radius: 10px;
            margin: 10px 0;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
<div style="max-width:1000px; margin:40px auto;">
    <div class="dashboard-content-card">

        
        <div class="header">
            <h1>🗂️ Event Team & Task Assignment</h1>
            <span class="user-badge">👋 <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
        </div>

        <!-- ===== CONTENT ===== -->
        <div class="content-area">

            <?php if (isset($msg)): ?>
                <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <?php while ($event = $events->fetch_assoc()): ?>
                <div class="event-block">
                    <h2><?php echo htmlspecialchars($event['name']); ?>
                        <small>(<?php echo $event['event_date']; ?>)</small>
                    </h2>

                    
                    <form method="post" class="form-inline">
                        <input type="hidden" name="event_id" value="<?php echo $event['Event_id']; ?>">
                        <input type="text" name="task_name" placeholder="Task/Role (e.g. Photography)" required>
                        <select name="member_id" required>
                            <option value="">Assign to member...</option>
                            <?php
                            $members->data_seek(0);
                            while ($m = $members->fetch_assoc()):
                            ?>
                                <option value="<?php echo $m['Member_id']; ?>"><?php echo htmlspecialchars($m['full_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                        <input type="date" name="deadline">
                        <button type="submit" name="add_task" class="btn btn-primary">Assign Task</button>
                    </form>

                    <div class="task-list">
                        <h4>Assigned Tasks:</h4>
                        <?php
                        $tasks = $conn->query("
                            SELECT t.T_id, t.name, t.deadline, t.status, m.full_name
                            FROM task t
                            LEFT JOIN member m ON t.Member_id = m.Member_id
                            WHERE t.Event_id = {$event['Event_id']}
                        ");
                        if ($tasks->num_rows == 0) {
                            echo "<p><em>No tasks assigned yet.</em></p>";
                        } else {
                            while ($task = $tasks->fetch_assoc()):
                        ?>
                            <div class="task-item">
                                <span>
                                    <strong><?php echo htmlspecialchars($task['name']); ?></strong>
                                    – <?php echo htmlspecialchars($task['full_name'] ?? 'Unassigned'); ?>
                                    <?php if ($task['deadline']): ?> | Deadline: <?php echo $task['deadline']; endif; ?>
                                </span>
                                <span>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="task_id" value="<?php echo $task['T_id']; ?>">
                                        <select name="new_status" class="status-select">
                                            <option value="Pending" <?php echo ($task['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="In Progress" <?php echo ($task['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="Completed" <?php echo ($task['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-success" style="padding:4px 12px;">Update</button>
                                    </form>
                                    <a href="?delete_task=<?php echo $task['T_id']; ?>" class="btn btn-danger" onclick="return confirm('Remove this task?')">✕</a>
                                </span>
                            </div>
                        <?php
                            endwhile;
                        }
                        ?>
                    </div>
                </div>
            <?php endwhile; ?>

        </div> 

        
        <div class="footer-link">
            <a href="dashboard.php" class="btn-secondary">← Back to Dashboard</a>
        </div>

    </div> 
</div>
</body>
</html>