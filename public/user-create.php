<?php

require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/models/User.php';

$userModel = new User($conn);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name === '' || $email === '' || $password === '') {

        $message = 'All fields are required.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = 'Please enter a valid email.';

    } else {

        try {

            if ($userModel->create($name, $email, $password)) {
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
    <title>Add User</title>
</head>

<body>

<h1>Add User</h1>

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
            required
        >
    </div>

    <br>

    <div>
        <label>Password:</label>
        <br>

        <input
            type="password"
            name="password"
            required
        >
    </div>

    <br>

    <button type="submit">
        Create User
    </button>

</form>

<br>

<a href="users.php">
    Back to Users
</a>

</body>

</html>