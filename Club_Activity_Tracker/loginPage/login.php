<?php 
session_start(); 

// NEW FIX: If user is already logged in, send them straight to dashboard
if (isset($_SESSION['Member_id'])) {
    header("Location: dashboard.php");
    exit();
}

include 'db_conn.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Login - Club Tracker</title><link rel="stylesheet" href="style.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></head>
<body>
<div class="container">
    <div class="screen">
        <div class="screen__content">
            <form class="login" method="post">
                <h1 class="login__title">Club Activity Tracker</h1>
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $email = $_POST['email'];
                    $password_input = $_POST['password'];

                    $sql = "SELECT * FROM Member WHERE email = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($row = $result->fetch_assoc()) {
                        if (password_verify($password_input, $row['password_hash'])) {
                            $_SESSION['Member_id'] = $row['Member_id'];
                            $_SESSION['full_name'] = $row['full_name'];
                            $_SESSION['role'] = $row['role'];
                            header("Location: dashboard.php");
                            exit();
                        } else {
                            echo "<div style='color: #d9534f; background: #f8d7da; border: 1px solid #f5c6cb; font-weight: 700; font-size: 13px; text-align: center; padding: 8px 12px; border-radius: 8px; width: 100%; box-sizing: border-box; margin: 15px auto 5px auto;'> Incorrect password!</div>";
                        }
                    } else {
                        echo "<div style='color: #d9534f; background: #f8d7da; border: 1px solid #f5c6cb; font-weight: 700; font-size: 13px; text-align: center; padding: 8px 12px; border-radius: 8px; width: 100%; box-sizing: border-box; margin: 15px auto 5px auto;'> User not found!</div>";
                    }
                }
                ?>
                <div class="login__field"><i class="login__icon fas fa-user"></i><input type="email" class="login__input" name="email" placeholder="Email Address" required></div>
                <div class="login__field"><i class="login__icon fas fa-lock"></i><input type="password" class="login__input" name="password" placeholder="Password" required></div>
                <button class="button login__submit" type="submit"><span class="button__text">Log In</span><i class="button__icon fas fa-chevron-right"></i></button>				
            </form>
            <div class="social-login">
                <h3 style="font-weight:400; font-size:14px; margin-bottom:10px;">Forgot password?</h3>
                <div class="social-icons">
                    <a href="forgot_password.php" class="social-login__icon" style="background:#fff; color:#5D54A4; border-radius:50%; width:40px; height:40px; line-height:40px; padding:0; margin-top:10px; text-shadow:none;"><i class="fas fa-key"></i></a>
                    <a href="register.php" class="social-login__icon" style="background:#fff; color:#5D54A4; border-radius:50%; width:40px; height:40px; line-height:40px; padding:0; margin-top:10px; text-shadow:none;"><i class="fas fa-user-plus"></i></a>
                </div>
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