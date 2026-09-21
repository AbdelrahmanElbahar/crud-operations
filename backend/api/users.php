<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../controllers/UserController.php';

try {

    $database = new Database();
    $conn = $database->connect();

    $controller = new UserController($conn);

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {

        if (isset($_GET['id'])) {

            $user = $controller->getById(
                (int)$_GET['id']
            );

            if (!$user) {
                jsonResponse([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            jsonResponse([
                'success' => true,
                'data' => $user
            ]);
        }

        jsonResponse([
            'success' => true,
            'data' => $controller->getAll()
        ]);
    }

    if ($method === 'POST') {

        $id = $_POST['id'] ?? null;

        if ($id) {

            $result = $controller->update(
                (int)$id,
                $_POST,
                $_FILES['image'] ?? null
            );

            jsonResponse([
                'success' => $result,
                'message' => 'User updated successfully.'
            ]);
        }

        $result = $controller->create(
            $_POST,
            $_FILES['image'] ?? null
        );

        jsonResponse([
            'success' => $result,
            'message' => 'User created successfully.'
        ], 201);
    }

    if ($method === 'DELETE') {

        if (!isset($_GET['id'])) {
            jsonResponse([
                'success' => false,
                'message' => 'User ID is required.'
            ], 400);
        }

        $result = $controller->delete(
            (int)$_GET['id']
        );

        jsonResponse([
            'success' => $result,
            'message' => 'User deleted successfully.'
        ]);
    }

    jsonResponse([
        'success' => false,
        'message' => 'Method not allowed.'
    ], 405);

} catch (Exception $e) {

    jsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}