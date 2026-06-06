<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\Config;
use PDO;
use Psr\Log\LoggerInterface;

final class ImageService
{
    public function __construct(
        private readonly PDO $db,
        private readonly Config $config,
        private readonly string $uploadPath,
        private readonly ?LoggerInterface $logger = null
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

    /**
     * @return array{
     *     success: bool,
     *     status: string,
     *     message: string,
     *     filename: string,
     *     database_deleted: bool,
     *     file_existed?: bool,
     *     file_deleted?: bool
     * }
     */
    public function delete(string $filename, string $email): array
    {
        $stmt = $this->db->prepare('SELECT filename FROM photos WHERE filename = ? AND user_id = ?');
        $stmt->execute([$filename, $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return [
                'success' => false,
                'status' => 'not_found_or_unauthorized',
                'message' => 'You do not have permission to delete this image or it does not exist.',
                'filename' => $filename,
                'database_deleted' => false,
            ];
        }

        $deleteStmt = $this->db->prepare('DELETE FROM photos WHERE filename = ? AND user_id = ?');
        $deleteStmt->execute([$filename, $email]);

        $path = rtrim($this->uploadPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
        $fileExisted = is_file($path);
        if ($fileExisted && !unlink($path)) {
            $this->logger?->error('Image database record deleted, but file deletion failed.', [
                'filename' => $filename,
                'path' => $path,
                'user_id' => $email,
            ]);

            return [
                'success' => false,
                'status' => 'file_delete_failed',
                'message' => 'Image database record deleted, but the file could not be removed.',
                'filename' => $filename,
                'database_deleted' => true,
                'file_existed' => true,
                'file_deleted' => false,
            ];
        }

        return [
            'success' => true,
            'status' => 'complete_success',
            'message' => 'Image deleted successfully.',
            'filename' => $filename,
            'database_deleted' => true,
            'file_existed' => $fileExisted,
            'file_deleted' => $fileExisted,
        ];
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
