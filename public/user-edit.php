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

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($name === '' || $email === '') {

        $message = 'All fields are required.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = 'Please enter a valid email.';

    } else {

        try {

            if ($userModel->update((int)$id, $name, $email)) {
                header('Location: users.php');
                exit;
            }

        } catch (mysqli_sql_exception $e) {

            $message = 'Email already exists.';
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit User</title>
</head>

<body>

<h1>Edit User</h1>

<?php if ($message): ?>

    <p>
        <?= htmlspecialchars($message) ?>
    </p>

<?php endif; ?>

<form method="POST">

    <div>

        <label>Name:</label>

        <br>

        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($user['name']) ?>"
            required
        >

    </div>

    <br>

    <div>

        <label>Email:</label>

        <br>

        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($user['email']) ?>"
            required
        >

    </div>

    <br>

    <button type="submit">
        Update User
    </button>

</form>

<br>

<a href="users.php">
    Back to Users
</a>

</body>

</html>