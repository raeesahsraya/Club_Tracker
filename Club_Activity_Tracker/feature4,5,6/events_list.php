<?php
session_start();
if (!isset($_SESSION['Member_id'])) { header("Location: login.php"); exit(); }
include 'db_conn.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="dashboard-main">
        <div class="dashboard-content-card">
            <div class="header">
                <h1>Events</h1>
                <a href="add_event.php" class="btn btn-primary">+ New Event</a>
            </div>

            <div style="margin-top:20px;">
                <?php
                $sql = "SELECT * FROM Events ORDER BY event_date DESC";
                $result = $conn->query($sql);
                if ($result->num_rows == 0) {
                    echo "<p>No events yet.</p>";
                } else {
                    echo "<div style='display:grid; grid-template-columns: repeat(auto-fill, minmax(300px,1fr)); gap:20px;'>";
                    while ($row = $result->fetch_assoc()) {
                        $resp_sql = "SELECT m.full_name FROM Task t JOIN Member m ON t.Member_id = m.Member_id WHERE t.Event_id = ?";
                        $resp_stmt = $conn->prepare($resp_sql);
                        $resp_stmt->bind_param("i", $row['Event_id']);
                        $resp_stmt->execute();
                        $resp_res = $resp_stmt->get_result();
                        $responsibles = [];
                        while ($r = $resp_res->fetch_assoc()) {
                            $responsibles[] = $r['full_name'];
                        }
                        $resp_list = empty($responsibles) ? 'None' : implode(', ', $responsibles);

                        echo "<div class='section-card'>";
                        echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                        echo "<p><strong>Date:</strong> " . $row['event_date'] . "</p>";
                        echo "<p><strong>Venue:</strong> " . htmlspecialchars($row['venue']) . "</p>";
                        echo "<p><strong>Budget:</strong> TK" . number_format($row['budget'], 2) . "</p>";
                        echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
                        echo "<p><strong>Responsible:</strong> " . $resp_list . "</p>";
                        echo "</div>";
                    }
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>