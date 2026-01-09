<?php
// export_responses.php
session_start();
require_once 'config/db.php';

// Auth Check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

// Headers for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=responses_' . date('Y-m-d') . '.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, array('ID', 'Name', 'UID', 'Email', 'Drive Link', 'Submitted At'));

// Fetch the data
$query = "SELECT id, name, uid, email, drive_link, submitted_at FROM form_responses ORDER BY submitted_at DESC";
$result = $conn->query($query);

// Loop over the rows, outputting them
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
