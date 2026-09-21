<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: blogs.php");

    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {

    header("Location: blogs.php");

    exit;
}


$backend =
    "http://backend/api/blogs.php?id=" .
    urlencode($id);


$ch = curl_init($backend);

curl_setopt(
    $ch,
    CURLOPT_CUSTOMREQUEST,
    "DELETE"
);

curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
);

curl_exec($ch);

curl_close($ch);


header("Location: blogs.php");

exit;