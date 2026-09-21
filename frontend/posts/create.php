<?php

$message = "";

// Get blogs from backend
$blogsResponse = @file_get_contents("http://backend/api/blogs.php");

$blogsResult = [];

if ($blogsResponse !== false) {
    $blogsResult = json_decode($blogsResponse, true);
}

$blogs = $blogsResult['data'] ?? [];


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $backend = "http://backend/api/posts.php";

    $ch = curl_init($backend);

    $postData = [
        'blog_id' => $_POST['blog_id'] ?? '',
        'title'   => $_POST['title'] ?? '',
        'content' => $_POST['content'] ?? ''
    ];

    // Add image if selected
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    $curlError = curl_error($ch);

    curl_close($ch);


    if ($response === false) {

        $message = "Backend connection failed: " . $curlError;

    } else {

        $result = json_decode($response, true);

        if (
            isset($result['success']) &&
            $result['success'] === true
        ) {

            header("Location: posts.php");
            exit;

        } else {

            $message = $result['message'] ?? "Something went wrong.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Post</title>

    <link rel="stylesheet" href="../css/style.css">

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

            <h1>Create Post</h1>

        </header>


        <section class="form-container">

            <?php if ($message): ?>

                <div class="error">

                    <?= htmlspecialchars($message) ?>

                </div>

            <?php endif; ?>


            <?php if (empty($blogs)): ?>

                <div class="error">

                    You need to create a blog before creating a post.

                </div>

                <a href="../blogs/create.php" class="button">
                    Create Blog
                </a>

            <?php else: ?>


                <form
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <div class="form-group">

                        <label for="blog_id">
                            Blog
                        </label>

                        <select
                            name="blog_id"
                            id="blog_id"
                            required
                        >

                            <option value="">
                                Select a blog
                            </option>

                            <?php foreach ($blogs as $blog): ?>

                                <option
                                    value="<?= $blog['id'] ?>"
                                >

                                    <?= htmlspecialchars($blog['title']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="title">
                            Title
                        </label>

                        <input
                            type="text"
                            name="title"
                            id="title"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="content">
                            Content
                        </label>

                        <textarea
                            name="content"
                            id="content"
                            rows="8"
                            required
                        ></textarea>

                    </div>


                    <div class="form-group">

                        <label for="image">
                            Image
                        </label>

                        <input
                            type="file"
                            name="image"
                            id="image"
                            accept="image/jpeg,image/png,image/webp,image/gif"
                        >

                    </div>


                    <button
                        type="submit"
                        class="button"
                    >
                        Create Post
                    </button>


                    <a
                        href="posts.php"
                        class="button secondary"
                    >
                        Cancel
                    </a>

                </form>

            <?php endif; ?>

        </section>

    </main>

</div>

</body>

</html>