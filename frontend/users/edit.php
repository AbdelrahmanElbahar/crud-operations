<?php

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: users.php");
    exit;
}

$backend = "http://backend/api/users.php";

$message = "";


// =========================
// GET USER
// =========================

$response = @file_get_contents(
    $backend . "?id=" . urlencode($id)
);

$userResult = [];

if ($response !== false) {
    $userResult = json_decode($response, true);
}

$user = $userResult['data'] ?? null;

if (!$user) {
    die("User not found.");
}


// =========================
// UPDATE USER
// =========================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ch = curl_init($backend);

    $postData = [
        'id' => $id,
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? ''
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

            header("Location: users.php");

            exit;

        } else {

            $message =
                $result['message']
                ?? "Failed to update user.";
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

    <title>Edit User</title>

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

            <h1>Edit User</h1>

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

                <label>
                    Current Image
                </label>

                <?php if (!empty($user['image'])): ?>

                    <br>

                    <img
                        class="preview"
                        src="http://localhost:8082/uploads/users/<?= htmlspecialchars($user['image']) ?>"
                        alt="Current User Image"
                    >

                <?php else: ?>

                    <p>No image</p>

                <?php endif; ?>

            </div>


            <div class="form-group">

                <label for="name">
                    Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= htmlspecialchars($user['name']) ?>"
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
                    value="<?= htmlspecialchars($user['email']) ?>"
                    required
                >

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
                Update User
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