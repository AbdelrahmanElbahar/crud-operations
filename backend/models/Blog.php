<?php

class Blog
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
                blogs.id,
                blogs.user_id,
                blogs.title,
                blogs.description,
                blogs.image,
                blogs.created_at,
                users.name AS user_name
            FROM blogs
            INNER JOIN users
                ON blogs.user_id = users.id
            ORDER BY blogs.id DESC
        ");

        $stmt->execute();

        $result = $stmt->get_result();

        $blogs = [];

        while ($row = $result->fetch_assoc()) {
            $blogs[] = $row;
        }

        return $blogs;
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT
                blogs.id,
                blogs.user_id,
                blogs.title,
                blogs.description,
                blogs.image,
                blogs.created_at,
                users.name AS user_name
            FROM blogs
            INNER JOIN users
                ON blogs.user_id = users.id
            WHERE blogs.id = ?
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function create(
        $userId,
        $title,
        $description,
        $image = null
    ) {
        $stmt = $this->conn->prepare("
            INSERT INTO blogs
                (user_id, title, description, image)
            VALUES
                (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isss",
            $userId,
            $title,
            $description,
            $image
        );

        return $stmt->execute();
    }

    public function update(
        $id,
        $userId,
        $title,
        $description,
        $image = null
    ) {
        if ($image !== null) {

            $stmt = $this->conn->prepare("
                UPDATE blogs
                SET
                    user_id = ?,
                    title = ?,
                    description = ?,
                    image = ?
                WHERE id = ?
            ");

            $stmt->bind_param(
                "isssi",
                $userId,
                $title,
                $description,
                $image,
                $id
            );

        } else {

            $stmt = $this->conn->prepare("
                UPDATE blogs
                SET
                    user_id = ?,
                    title = ?,
                    description = ?
                WHERE id = ?
            ");

            $stmt->bind_param(
                "issi",
                $userId,
                $title,
                $description,
                $id
            );
        }

        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("
            DELETE FROM blogs
            WHERE id = ?
        ");

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}