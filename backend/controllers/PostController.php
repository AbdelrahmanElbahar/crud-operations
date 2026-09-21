<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../config/upload.php';

class PostController
{
    private $postModel;

    public function __construct($conn)
    {
        $this->postModel = new Post($conn);
    }

    public function getAll()
    {
        return $this->postModel->getAll();
    }

    public function getById($id)
    {
        return $this->postModel->getById($id);
    }

    public function create($data, $file = null)
    {
        if (
            empty($data['blog_id']) ||
            empty($data['title']) ||
            empty($data['content'])
        ) {
            throw new Exception(
                "Blog, title and content are required."
            );
        }

        $image = null;

        if ($file !== null) {
            $image = uploadImage($file, 'posts');
        }

        return $this->postModel->create(
            (int)$data['blog_id'],
            trim($data['title']),
            trim($data['content']),
            $image
        );
    }

    public function update($id, $data, $file = null)
    {
        if (
            empty($data['blog_id']) ||
            empty($data['title']) ||
            empty($data['content'])
        ) {
            throw new Exception(
                "Blog, title and content are required."
            );
        }

        $image = null;

        if ($file !== null) {
            $image = uploadImage($file, 'posts');
        }

        return $this->postModel->update(
            $id,
            (int)$data['blog_id'],
            trim($data['title']),
            trim($data['content']),
            $image
        );
    }

    public function delete($id)
    {
        return $this->postModel->delete($id);
    }
}