<?php

$backend = "http://backend/api/posts.php";

$response = @file_get_contents($backend);

$result = [];

if ($response !== false) {
    $result = json_decode($response, true);
}

$posts = $result['data'] ?? [];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Posts</title>

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

            <a href="../blogs/blogs.php">
                Blogs
            </a>

            <a href="posts.php" class="active">
                Posts
            </a>

        </nav>

    </aside>


    <main class="main">

        <header class="header">

            <h1>Posts</h1>

        </header>


        <a
            href="create.php"
            class="btn"
        >
            Add Post
        </a>

        <br>
        <br>


        <table>

            <thead>

                <tr>

                    <th>Image</th>

                    <th>ID</th>

                    <th>Title</th>

                    <th>Blog</th>

                    <th>Content</th>

                    <th>Created At</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($posts)): ?>

                    <tr>

                        <td colspan="7">
                            No posts found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($posts as $post): ?>

                        <tr>

                            <td>

                                <?php if (!empty($post['image'])): ?>

                                    <img
                                        class="preview"
                                        src="http://localhost:8082/uploads/posts/<?= htmlspecialchars($post['image']) ?>"
                                        alt="Post Image"
                                    >

                                <?php else: ?>

                                    No image

                                <?php endif; ?>

                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $post['id']
                                ) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $post['title']
                                ) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $post['blog_title']
                                ) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $post['content']
                                ) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $post['created_at']
                                ) ?>
                            </td>


                            <td>

                                <a
                                    href="edit.php?id=<?= $post['id'] ?>"
                                    class="btn"
                                >
                                    Edit
                                </a>


                                <form
                                    action="delete.php"
                                    method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this post?');"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= $post['id'] ?>"
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