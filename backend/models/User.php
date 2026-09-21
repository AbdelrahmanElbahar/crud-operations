<?php

class User
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
                id,
                name,
                email,
                image,
                created_at
            FROM users
            ORDER BY id DESC
        ");

        $stmt->execute();

        $result = $stmt->get_result();

        $users = [];

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        return $users;
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT
                id,
                name,
                email,
                image,
                created_at
            FROM users
            WHERE id = ?
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function create(
        $name,
        $email,
        $password,
        $image = null
    ) {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $this->conn->prepare("
            INSERT INTO users
                (name, email, password, image)
            VALUES
                (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssss",
            $name,
            $email,
            $hashedPassword,
            $image
        );

        return $stmt->execute();
    }

    public function update(
        $id,
        $name,
        $email,
        $image = null
    ) {
        if ($image !== null) {

            $stmt = $this->conn->prepare("
                UPDATE users
                SET
                    name = ?,
                    email = ?,
                    image = ?
                WHERE id = ?
            ");

            $stmt->bind_param(
                "sssi",
                $name,
                $email,
                $image,
                $id
            );

        } else {

            $stmt = $this->conn->prepare("
                UPDATE users
                SET
                    name = ?,
                    email = ?
                WHERE id = ?
            ");

            $stmt->bind_param(
                "ssi",
                $name,
                $email,
                $id
            );
        }

        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("
            DELETE FROM users
            WHERE id = ?
        ");

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}