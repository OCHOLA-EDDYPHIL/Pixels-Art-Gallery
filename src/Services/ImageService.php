<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\Config;
use PDO;
use RuntimeException;

final class ImageService
{
    public function __construct(
        private readonly PDO $db,
        private readonly Config $config,
        private readonly string $uploadPath
    ) {
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0775, true);
        }
    }

    /**
     * @return array{success: bool, message: string, filename?: string}
     */
    public function upload(array $file, string $caption, string $email): array
    {
        $validation = $this->validateUpload($file);
        if ($validation['success'] === false) {
            return $validation;
        }

        $filename = $validation['filename'];
        $target = rtrim($this->uploadPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['success' => false, 'message' => 'Failed to save file'];
        }

        $stmt = $this->db->prepare('INSERT INTO photos (filename, caption, user_id) VALUES (?, ?, ?)');
        $stmt->execute([$filename, $caption, $email]);

        return ['success' => true, 'message' => 'Image uploaded', 'filename' => $filename];
    }

    public function delete(string $filename, string $email): string
    {
        $stmt = $this->db->prepare('SELECT filename FROM photos WHERE filename = ? AND user_id = ?');
        $stmt->execute([$filename, $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return 'You do not have permission to delete this image or it does not exist.';
        }

        $deleteStmt = $this->db->prepare('DELETE FROM photos WHERE filename = ? AND user_id = ?');
        $deleteStmt->execute([$filename, $email]);

        $path = rtrim($this->uploadPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
        if (is_file($path)) {
            @unlink($path);
        }

        return 'Image deleted successfully.';
    }

    /**
     * @return array{success: bool, message: string, filename?: string}
     */
    private function validateUpload(array $file): array
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error occurred'];
        }

        if ($file['size'] > $this->config->maxFileSize()) {
            return ['success' => false, 'message' => 'File too large'];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        ];

        if (!isset($allowed[$mimeType])) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG and PNG allowed.'];
        }

        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['success' => false, 'message' => 'File is not a valid image'];
        }

        $extension = $allowed[$mimeType];
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;

        return ['success' => true, 'message' => 'valid', 'filename' => $filename];
    }
}
