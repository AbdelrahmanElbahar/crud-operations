<?php

$host = "db";
$username = "root";
$password = "root";
$database = "crud_operations";

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}