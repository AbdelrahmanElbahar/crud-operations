<?php

$message = "";

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: posts.php");
    exit;
}


// Get blogs
$blogsResponse = @file_get_contents("http://backend/api/blogs.php");

$blogsResult = [];

if ($blogsResponse !== false) {
    $blogsResult = json_decode($blogsResponse, true);
}

$blogs = $blogsResult['data'] ?? [];


// Get current post
$postResponse = @file_get_contents(
    "http://backend/api/posts.php?id=" . urlencode($id)
);

$postResult = [];

if ($postResponse !== false) {
    $postResult = json_decode($postResponse, true);
}

$post = $postResult['data'] ?? null;


// If post doesn't exist
if (!$post) {
    echo "Post not found.";
    exit;
}


// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $backend = "http://backend/api/posts.php";

    $ch = curl_init($backend);

    $postData = [
        'id'      => $id,
        'blog_id' => $_POST['blog_id'] ?? '',
        'title'   => $_POST['title'] ?? '',
        'content' => $_POST['content'] ?? ''
    ];


    // Add new image only if user selected one
    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        $postData['image'] = new CURLFile(
            $_FILES['image']['tmp_name'],
            $_FILES['image']['type'],
            $_FILES['image']['name']
        );
    }


    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        $postData
    );

    curl_setopt(
        $ch,
        CURLOPT_RETURNTRANSFER,
        true
    );


    $response = curl_exec($ch);

    $curlError = curl_error($ch);

    curl_close($ch);


    if ($response === false) {

        $message =
            "Backend connection failed: " .
            $curlError;

    } else {

        $result = json_decode(
            $response,
            true
        );


        if (
            isset($result['success']) &&
            $result['success'] === true
        ) {

            header("Location: posts.php");
            exit;

        } else {

            $message =
                $result['message'] ??
                "Something went wrong.";
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Post</title>

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

            <a
                href="posts.php"
                class="active"
            >
                Posts
            </a>

        </nav>

    </aside>


    <main class="main">

        <header class="header">

            <h1>Edit Post</h1>

        </header>


        <section class="form-container">

            <?php if ($message): ?>

                <div class="error">

                    <?= htmlspecialchars($message) ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                enctype="multipart/form-data"
            >

                <!-- Blog -->

                <div class="form-group">

                    <label for="blog_id">
                        Blog
                    </label>

                    <select
                        name="blog_id"
                        id="blog_id"
                        required
                    >

                        <?php foreach ($blogs as $blog): ?>

                            <option
                                value="<?= $blog['id'] ?>"
                                <?= $blog['id'] == $post['blog_id']
                                    ? 'selected'
                                    : '' ?>
                            >

                                <?= htmlspecialchars(
                                    $blog['title']
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- Title -->

                <div class="form-group">

                    <label for="title">
                        Title
                    </label>

                    <input
                        type="text"
                        name="title"
                        id="title"
                        value="<?= htmlspecialchars(
                            $post['title']
                        ) ?>"
                        required
                    >

                </div>


                <!-- Content -->

                <div class="form-group">

                    <label for="content">
                        Content
                    </label>

                    <textarea
                        name="content"
                        id="content"
                        rows="8"
                        required
                    ><?= htmlspecialchars(
                        $post['content']
                    ) ?></textarea>

                </div>


                <!-- Current Image -->

                <?php if (!empty($post['image'])): ?>

                    <div class="form-group">

                        <label>
                            Current Image
                        </label>

                        <br>

                        <img
                            src="http://localhost:8082/uploads/posts/<?= htmlspecialchars($post['image']) ?>"
                            alt="Post image"
                            style="
                                width: 150px;
                                height: 100px;
                                object-fit: cover;
                                border-radius: 8px;
                            "
                        >

                    </div>

                <?php endif; ?>


                <!-- New Image -->

                <div class="form-group">

                    <label for="image">
                        Replace Image
                    </label>

                    <input
                        type="file"
                        name="image"
                        id="image"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                    >

                    <small>
                        Leave empty to keep the current image.
                    </small>

                </div>


                <!-- Buttons -->

                <button
                    type="submit"
                    class="button"
                >
                    Update Post
                </button>


                <a
                    href="posts.php"
                    class="button secondary"
                >
                    Cancel
                </a>

            </form>

        </section>

    </main>

</div>

</body>

</html>