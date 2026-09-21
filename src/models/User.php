<?php

class User
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Get all users
    public function getAll()
    {
        $sql = "SELECT id, name, email, created_at FROM users";

        $result = $this->conn->query($sql);

        $users = [];

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        return $users;
    }

    // Get one user
    public function getById($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT id, name, email, created_at
             FROM users
             WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // Create user
    public function create($name, $email, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare(
            "INSERT INTO users (name, email, password)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            "sss",
            $name,
            $email,
            $hashedPassword
        );

        return $stmt->execute();
    }

    // Update user
    public function update($id, $name, $email)
    {
        $stmt = $this->conn->prepare(
            "UPDATE users
             SET name = ?, email = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "ssi",
            $name,
            $email,
            $id
        );

        return $stmt->execute();
    }

    // Delete user
    public function delete($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM users
             WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}