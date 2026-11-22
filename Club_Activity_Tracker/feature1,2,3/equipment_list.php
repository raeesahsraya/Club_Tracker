<?php
session_start();
if (!isset($_SESSION['Member_id'])) { header("Location: login.php"); exit(); }
include 'db_conn.php';

$msg = "";

// Admin Adding Equipment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_equipment'])) {
    if ($_SESSION['role'] === 'admin') {
        $name = trim($_POST['name']);
        $category = trim($_POST['category']);
        
        $stmt = $conn->prepare("INSERT INTO Equipment (name, category, status) VALUES (?, ?, 'Available')");
        $stmt->bind_param("ss", $name, $category);
        if ($stmt->execute()) {
            $msg = "<div style='color:#155724; background:#d4edda; padding:10px; border-radius:8px; margin-bottom:15px;'>✅ Item added.</div>";
        }
    }
}

$equipment = $conn->query("SELECT * FROM Equipment ORDER BY E_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Equipment List</title><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="dashboard-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="dashboard-main">
            <div class="dashboard-content-card">
                <div class="header"><h1>🔧 Equipment Inventory</h1></div>
                <?php echo $msg; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <div class="section-card">
                    <h3>+ Add New Equipment</h3>
                    <form method="POST" style="display:flex; gap:10px; margin-top:10px; flex-wrap:wrap;">
                        <input type="text" name="name" placeholder="Item Name" required style="padding:8px; border-radius:6px; border:1px solid #ccc; flex:1;">
                        <select name="category" required style="padding:8px; border-radius:6px; border:1px solid #ccc;">
                            <option value="Electronics">Electronics</option><option value="Audio/Visual">Audio/Visual</option>
                            <option value="Miscellaneous">Miscellaneous</option>
                        </select>
                        <button type="submit" name="add_equipment" class="btn btn-primary">Add Item</button>
                    </form>
                </div>
                <?php endif; ?>

                <table style="width:100%; border-collapse:collapse; margin-top:20px;">
                    <thead style="background:#f0f0ff; color:#4C489D;">
                        <tr><th style="padding:10px; text-align:left;">ID</th><th style="padding:10px; text-align:left;">Name</th><th style="padding:10px; text-align:left;">Status</th><th style="padding:10px; text-align:left;">Action</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $equipment->fetch_assoc()): ?>
                        <tr style="border-bottom:1px solid #ddd;">
                            <td style="padding:10px;">#<?php echo $row['E_id']; ?></td>
                            <td style="padding:10px;"><strong><?php echo htmlspecialchars($row['name']); ?></strong> (<?php echo htmlspecialchars($row['category']); ?>)</td>
                            <td style="padding:10px;">
                                <span style="background:#e9ecef; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:bold; color: <?php echo ($row['status'] == 'Available') ? '#059669' : '#dc2626'; ?>;">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td style="padding:10px;">
                                <?php if ($row['status'] == 'Available'): ?>
                                    <a href="request_equipment.php?E_id=<?php echo $row['E_id']; ?>" class="btn btn-primary" style="padding:4px 12px; font-size:12px;">Request</a>
                                <?php endif; ?>
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