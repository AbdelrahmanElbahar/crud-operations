<?php

$backend = "http://backend";

function getData($url)
{
    $response = @file_get_contents($url);

    if ($response === false) {
        return [];
    }

    $result = json_decode($response, true);

    return $result['data'] ?? [];
}

$users = getData($backend . "/api/users.php");
$blogs = getData($backend . "/api/blogs.php");
$posts = getData($backend . "/api/posts.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>CRUD Dashboard</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>

<div class="layout">

    <aside class="sidebar">

        <h2>CRUD System</h2>

        <nav>

            <a href="index.php" class="active">
                Dashboard
            </a>

            <a href="users/users.php">
                Users
            </a>

            <a href="blogs/blogs.php">
                Blogs
            </a>

            <a href="posts/posts.php">
                Posts
            </a>

        </nav>

    </aside>


    <main class="main">

        <header class="header">

            <h1>Dashboard</h1>

        </header>


        <section class="cards">

            <div class="card">

                <h3>Users</h3>

                <p>
                    <?= count($users) ?>
                </p>

            </div>


            <div class="card">

                <h3>Blogs</h3>

                <p>
                    <?= count($blogs) ?>
                </p>

            </div>


            <div class="card">

                <h3>Posts</h3>

                <p>
                    <?= count($posts) ?>
                </p>

            </div>

        </section>

    </main>

</div>

</body>

</html>