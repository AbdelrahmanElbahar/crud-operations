<?php

$backend = "http://backend/api/users.php";

$response = @file_get_contents($backend);

$result = [];

if ($response !== false) {
    $result = json_decode($response, true);
}

$users = $result['data'] ?? [];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Users</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>

<body>

<div class="layout">

    <aside class="sidebar">

        <h2>CRUD System</h2>

        <nav>

            <a href="../index.php">
                Dashboard
            </a>

            <a href="users.php" class="active">
                Users
            </a>

            <a href="../blogs/blogs.php">
                Blogs
            </a>

            <a href="../posts/posts.php">
                Posts
            </a>

        </nav>

    </aside>


    <main class="main">

        <header class="header">

            <h1>Users</h1>

        </header>


        <a href="create.php" class="btn">
            Add User
        </a>

        <br>
        <br>


        <table>

            <thead>

                <tr>

                    <th>Image</th>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Created At</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($users)): ?>

                    <tr>

                        <td colspan="6">
                            No users found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($users as $user): ?>

                        <tr>

                            <td>

                                <?php if (!empty($user['image'])): ?>

                                    <img
                                        class="preview"
                                        src="http://localhost:8082/uploads/users/<?= htmlspecialchars($user['image']) ?>"
                                        alt="User Image"
                                    >

                                <?php else: ?>

                                    No image

                                <?php endif; ?>

                            </td>


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

                                <a
                                    href="edit.php?id=<?= $user['id'] ?>"
                                    class="btn"
                                >
                                    Edit
                                </a>


                                <form
                                    action="delete.php"
                                    method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this user?');"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= $user['id'] ?>"
                                    >

                                    <button type="submit">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </main>

</div>

</body>

</html>