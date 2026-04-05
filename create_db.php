<?php
$host = "127.0.0.1";
$root = "root";
$pass = "";

try {
    $dbh = new PDO("mysql:host=$host", $root, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->exec("CREATE DATABASE IF NOT EXISTS smartgate");
    echo "Database created successfully";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
