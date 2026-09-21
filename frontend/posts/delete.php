<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: posts.php");

    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {

    header("Location: posts.php");

    exit;
}


$backend =
    "http://backend/api/posts.php?id=" .
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


header("Location: posts.php");

exit;