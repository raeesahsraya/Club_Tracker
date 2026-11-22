<?php
session_start();
if (!isset($_SESSION['Member_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_expense'])) {
    $event_id = intval($_POST['event_id']);
    $category = trim($_POST['category']);
    $amount = floatval($_POST['amount']);
    $expense_date = $_POST['expense_date'] ?: date('Y-m-d');
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("INSERT INTO expense (category, amount, expense_date, description, Event_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssi", $category, $amount, $expense_date, $description, $event_id);
    if ($stmt->execute()) {
        $msg = "Expense recorded.";
    } else {
        $msg = "Error: " . $conn->error;
    }
}

if (isset($_GET['delete_expense'])) {
    $id = intval($_GET['delete_expense']);
    $conn->query("DELETE FROM expense WHERE id = $id");
    $msg = "Expense deleted.";
}

$events = $conn->query("SELECT Event_id, name, budget FROM events ORDER BY event_date DESC");

$selected_event = isset($_GET['event_id']) ? intval($_GET['event_id']) : ($events->num_rows ? $events->fetch_assoc()['Event_id'] : 0);
$events->data_seek(0);

$event_info = null;
$total_expense = 0;
$expenses = [];
if ($selected_event) {
    $result = $conn->query("SELECT name, budget FROM events WHERE Event_id = $selected_event");
    $event_info = $result->fetch_assoc();
    $expenses_result = $conn->query("SELECT id, category, amount, expense_date, description FROM expense WHERE Event_id = $selected_event ORDER BY expense_date DESC");
    while ($row = $expenses_result->fetch_assoc()) {
        $expenses[] = $row;
        $total_expense += $row['amount'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Event Expense Tracking</title>
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

        .budget-bar {
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            height: 30px;
            margin: 15px 0;
            max-width: 100%;
        }
        .budget-fill {
            height: 100%;
            background: #5D54A4;
            color: #fff;
            line-height: 30px;
            text-align: center;
            border-radius: 10px;
            white-space: nowrap;
            min-width: 30px;
            transition: width 0.3s ease;
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
            margin: 15px 0;
            padding: 15px;
            background: #f8f8ff;
            border-radius: 12px;
        }
        .form-inline input,
        .form-inline select {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            flex: 1 1 auto;
            min-width: 120px;
            max-width: 100%;
        }
        .form-inline input[type="number"] { max-width: 140px; }
        .form-inline input[type="date"] { max-width: 160px; }
        .form-inline input[type="text"] { flex: 2; min-width: 150px; }

        .table-wrapper {
            overflow-x: auto;
            margin: 20px 0 10px 0;
            border-radius: 12px;
            border: 1px solid #eee;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            white-space: nowrap;
        }
        th { background: #f0f0ff; color: #4C489D; font-weight: 700; }
        td:last-child { text-align: center; min-width: 50px; }

        .event-selector {
            background: #f8f8ff;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .event-selector select {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            min-width: 250px;
            max-width: 100%;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        .info-item {
            background: #f8f8ff;
            padding: 12px 15px;
            border-radius: 10px;
            border-left: 4px solid #5D54A4;
        }
        .info-item label { font-size: 12px; color: #7875B5; display: block; }
        .info-item .value { font-size: 20px; font-weight: 700; color: #4C489D; }
        .msg {
            padding: 12px 15px;
            border-radius: 10px;
            margin: 10px 0;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .msg-error { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }

        @media (max-width: 768px) {
            .form-inline { flex-direction: column; align-items: stretch; }
            .form-inline input,
            .form-inline select {
                width: 100% !important;
                max-width: 100% !important;
                min-width: unset;
            }
            .info-grid { grid-template-columns: 1fr 1fr; }
            th, td { padding: 8px 10px; font-size: 14px; }
        }
        @media (max-width: 480px) {
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div style="max-width:1000px; margin:40px auto;">
    <div class="dashboard-content-card">

       
        <div class="header">
            <h1>💰 Event Expense Tracking</h1>
            <span class="user-badge">👋 <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
        </div>

       
        <div class="content-area">

            <?php if (isset($msg)): ?>
                <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <div class="event-selector">
                <form method="get">
                    <label style="font-weight:600; margin-right:10px;">Select Event:</label>
                    <select name="event_id" onchange="this.form.submit()">
                        <option value="">-- Choose an event --</option>
                        <?php while ($ev = $events->fetch_assoc()): ?>
                            <option value="<?php echo $ev['Event_id']; ?>" <?php echo ($selected_event == $ev['Event_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ev['name']); ?> (Budget: $<?php echo number_format($ev['budget'], 2); ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
            </div>

            <?php if ($event_info && $selected_event): ?>
                <h2><?php echo htmlspecialchars($event_info['name']); ?></h2>

                <div class="info-grid">
                    <div class="info-item" style="border-left-color:#5D54A4;">
                        <label>Budget</label>
                        <div class="value">$<?php echo number_format($event_info['budget'], 2); ?></div>
                    </div>
                    <div class="info-item" style="border-left-color:#3498db;">
                        <label>Total Expenses</label>
                        <div class="value">$<?php echo number_format($total_expense, 2); ?></div>
                    </div>
                    <div class="info-item" style="border-left-color:<?php echo ($event_info['budget'] - $total_expense < 0) ? '#e74c3c' : '#2ecc71'; ?>;">
                        <label>Remaining</label>
                        <div class="value">$<?php echo number_format($event_info['budget'] - $total_expense, 2); ?></div>
                    </div>
                </div>

                <?php
                $percent = ($event_info['budget'] > 0) ? min(100, ($total_expense / $event_info['budget']) * 100) : 0;
                $color = ($percent > 90) ? '#ff6b6b' : (($percent > 70) ? '#f39c12' : '#5D54A4');
                ?>
                <div class="budget-bar">
                    <div class="budget-fill" style="width: <?php echo $percent; ?>%; background: <?php echo $color; ?>;">
                        <?php echo round($percent, 1); ?>% used
                    </div>
                </div>

                <form method="post" class="form-inline">
                    <input type="hidden" name="event_id" value="<?php echo $selected_event; ?>">
                    <input type="text" name="category" placeholder="Category (e.g. Food)" required>
                    <input type="number" step="0.01" name="amount" placeholder="Amount" required>
                    <input type="date" name="expense_date" value="<?php echo date('Y-m-d'); ?>">
                    <input type="text" name="description" placeholder="Description (optional)">
                    <button type="submit" name="add_expense" class="btn btn-primary">Add Expense</button>
                </form>

                <?php if (count($expenses) > 0): ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th style="text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($expenses as $exp): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($exp['category']); ?></td>
                                    <td>$<?php echo number_format($exp['amount'], 2); ?></td>
                                    <td><?php echo $exp['expense_date']; ?></td>
                                    <td><?php echo htmlspecialchars($exp['description'] ?: '—'); ?></td>
                                    <td style="text-align:center;">
                                        <a href="?delete_expense=<?php echo $exp['id']; ?>&event_id=<?php echo $selected_event; ?>"
                                           class="btn btn-danger"
                                           onclick="return confirm('Delete this expense?')"
                                           style="padding:4px 12px;">✕</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color:#64748b; padding:20px; background:#f8f8ff; border-radius:10px; text-align:center;">
                        <em>No expenses recorded yet for this event.</em>
                    </p>
                <?php endif; ?>

            <?php elseif ($selected_event && !$event_info): ?>
                <p style="color:#e74c3c;">Event not found. Please select a valid event.</p>
            <?php else: ?>
                <p style="color:#64748b; padding:30px; text-align:center; background:#f8f8ff; border-radius:10px;">
                    Please select an event from the dropdown above to start tracking expenses.
                </p>
            <?php endif; ?>

        </div> 

        
        <div class="footer-link">
            <a href="dashboard.php" class="btn-secondary">← Back to Dashboard</a>
        </div>

    </div> 
</div>
</body>
</html>