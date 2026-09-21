<?php

$backend = "http://backend/api/blogs.php";

$response = @file_get_contents($backend);

$result = [];

if ($response !== false) {
    $result = json_decode($response, true);
}

$blogs = $result['data'] ?? [];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Blogs</title>

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

            <a href="../users/users.php">
                Users
            </a>

            <a href="blogs.php" class="active">
                Blogs
            </a>

            <a href="../posts/posts.php">
                Posts
            </a>

        </nav>

    </aside>


    <main class="main">

        <header class="header">

            <h1>Blogs</h1>

        </header>


        <a
            href="create.php"
            class="btn"
        >
            Add Blog
        </a>

        <br>
        <br>


        <table>

            <thead>

                <tr>

                    <th>Image</th>

                    <th>ID</th>

                    <th>Title</th>

                    <th>Owner</th>

                    <th>Description</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($blogs)): ?>

                    <tr>

                        <td colspan="6">
                            No blogs found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($blogs as $blog): ?>

                        <tr>

                            <td>

                                <?php if (!empty($blog['image'])): ?>

                                    <img
                                        class="preview"
                                        src="http://localhost:8082/uploads/blogs/<?= htmlspecialchars($blog['image']) ?>"
                                        alt="Blog Image"
                                    >

                                <?php else: ?>

                                    No image

                                <?php endif; ?>

                            </td>


                            <td>
                                <?= htmlspecialchars($blog['id']) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($blog['title']) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($blog['user_name']) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($blog['description'] ?? '') ?>
                            </td>


                            <td>

                                <a
                                    href="edit.php?id=<?= $blog['id'] ?>"
                                    class="btn"
                                >
                                    Edit
                                </a>


                                <form
                                    action="delete.php"
                                    method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this blog?');"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= $blog['id'] ?>"
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