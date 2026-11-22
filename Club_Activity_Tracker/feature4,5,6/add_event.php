<?php
session_start();
if (!isset($_SESSION['Member_id'])) { header("Location: login.php"); exit(); }
include 'db_conn.php';

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $venue = $_POST['venue'];
    $budget = $_POST['budget'];
    $description = $_POST['description'];
    $members = isset($_POST['members']) ? $_POST['members'] : [];

    $stmt = $conn->prepare("INSERT INTO Events (name, event_date, venue, budget, description, status) VALUES (?, ?, ?, ?, ?, 'planned')");
    $stmt->bind_param("sssds", $name, $date, $venue, $budget, $description);
    if ($stmt->execute()) {
        $event_id = $conn->insert_id;
        if (!empty($members)) {
            $task_stmt = $conn->prepare("INSERT INTO Task (name, deadline, status, Event_id, Member_id) VALUES (?, ?, ?, ?, ?)");
            foreach ($members as $member_id) {
                $task_name = "Responsible for event: " . $name;
                $task_deadline = $date;
                $task_status = 'pending';
                $task_stmt->bind_param("sssii", $task_name, $task_deadline, $task_status, $event_id, $member_id);
                $task_stmt->execute();
            }
        }
        $message = "<div style='color:#a8e6cf;'>Event created successfully!</div>";
    } else {
        $message = "<div style='color:#ffdddd;'>Error creating event.</div>";
    }
}

$members_list = $conn->query("SELECT Member_id, full_name FROM Member ORDER BY full_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="dashboard-main">
        <div class="dashboard-content-card" style="max-width:600px; margin:0 auto;">
            <h1>Create New Event</h1>
            <?php echo $message; ?>
            <form method="post" style="margin-top:20px;">
                <div class="login__field">
                    <label>Event Name</label>
                    <input type="text" name="name" class="login__input" required>
                </div>
                <div class="login__field">
                    <label>Date</label>
                    <input type="date" name="date" class="login__input" required>
                </div>
                <div class="login__field">
                    <label>Venue</label>
                    <input type="text" name="venue" class="login__input" required>
                </div>
                <div class="login__field">
                    <label>Budget (TK)</label>
                    <input type="number" step="0.01" name="budget" class="login__input" required>
                </div>
                <div class="login__field">
                    <label>Description</label>
                    <textarea name="description" class="login__input" rows="3"></textarea>
                </div>
                <div class="login__field">
                    <label>Responsible Members (hold Ctrl to select multiple)</label>
                    <select name="members[]" multiple class="login__input" style="height:100px;">
                        <?php while($m = $members_list->fetch_assoc()): ?>
                            <option value="<?= $m['Member_id'] ?>"><?= htmlspecialchars($m['full_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button class="button login__submit" type="submit">Create Event</button>
            </form>
            <p><a href="events_list.php">← Back to Events</a></p>
        </div>
    </div>
</div>
</body>
</html>