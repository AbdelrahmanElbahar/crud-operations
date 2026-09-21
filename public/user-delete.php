<?php

require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/models/User.php';

$userModel = new User($conn);

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("Invalid user ID.");
}

$user = $userModel->getById((int)$id);

if (!$user) {
    die("User not found.");
}

$userModel->delete((int)$id);

header('Location: users.php');
exit;