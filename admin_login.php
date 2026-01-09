<?php
// admin_login.php (formerly index.html)
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_panel.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SecureView</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body class="auth-body">
    <div class="auth-card">
        <img src="assets/lock.png" alt="SecureView Lock" class="auth-logo">
        <h1 class="auth-title">SecureView</h1>
        <p class="auth-subtitle">Administrator Access</p>

        <form action="admin_auth.php" method="post" class="auth-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Admin Username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Admin Password" required>
            </div>

            <button type="submit" class="btn btn-danger auth-btn">Login as Admin</button>
        </form>
        <a href="login.php" class="footer-link">Back to User Login</a>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>

</html>