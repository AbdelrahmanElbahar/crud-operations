<?php

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
// CREATE BLOG
// =========================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $backend =
        "http://backend/api/blogs.php";

    $ch = curl_init($backend);

    $postData = [
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
                ?? "Failed to create blog.";

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

    <title>Create Blog</title>

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

            <h1>Create Blog</h1>

        </header>


        <?php if (!empty($message)): ?>

            <div class="card">

                <p>
                    <?= htmlspecialchars($message) ?>
                </p>

            </div>

            <br>

        <?php endif; ?>


        <?php if (empty($users)): ?>

            <div class="card">

                <p>
                    You need to create a user
                    before creating a blog.
                </p>

            </div>

            <br>

            <a
                href="../users/create.php"
                class="btn"
            >
                Create User
            </a>

        <?php else: ?>


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

                        <option value="">
                            Select User
                        </option>


                        <?php foreach ($users as $user): ?>

                            <option
                                value="<?= $user['id'] ?>"
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
                    ></textarea>

                </div>


                <div class="form-group">

                    <label for="image">
                        Blog Image
                    </label>

                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/*"
                    >

                </div>


                <button type="submit">
                    Create Blog
                </button>


                <a
                    href="blogs.php"
                    class="btn"
                >
                    Cancel
                </a>


            </form>

        <?php endif; ?>

    </main>

</div>

</body>

</html>