<?php

class Post
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("
            SELECT
                posts.id,
                posts.blog_id,
                posts.title,
                posts.content,
                posts.image,
                posts.created_at,
                posts.updated_at,
                blogs.title AS blog_title
            FROM posts
            INNER JOIN blogs
                ON posts.blog_id = blogs.id
            ORDER BY posts.id DESC
        ");

        $stmt->execute();

        $result = $stmt->get_result();

        $posts = [];

        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }

        return $posts;
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT
                posts.id,
                posts.blog_id,
                posts.title,
                posts.content,
                posts.image,
                posts.created_at,
                posts.updated_at,
                blogs.title AS blog_title
            FROM posts
            INNER JOIN blogs
                ON posts.blog_id = blogs.id
            WHERE posts.id = ?
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function create(
        $blogId,
        $title,
        $content,
        $image = null
    ) {
        $stmt = $this->conn->prepare("
            INSERT INTO posts
                (blog_id, title, content, image)
            VALUES
                (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isss",
            $blogId,
            $title,
            $content,
            $image
        );

        return $stmt->execute();
    }

    public function update(
        $id,
        $blogId,
        $title,
        $content,
        $image = null
    ) {
        if ($image !== null) {

            $stmt = $this->conn->prepare("
                UPDATE posts
                SET
                    blog_id = ?,
                    title = ?,
                    content = ?,
                    image = ?
                WHERE id = ?
            ");

            $stmt->bind_param(
                "isssi",
                $blogId,
                $title,
                $content,
                $image,
                $id
            );

        } else {

            $stmt = $this->conn->prepare("
                UPDATE posts
                SET
                    blog_id = ?,
                    title = ?,
                    content = ?
                WHERE id = ?
            ");

            $stmt->bind_param(
                "issi",
                $blogId,
                $title,
                $content,
                $id
            );
        }

        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("
            DELETE FROM posts
            WHERE id = ?
        ");

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}