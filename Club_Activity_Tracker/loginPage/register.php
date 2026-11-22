<?php include 'db_conn.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Register - Club Tracker</title><link rel="stylesheet" href="style.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></head>
<body>
<div class="container">
    <div class="screen">
        <div class="screen__content">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $full_name = $_POST['full_name'];
                $email = $_POST['email'];
                $password = $_POST['password']; // Make sure name="password" is used
                $role = $_POST['role'];
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Check if email exists
                $checkSql = "SELECT Member_id FROM Member WHERE email = ?";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->bind_param("s", $email);
                $checkStmt->execute();
                $checkStmt->store_result();

                if ($checkStmt->num_rows > 0) {
                    echo "<div style='color:#ffdddd; text-align:center; margin-bottom:10px;'>This email is already registered! <a href='login.php' style='color:#fff;'>Login instead</a></div>";
                } else {
                    // Force email_verified to 1 so you can log in right away
                    $sql = "INSERT INTO Member (full_name, email, role, password_hash, verification_token, email_verified) VALUES (?, ?, ?, ?, ?, 1)";
                    $token = bin2hex(random_bytes(32));
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssss", $full_name, $email, $role, $password_hash, $token);

                    if ($stmt->execute()) {
                        echo "<div style='color:#a8e6cf; font-weight:bold; text-align:center; margin-bottom:10px;'>✅ Account created successfully! Go to <a href='login.php' style='color:#fff;'>Login</a> page immediately.</div>";
                    }
                }
            }
            ?>
            <form class="login" method="post">
                <h1 class="login__title">Club Activity Tracker</h1>
                <div class="login__field"><i class="login__icon fas fa-user"></i><input type="text" class="login__input" name="full_name" placeholder="Full Name" required></div>
                <div class="login__field"><i class="login__icon fas fa-envelope"></i><input type="email" class="login__input" name="email" placeholder="Email Address" required></div>
                <div class="login__field"><i class="login__icon fas fa-lock"></i><input type="password" class="login__input" name="password" placeholder="Password" required></div>
                <div class="login__field"><i class="login__icon fas fa-tag"></i>
                    <select class="login__input" name="role" style="color:#4C489D; border-bottom:2px solid #D1D1D4;">
                        <option value="member">Member</option><option value="admin">Admin</option>
                    </select>
                </div>
                <button class="button login__submit" type="submit"><span class="button__text">Sign Up</span><i class="button__icon fas fa-chevron-right"></i></button>				
            </form>
            <div class="social-login">
                <div class="social-icons"><a href="login.php" class="social-login__icon" style="background:#fff; color:#5D54A4; border-radius:50%; width:40px; height:40px; line-height:40px; padding:0; margin-top:10px; text-shadow:none;"><i class="fas fa-sign-in-alt"></i></a></div>
                <h3 style="font-weight:400; font-size:14px; margin-top: 8px;">Already a member?</h3>
            </div>
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