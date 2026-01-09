<?php
// config/db.php

$host = 'localhost';
$dbname = 'secureview';
$username = 'root';
$password = ''; // You should change this to your actual password or use env vars

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
