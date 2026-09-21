<?php

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: blogs.php");
    exit;
}

$backend = "http://backend/api/blogs.php";

$message = "";


// =========================
// LOAD USERS
// =========================

$usersResponse = @file_get_contents(
    "http://backend/api/users.php"
);

$usersResult = [];

if ($usersResponse !== false) {
    $usersResult = json_decode(
        $usersResponse,
        true
    );
}

$users = $usersResult['data'] ?? [];


// =========================
// LOAD BLOG
// =========================

$response = @file_get_contents(
    $backend . "?id=" . urlencode($id)
);

$blogResult = [];

if ($response !== false) {
    $blogResult = json_decode(
        $response,
        true
    );
}

$blog = $blogResult['data'] ?? null;

if (!$blog) {
    die("Blog not found.");
}


// =========================
// UPDATE BLOG
// =========================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ch = curl_init($backend);

    $postData = [
        'id' => $id,
        'user_id' => $_POST['user_id'] ?? '',
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? ''
    ];


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


    curl_setopt(
        $ch,
        CURLOPT_POST,
        true
    );

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

            header("Location: blogs.php");

            exit;

        } else {

            $message =
                $result['message']
                ?? "Failed to update blog.";

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

    <title>Edit Blog</title>

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

            <h1>Edit Blog</h1>

        </header>


        <?php if (!empty($message)): ?>

            <div class="card">

                <p>
                    <?= htmlspecialchars($message) ?>
                </p>

            </div>

            <br>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <div class="form-group">

                <label for="user_id">
                    Owner
                </label>

                <select
                    id="user_id"
                    name="user_id"
                    required
                >

                    <?php foreach ($users as $user): ?>

                        <option
                            value="<?= $user['id'] ?>"
                            <?= (
                                $user['id'] == $blog['user_id']
                            )
                            ? 'selected'
                            : ''
                            ?>
                        >

                            <?= htmlspecialchars(
                                $user['name']
                            ) ?>

                            -

                            <?= htmlspecialchars(
                                $user['email']
                            ) ?>

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
                    id="title"
                    name="title"
                    value="<?= htmlspecialchars(
                        $blog['title']
                    ) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="6"
                ><?= htmlspecialchars(
                    $blog['description'] ?? ''
                ) ?></textarea>

            </div>


            <div class="form-group">

                <label>
                    Current Image
                </label>

                <br>

                <?php if (!empty($blog['image'])): ?>

                    <img
                        class="preview"
                        src="http://localhost:8082/uploads/blogs/<?= htmlspecialchars($blog['image']) ?>"
                        alt="Current Blog Image"
                    >

                <?php else: ?>

                    <p>
                        No image
                    </p>

                <?php endif; ?>

            </div>


            <div class="form-group">

                <label for="image">
                    Replace Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/*"
                >

            </div>


            <button type="submit">
                Update Blog
            </button>


            <a
                href="blogs.php"
                class="btn"
            >
                Cancel
            </a>


        </form>

    </main>

</div>

</body>

</html>