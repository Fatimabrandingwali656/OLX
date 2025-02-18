<?php
$host = "localhost";
$dbname = "dbilywfhc7k0eo";
$username = "un6ftlkotgezy";
$password = "8i6esg2q8ur5";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
