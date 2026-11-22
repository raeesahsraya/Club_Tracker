<?php
session_start();
if (!isset($_SESSION['Member_id'])) { header("Location: login.php"); exit(); }
include 'db_conn.php';

$msg = "";
$e_id = isset($_GET['E_id']) ? (int)$_GET['E_id'] : 0;

// Handle the request submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $e_id_post = (int)$_POST['E_id'];
    $expected_return = $_POST['expected_return'];
    $member_id = $_SESSION['Member_id'];
    
    $stmt = $conn->prepare("INSERT INTO equipment_requests (E_id, Member_id, expected_return_date) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $e_id_post, $member_id, $expected_return);
    
    if ($stmt->execute()) {
        $msg = "<div style='color:#155724; background:#d4edda; padding:10px; border-radius:8px; margin-bottom:15px; border:1px solid #c3e6cb;'>✅ Request submitted successfully. Awaiting admin approval.</div>";
    } else {
        $msg = "<div style='color:#721c24; background:#f8d7da; padding:10px; border-radius:8px; margin-bottom:15px; border:1px solid #f5c6cb;'>⚠️ Error submitting request.</div>";
    }
}

// Fetch the item details to show the user what they are requesting
$item = $conn->query("SELECT * FROM equipment WHERE E_id = $e_id AND status = 'Available'")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Equipment - Club Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <div class="dashboard-main">
            <div class="dashboard-content-card" style="max-width: 600px; margin: 0 auto;">
                <div class="header">
                    <h1>📦 Request Equipment</h1>
                </div>
                
                <?php echo $msg; ?>

                <?php if ($item): ?>
                    <div class="section-card">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p style="color: #64748b;">Category: <?php echo htmlspecialchars($item['category']); ?></p>
                    </div>
                    
                    <form method="POST" style="margin-top: 20px;">
                        <input type="hidden" name="E_id" value="<?php echo $item['E_id']; ?>">
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #4C489D;">Expected Return Date</label>
                            <input type="date" name="expected_return" required min="<?php echo date('Y-m-d'); ?>" style="padding: 10px; width: 100%; border-radius: 8px; border: 1px solid #ccc; font-family: inherit;">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Confirm Request</button>
                        <a href="equipment_list.php" style="margin-left: 15px; color: #64748b; text-decoration: none; font-weight: 600;">Cancel</a>
                    </form>
                <?php else: ?>
                    <div style="padding: 20px; background: #f8f8ff; border-radius: 8px; text-align: center;">
                        <p style="color: #dc2626; margin-bottom: 15px;">This item is either invalid or currently unavailable.</p>
                        <a href="equipment_list.php" class="btn btn-primary" style="text-decoration: none;">← Go back to Equipment List</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>