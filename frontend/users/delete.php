<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: users.php");
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    header("Location: users.php");
    exit;
}

$backend = "http://backend/api/users.php?id=" . urlencode($id);

$ch = curl_init($backend);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_exec($ch);

curl_close($ch);

header("Location: users.php");

exit;