<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/upload.php';

class UserController
{
    private $userModel;

    public function __construct($conn)
    {
        $this->userModel = new User($conn);
    }

    public function getAll()
    {
        return $this->userModel->getAll();
    }

    public function getById($id)
    {
        return $this->userModel->getById($id);
    }

    public function create($data, $file = null)
    {
        if (
            empty($data['name']) ||
            empty($data['email']) ||
            empty($data['password'])
        ) {
            throw new Exception(
                "Name, email and password are required."
            );
        }

        $image = null;

        if ($file !== null) {
            $image = uploadImage($file, 'users');
        }

        return $this->userModel->create(
            trim($data['name']),
            trim($data['email']),
            $data['password'],
            $image
        );
    }

    public function update($id, $data, $file = null)
    {
        if (
            empty($data['name']) ||
            empty($data['email'])
        ) {
            throw new Exception(
                "Name and email are required."
            );
        }

        $image = null;

        if ($file !== null) {
            $image = uploadImage($file, 'users');
        }

        return $this->userModel->update(
            $id,
            trim($data['name']),
            trim($data['email']),
            $image
        );
    }

    public function delete($id)
    {
        return $this->userModel->delete($id);
    }
}