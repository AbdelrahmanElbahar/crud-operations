<?php

require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/models/User.php';

$userModel = new User($conn);

$users = $userModel->getAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
</head>

<body>

<h1>Users</h1>

<a href="user-create.php">Add User</a>

<br><br>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($users as $user): ?>

        <tr>

            <td>
                <?= htmlspecialchars($user['id']) ?>
            </td>

            <td>
                <?= htmlspecialchars($user['name']) ?>
            </td>

            <td>
                <?= htmlspecialchars($user['email']) ?>
            </td>

            <td>
                <?= htmlspecialchars($user['created_at']) ?>
            </td>

            <td>

                <a href="user-edit.php?id=<?= $user['id'] ?>">
                    Edit
                </a>

                |

                <a href="user-delete.php?id=<?= $user['id'] ?>"
                   onclick="return confirm('Delete this user?')">
                    Delete
                </a>

            </td>

        </tr>

    <?php endforeach; ?>

</table>

</body>
</html>