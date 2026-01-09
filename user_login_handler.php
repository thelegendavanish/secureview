<?php
// user_login_handler.php
session_start();
require_once 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $uid = trim($_POST['uid'] ?? '');

    if (empty($name) || empty($uid)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href='login.php';</script>";
        exit();
    }

    // Check credentials against users table
    // Note: The original code selected from 'users'. 
    // If the 'users' table doesn't exist in my database.sql, I should create it or assume it exists.
    // The original code had: SELECT * FROM users WHERE name = ? AND uid = ?
    // I will assume this table exists or I should add it to database.sql.
    // However, for the user to login, they need to be in the DB.

    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ? AND uid = ?");
    if (!$stmt) {
        // Fallback if table query fails (e.g. table doesn't exist yet)
        // For the sake of this refactor, I will allow login if not found? No, that's insecure.
        // I'll stick to strict check.
        die("Error checking user: " . $conn->error);
    }

    $stmt->bind_param("ss", $name, $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['name'] = $name;
        $_SESSION['uid'] = $uid;
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];

        // Log login
        $log_stmt = $conn->prepare("INSERT INTO user_logins (name, uid, ip_address) VALUES (?, ?, ?)");
        $log_stmt->bind_param("sss", $name, $uid, $_SESSION['ip']);
        $log_stmt->execute();

        header("Location: user_panel.php");
        exit();
    } else {
        echo "<script>alert('Invalid Login Credentials.'); window.location.href='login.php';</script>";
    }
} else {
    header("Location: login.php");
    exit();
}
