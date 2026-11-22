<?php include 'db_conn.php'; require 'send_mail.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Forgot Password</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <div class="screen" style="height:450px;">
        <div class="screen__content">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $email = $_POST['email'];
                $sql = "SELECT Member_id FROM Member WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    $update = "UPDATE Member SET reset_token = ?, reset_token_expiry = ? WHERE Member_id = ?";
                    $upStmt = $conn->prepare($update);
                    $upStmt->bind_param("ssi", $token, $expiry, $row['Member_id']);
                    $upStmt->execute();

                    $resetLink = "http://localhost/club_tracker/reset_password.php?token=" . $token;
                    $htmlBody = "<h2>Reset Your Password</h2><p>Click <a href='$resetLink'>here</a> to reset your password. This link expires in 1 hour.</p>";
                    sendEmail($email, "Password Reset - Club Tracker", $htmlBody);
                    echo "<div style='color:#a8e6cf; text-align:center; margin-top:20px;'>Reset link sent! Check your email.</div>";
                } else {
                    echo "<div style='color:#ffdddd; text-align:center; margin-top:20px;'>Email not found.</div>";
                }
            }
            ?>
            <form class="login" method="post">
                <h1 class="login__title" style="margin-bottom:0; margin-top:-50px;">Reset Password</h1>
                <div class="login__field"><i class="login__icon fas fa-envelope"></i><input type="email" class="login__input" name="email" placeholder="Enter your email" required></div>
                <button class="button login__submit" type="submit"><span class="button__text">Send Reset Link</span><i class="button__icon fas fa-chevron-right"></i></button>
            </form>
            <div style="text-align:center; margin-top:-10px;"><a href="login.php" style="color:#fff;">Back to Login</a></div>
        </div>
        <div class="screen__background">
            <span class="screen__background__shape screen__background__shape4"></span>
            <span class="screen__background__shape screen__background__shape3"></span>		
            <span class="screen__background__shape screen__background__shape2"></span>
            <span class="screen__background__shape screen__background__shape1"></span>
        </div>		
    </div>
</div>
</body>
</html>