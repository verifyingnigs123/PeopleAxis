<?php
// Quick script to check OTP in database
date_default_timezone_set('Asia/Manila');

$db = new mysqli('localhost', 'root', '', 'peopleaxis');
if ($db->connect_error) {
    echo 'Error: ' . $db->connect_error;
    exit;
}

$email = 'doraidolloyd@gmail.com';
$result = $db->query("SELECT email, otp, is_used, expires_at FROM otp WHERE email='$email' ORDER BY created_at DESC LIMIT 1");
if ($result && $row = $result->fetch_assoc()) {
    echo "Email: " . $row['email'] . "\n";
    echo "OTP: " . $row['otp'] . "\n";
    echo "Is Used: " . $row['is_used'] . "\n";
    echo "Expires At: " . $row['expires_at'] . "\n";
    echo "Current Time: " . date('Y-m-d H:i:s') . "\n";
    echo "Expires Timestamp: " . strtotime($row['expires_at']) . "\n";
    echo "Current Timestamp: " . time() . "\n";
    echo "Expired? " . (strtotime($row['expires_at']) < time() ? 'YES' : 'NO') . "\n";
} else {
    echo "No OTP found for this email\n";
}
$db->close();
