<?php

/**
 * Handles image upload and management functionalities.
 * Extends the Databasehandler class to utilize database operations.
 */
class ImageHandler extends Databasehandler
{
    /**
     * @var string $targetDir Directory where uploaded images will be stored.
     */
    private string $targetDir;

    /**
     * @var int $maxFileSize Maximum file size allowed for uploads (in bytes).
     */
    private int $maxFileSize = 10000000; // 10MB

    /**
     * @var array $allowedFileTypes Array of allowed file types for upload.
     */
    private array $allowedFileTypes = ['jpg', 'jpeg', 'png'];

    /**
     * Constructor. Initializes the target directory for uploads and creates it if it doesn't exist.
     */
    public function __construct()
    {
        parent::__construct(); // Call the parent constructor to initialize database connection
        $this->targetDir = __DIR__ . "/../uploads/"; // Set the target directory for uploads
        if (!file_exists($this->targetDir)) {
            mkdir($this->targetDir, 0777, true); // Create the directory if it doesn't exist
        }
    }

    /**
     * Handles the process of uploading an image.
     *
     * @param array $file The uploaded file from the $_FILES array.
     * @param string $caption The caption for the uploaded image.
     * @param string $email The email of the user uploading the image.
     * @return bool|array|string Returns a success message with file name if successful, or an error message if not.
     */
    public function handleUpload(array $file, string $caption, string $email): bool|array|string
    {
        // Check if user exists in the database
        $userExists = $this->checkUserExists($email);
        if (!$userExists) {
            return "User not found.";
        }

        // Attempt to upload the image
        $uploadResult = $this->uploadImage($file);
        if (is_array($uploadResult) && $uploadResult['success']) {
            // Store the image caption in the database if upload is successful
            return $this->storeCaptionInDB($uploadResult['fileName'], $caption, $email);
        } else {
            // Return the error message if upload failed
            return $uploadResult;
        }
    }

    /**
     * Checks if a user exists in the database based on their email.
     *
     * @param string $email The email of the user to check.
     * @return bool Returns true if the user exists, false otherwise.
     */
    private function checkUserExists(string $email): bool
    {
        $sql = "SELECT * FROM users WHERE email_address = ?";
        $stmt = Databasehandler::getInstance()->connect()->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Validates and uploads the image file to the server.
     *
     * @param array $file The uploaded file from the $_FILES array.
     * @return bool|array|string Returns an array with success status and file name if successful, or an error message if not.
     */
    public function uploadImage(array $file): bool|array|string
    {
        $validationResult = $this->validateImage($file);
        if (is_string($validationResult)) {
            return $validationResult;
        }

        $uniqueFileName = $validationResult['filename'];
        $targetFilePath = $this->targetDir . $uniqueFileName;
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            return ['success' => true, 'fileName' => $uniqueFileName];
        }

        return "Sorry, there was an error uploading your file.";
    }

    /**
     * Validates the image file based on size and type.
     *
     * This method performs a series of checks on an uploaded file to ensure it meets
     * specific criteria for image uploads. It first verifies that the file is an actual image
     * by checking its MIME type. Then, it ensures the file size does not exceed a predefined maximum.
     * Finally, it checks if the file's extension is among the allowed types (JPG, JPEG, PNG).
     * These validations help maintain the integrity and consistency of the image data stored by the application.
     *
     * @param array $file The uploaded file from the $_FILES array. This array contains details
     *                    about the file, including its name, type, size, temporary storage path, and error status.
     * @return bool|string Returns true if the file passes all validation checks, indicating it is a valid image
     *                     file that can be safely uploaded. If the file fails any check, a string containing an
     *                     appropriate error message is returned. This message can be used to inform the user about
     *                     the specific reason their upload was rejected.
     */
    public function validateImage(array $file): bool|array|string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return "Upload error occurred.";
        }

        if ($file["size"] > $this->maxFileSize) {
            return "Sorry, your file is too large.";
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file["tmp_name"]);
        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        ];

        if (!isset($allowedMimes[$mimeType])) {
            return "Invalid file type. Only JPG and PNG allowed.";
        }

        $imageInfo = getimagesize($file["tmp_name"]);
        if ($imageInfo === false) {
            return "File is not an image.";
        }

        $extension = $allowedMimes[$mimeType];
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;

        return ['success' => true, 'filename' => $filename];
    }

    /**
     * Generates a unique file name for the uploaded file.
     *
     * @param string $fileName The original file name.
     * @return string The unique file name.
     */
    private function generateUniqueFileName(string $fileName): string
    {
        $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        return uniqid() . 'Classes.' . $imageFileType;
    }

    /**
     * Stores the image caption in the database.
     *
     * @param string $fileName The file name of the uploaded image.
     * @param string $caption The caption for the uploaded image.
     * @param string $email The email of the user uploading the image.
     * @return string Returns a success message or an error message.
     */
    public function storeCaptionInDB(string $fileName, string $caption, string $email): string
    {
        $sql = "INSERT INTO photos (filename, caption, user_id) VALUES (?, ?, ?)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(1, $fileName);
        $stmt->bindParam(2, $caption);
        $stmt->bindParam(3, $email);
        if ($stmt->execute()) {
            header('Location: ../main.php');  // Redirects after a successful upload
            exit();
        } else {
            return "Error: " . $stmt->errorInfo()[2];
        }
    }

    /**
     * Deletes an image from the server and the database.
     *
     * @param string $filename The file name of the image to delete.
     * @param string $email The email of the user attempting to delete the image.
     * @return string Returns a success message or an error message.
     */
    public function deleteImage(string $filename, string $email): string
    {
        // Verify that the user is the uploader
        $sql = "SELECT * FROM photos WHERE filename = ? AND user_id = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$filename, $email]);
        $image = $stmt->fetch();

        if (!$image) {
            return "You do not have permission to delete this image or it does not exist.";
        }

        // Delete the record from the database
        $sql = "DELETE FROM photos WHERE filename = ? AND user_id = ?";
        $stmt = $this->connect()->prepare($sql);
        $deleteSuccess = $stmt->execute([$filename, $email]);

        if ($deleteSuccess) {
            // Delete the file from the server
            $filePath = $this->targetDir . $filename;
            if (file_exists($filePath)) {
                unlink($filePath);
                return "Image deleted successfully.";
            } else {
                return "Image file not found on the server.";
            }
        } else {
            return "Error deleting image from database.";
        }
    }
}
