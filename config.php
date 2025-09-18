<?php
$host = "data.cliumscw44qs.ap-south-1.rds.amazonaws.com";  // RDS endpoint
$db_user = "subha";  // DB master username
$db_pass = "subha234";  // DB password
$db_name = "LoginDB";  // DB name

$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
