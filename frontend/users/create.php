<?php

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $backend = "http://backend/api/users.php";

    $ch = curl_init($backend);

    $postData = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? ''
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
            "Backend connection failed: " . $curlError;

    } else {

        $result = json_decode(
            $response,
            true
        );

        if (
            isset($result['success']) &&
            $result['success'] === true
        ) {

            header("Location: users.php");

            exit;

        } else {

            $message =
                $result['message']
                ?? "Backend response: " . $response;
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

    <title>Create User</title>

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

            <h1>Create User</h1>

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

                <label for="name">
                    Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    required
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >

            </div>


            <div class="form-group">

                <label for="image">
                    Profile Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/*"
                >

            </div>


            <button type="submit">
                Create User
            </button>

            <a
                href="users.php"
                class="btn"
            >
                Cancel
            </a>

        </form>

    </main>

</div>

</body>

</html>