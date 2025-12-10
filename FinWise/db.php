<?php
$conn = new mysqli("localhost", "root", "root", "finance_app");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
