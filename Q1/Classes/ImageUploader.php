<?php

class ImageUploader extends Databasehandler
{
    private $targetDir;
    private $maxFileSize = 10000000; // 10MB
    private $allowedFileTypes = ['jpg', 'jpeg', 'png'];

    public function __construct()
    {
        parent::__construct();
        $this->targetDir = __DIR__ . "/../uploads/"; // Adjust the path as necessary
        if (!file_exists($this->targetDir)) {
            mkdir($this->targetDir, 0777, true);
        }
    }

    public function handleUpload($file, $caption, $email)
    {
        // check if user exists
        $userExists = $this->checkUserExists($email);
        if (!$userExists) {
            return "User not found.";
        }
        $uploadResult = $this->uploadImage($file);
        if (is_array($uploadResult) && $uploadResult['success']) {
            $user_id = $this->getUserIdByEmail($email);
            return $this->storeCaptionInDB($uploadResult['fileName'], $caption, $email);
        } else {
            return $uploadResult;
        }
    }

    // method to check if user exists
    private function checkUserExists($email)
    {
        $sql = "SELECT * FROM users WHERE email_address = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    public function uploadImage($file)
    {
        $validationResult = $this->validateImage($file);
        if ($validationResult !== true) {
            return $validationResult;
        }

        $uniqueFileName = $this->generateUniqueFileName($file["name"]);
        $targetFilePath = $this->targetDir . $uniqueFileName;
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            return ['success' => true, 'fileName' => $uniqueFileName];
        } else {
            return "Sorry, there was an error uploading your file.";
        }
    }

    public function validateImage($file)
    {
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            return "File is not an image.";
        }

        if ($file["size"] > $this->maxFileSize) {
            return "Sorry, your file is too large.";
        }

        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        if (!in_array($imageFileType, $this->allowedFileTypes)) {
            return "Sorry, only JPG, JPEG, PNG files are allowed.";
        }

        return true;
    }

    private function generateUniqueFileName($fileName)
    {
        $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        return uniqid() . '.' . $imageFileType;
    }

    public function storeCaptionInDB($fileName, $caption, $email)
    {
        $sql = "INSERT INTO photos (filename, caption, user_id) VALUES (?, ?, ?)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(1, $fileName, PDO::PARAM_STR);
        $stmt->bindParam(2, $caption, PDO::PARAM_STR);
        $stmt->bindParam(3, $email, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return "New record created successfully";
            header('Location: ../main.php');
        } else {
            return "Error: " . $stmt->errorInfo()[2];
        }
    }

}