<?php
// login.php (formerly userlogin.html)
session_start();
if (isset($_SESSION['uid'])) {
    header('Location: user_panel.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - SecureView</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body class="auth-body">
    <div class="auth-card">
        <img src="assets/lock.png" alt="SecureView Lock" class="auth-logo">
        <h1 class="auth-title">SecureView</h1>
        <p class="auth-subtitle">User Access Portal</p>

        <form action="user_login_handler.php" method="post" class="auth-form">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="uid">Unique ID (UID)</label>
                <input type="text" id="uid" name="uid" placeholder="Enter your UID" required>
            </div>

            <button type="submit" class="btn btn-primary auth-btn">Login</button>
        </form>
        <a href="admin_login.php" class="footer-link">Admin Login</a>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>

</html>