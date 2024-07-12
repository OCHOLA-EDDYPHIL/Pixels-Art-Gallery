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
    private $targetDir;

    /**
     * @var int $maxFileSize Maximum file size allowed for uploads (in bytes).
     */
    private $maxFileSize = 10000000; // 10MB

    /**
     * @var array $allowedFileTypes Array of allowed file types for upload.
     */
    private $allowedFileTypes = ['jpg', 'jpeg', 'png'];

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
     * @return string|array Returns a success message with file name if successful, or an error message if not.
     */
    public function handleUpload($file, $caption, $email)
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
    private function checkUserExists($email)
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
     * @return array|string Returns an array with success status and file name if successful, or an error message if not.
     */
    public function uploadImage($file)
    {
        // Validate the image file
        $validationResult = $this->validateImage($file);
        if ($validationResult !== true) {
            return $validationResult;
        }

        // Generate a unique file name and upload the file
        $uniqueFileName = $this->generateUniqueFileName($file["name"]);
        $targetFilePath = $this->targetDir . $uniqueFileName;
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            return ['success' => true, 'fileName' => $uniqueFileName];
        } else {
            return "Sorry, there was an error uploading your file.";
        }
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
    public function validateImage($file)
    {
        // Check if the file is an image by attempting to get its size and type.
        // This is done using the getimagesize() function, which returns false if the file is not an image.
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            return "File is not an image."; // Return an error if the file is not an image.
        }

        // Check if the file size exceeds the maximum allowed size.
        // This is important to prevent users from uploading excessively large files that could strain server resources.
        if ($file["size"] > $this->maxFileSize) {
            return "Sorry, your file is too large."; // Return an error if the file is too large.
        }

        // Check if the file's type (extension) is among the allowed types.
        // This helps ensure that only supported image formats are uploaded, enhancing security and compatibility.
        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        if (!in_array($imageFileType, $this->allowedFileTypes)) {
            return "Sorry, only JPG, JPEG, PNG files are allowed."; // Return an error if the file type is not allowed.
        }

        return true; // Return true if all checks pass, indicating the file is a valid image.
    }

    /**
     * Generates a unique file name for the uploaded file.
     *
     * @param string $fileName The original file name.
     * @return string The unique file name.
     */
    private function generateUniqueFileName($fileName)
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
    public function storeCaptionInDB($fileName, $caption, $email)
    {
        $sql = "INSERT INTO photos (filename, caption, user_id) VALUES (?, ?, ?)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(1, $fileName, PDO::PARAM_STR);
        $stmt->bindParam(2, $caption, PDO::PARAM_STR);
        $stmt->bindParam(3, $email, PDO::PARAM_STR);
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
    public function deleteImage($filename, $email)
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