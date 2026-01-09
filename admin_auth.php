<?php
// admin_auth.php
session_start();
require_once 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href='admin_login.php';</script>";
        exit();
    }

    // Check credentials against admins table
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        // Verify Password
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $row['id'];
            header("Location: admin_panel.php");
            exit();
        }
    }

    // Login Failed
    echo "<script>alert('Invalid Username or Password'); window.location.href='admin_login.php';</script>";
    exit();
} else {
    header("Location: admin_login.php");
    exit();
}
