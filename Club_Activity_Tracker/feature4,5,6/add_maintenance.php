<?php
session_start();
if (!isset($_SESSION['Member_id'])) { header("Location: login.php"); exit(); }
include 'db_conn.php';

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $e_id = $_POST['equipment'];
    $cost = $_POST['cost'];
    $date = $_POST['date'];
    $type = $_POST['type'];
    $remark = $_POST['remark'];
    $update_status = isset($_POST['update_status']) ? $_POST['update_status'] : '';

    $stmt = $conn->prepare("INSERT INTO Maintenance (E_id, cost, date, type, remark) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("idsss", $e_id, $cost, $date, $type, $remark);
    if ($stmt->execute()) {
        if ($update_status == 'under_maintenance') {
            $upd = $conn->prepare("UPDATE Equipment SET status = 'under_maintenance' WHERE E_id = ?");
            $upd->bind_param("i", $e_id);
            $upd->execute();
        } elseif ($update_status == 'damaged') {
            $upd = $conn->prepare("UPDATE Equipment SET status = 'damaged' WHERE E_id = ?");
            $upd->bind_param("i", $e_id);
            $upd->execute();
        }
        $message = "<div style='color:#a8e6cf;'>Record added successfully.</div>";
    } else {
        $message = "<div style='color:#ffdddd;'>Error adding record.</div>";
    }
}

$equipment_list = $conn->query("SELECT E_id, name FROM Equipment ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Maintenance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-wrapper">
    <<?php include 'sidebar.php'; ?>

    <div class="dashboard-main">
        <div class="dashboard-content-card" style="max-width:600px; margin:0 auto;">
            <h1>New Maintenance Record</h1>
            <?php echo $message; ?>
            <form method="post" style="margin-top:20px;">
                <div class="login__field">
                    <label>Equipment</label>
                    <select name="equipment" class="login__input" required>
                        <option value="">Select Equipment</option>
                        <?php while($eq = $equipment_list->fetch_assoc()): ?>
                            <option value="<?= $eq['E_id'] ?>"><?= htmlspecialchars($eq['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="login__field">
                    <label>Cost (TK)</label>
                    <input type="number" step="0.01" name="cost" class="login__input" required>
                </div>
                <div class="login__field">
                    <label>Date</label>
                    <input type="date" name="date" class="login__input" required>
                </div>
                <div class="login__field">
                    <label>Type (e.g., Repair, Cleaning)</label>
                    <input type="text" name="type" class="login__input" required>
                </div>
                <div class="login__field">
                    <label>Remark</label>
                    <textarea name="remark" class="login__input" rows="3"></textarea>
                </div>
                <div class="login__field">
                    <label>Update equipment status to:</label>
                    <select name="update_status" class="login__input">
                        <option value="">No change</option>
                        <option value="under_maintenance">Under Maintenance</option>
                        <option value="damaged">Damaged</option>
                    </select>
                </div>
                <button class="button login__submit" type="submit">Add Record</button>
            </form>
            <p><a href="maintenance_records.php">← Back to Records</a></p>
        </div>
    </div>
</div>
</body>
</html>