<?php

function uploadImage($file, $folder)
{
    if (
        !isset($file) ||
        $file['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Image upload failed.");
    }

    $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif'
    ];

    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Invalid image type.");
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("Image must be smaller than 5MB.");
    }

    $extension = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    $filename = uniqid('', true) . '.' . $extension;

    $uploadDirectory =
        "/var/www/backend/uploads/" . $folder . "/";

    if (!is_dir($uploadDirectory)) {
        throw new Exception(
            "Upload directory does not exist: " .
            $uploadDirectory
        );
    }

    $destination =
        $uploadDirectory . $filename;

    if (!move_uploaded_file(
        $file['tmp_name'],
        $destination
    )) {
        throw new Exception(
            "Failed to save image."
        );
    }

    return $filename;
}