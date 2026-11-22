<?php include 'db_conn.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Reset Password</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <div class="screen" style="height:500px;">
        <div class="screen__content">
            <?php
            if (isset($_GET['token'])) {
                $token = $_GET['token'];
                $sql = "SELECT Member_id FROM Member WHERE reset_token = ? AND reset_token_expiry > NOW()";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $update = "UPDATE Member SET password_hash = ?, reset_token = NULL, reset_token_expiry = NULL WHERE Member_id = ?";
                        $upStmt = $conn->prepare($update);
                        $upStmt->bind_param("si", $new_pass, $row['Member_id']);
                        $upStmt->execute();
                        echo "<div style='color:#a8e6cf; text-align:center; margin-top:20px;'>Password reset successfully! <a href='login.php' style='color:#fff;'>Login now</a></div>";
                        exit();
                    }
                    ?>
                    <form class="login" method="post">
                        <h1 class="login__title" style="margin-bottom:0; margin-top:-60px;">New Password</h1>
                        <div class="login__field"><i class="login__icon fas fa-lock"></i><input type="password" class="login__input" name="password" placeholder="Enter new password" required></div>
                        <button class="button login__submit" type="submit"><span class="button__text">Update Password</span><i class="button__icon fas fa-chevron-right"></i></button>
                    </form>
                    <?php
                } else {
                    echo "<div style='color:#ffdddd; text-align:center; margin-top:60px;'>Invalid or expired token.</div>";
                }
            } else {
                header("Location: forgot_password.php");
            }
            ?>
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