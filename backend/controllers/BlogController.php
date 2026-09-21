<?php

require_once __DIR__ . '/../models/Blog.php';
require_once __DIR__ . '/../config/upload.php';

class BlogController
{
    private $blogModel;

    public function __construct($conn)
    {
        $this->blogModel = new Blog($conn);
    }

    public function getAll()
    {
        return $this->blogModel->getAll();
    }

    public function getById($id)
    {
        return $this->blogModel->getById($id);
    }

    public function create($data, $file = null)
    {
        if (
            empty($data['user_id']) ||
            empty($data['title'])
        ) {
            throw new Exception(
                "User and title are required."
            );
        }

        $image = null;

        if ($file !== null) {
            $image = uploadImage($file, 'blogs');
        }

        return $this->blogModel->create(
            (int)$data['user_id'],
            trim($data['title']),
            trim($data['description'] ?? ''),
            $image
        );
    }

    public function update($id, $data, $file = null)
    {
        if (
            empty($data['user_id']) ||
            empty($data['title'])
        ) {
            throw new Exception(
                "User and title are required."
            );
        }

        $image = null;

        if ($file !== null) {
            $image = uploadImage($file, 'blogs');
        }

        return $this->blogModel->update(
            $id,
            (int)$data['user_id'],
            trim($data['title']),
            trim($data['description'] ?? ''),
            $image
        );
    }

    public function delete($id)
    {
        return $this->blogModel->delete($id);
    }
}