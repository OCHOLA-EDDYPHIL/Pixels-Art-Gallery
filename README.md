# Pixels - Art Show Application

Welcome to **Pixels**, an art gallery web application where users can upload, view, and manage their photos. This project is built with PHP for the backend and HTML/CSS/JavaScript for the frontend, with database integration for storing user information and photos.

## Features

- **User Authentication**: Users can log in and log out securely.
- **Image Upload**: Logged-in users can upload their photos along with captions.
- **Photo Gallery**: View all uploaded photos in a visually consistent format.
- **Delete Photos**: Users can delete their uploaded photos.
- **Responsive Design**: The interface is optimized for mobile and desktop screens.

---

## Requirements

To run this project, you'll need:

- **PHP 8.4** or higher
- Web server (e.g., built-in PHP server, Apache, or Nginx)
- MySQL or compatible database
- Composer (for dependency management, if required)
- PhpStorm (optional, for development)

---

## Installation and Setup

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/your-repo/pixels.git
   cd pixels
   ```

2. **Set Up the Database**:
    - Create a MySQL database (e.g., `pixels_db`).
    - Import the database schema (if provided), or create the necessary tables:
      ```sql
      CREATE TABLE photos (
          id INT AUTO_INCREMENT PRIMARY KEY,
          filename VARCHAR(255) NOT NULL,
          caption TEXT,
          user_id VARCHAR(255),
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
 
      CREATE TABLE users (
          id INT AUTO_INCREMENT PRIMARY KEY,
          email VARCHAR(255) NOT NULL UNIQUE,
          password_hash VARCHAR(255) NOT NULL
      );
      ```

3. **Configure Database Connection**:
    - Open the file `Classes/Databasehandler.php`.
    - Update the database credentials:
      ```php
      $host = 'localhost';
      $db   = 'pixels_db';
      $user = 'your_username';
      $pass = 'your_password';
      ```

4. **Start the PHP Server**:
    - Using the built-in PHP server:
      ```bash
      php -S localhost:8000
      ```
    - Open your browser and navigate to:
      ```
      http://localhost:8000
      ```

5. **Access the Application**:
    - Use the homepage to log in, upload, and manage your photos.


## Project Structure
```graphql
pixels/
├── Classes/
│   ├── Databasehandler.php \# Handles the database connection.
│   ├── YourOtherClasses.php \# Add any additional classes here.
├── assets/
│   ├── css/ \# Stylesheets for the project.
│   ├── js/ \# JavaScript files for interactivity.
│   ├── images/ \# Static images (if needed).
├── includes/
│   ├── logout.inc.php \# Handles user logout.
│   ├── delete\_image.inc.php \# Handles photo deletion.
├── uploads/ \# Directory for uploaded images.
├── index.php \# Main entry point for the application.
├── upload.php \# Handles the photo upload interface.
├── README.md \# Documentation for the project.
```
## Usage

1. **Homepage**:
    - Displays a list of uploaded photos.
    - Allows users to log in or log out.

2. **Logging In**:
    - Users can log in with their email and password.

3. **Photo Upload**:
    - After logging in, users can upload photos with captions.

4. **Photo Gallery**:
    - Each photo shows the uploader and its caption underneath.
    - Users can delete their own photos from the gallery.

[//]: # (Todo: Add screenshots of the application.)
---
[//]: # (## Screenshots)

[//]: # ()
[//]: # (### Homepage)

[//]: # (![Homepage Screenshot]&#40;assets/images/homepage-screenshot.png&#41;)

[//]: # ()
[//]: # (### Login Page)

[//]: # (![Login Page Screenshot]&#40;assets/images/login-screenshot.png&#41;)

[//]: # ()
[//]: # (### Photo Upload)

[//]: # (![Upload Screenshot]&#40;assets/images/upload-screenshot.png&#41;)

[//]: # ()
[//]: # (---)

## Technologies Used

- **Backend**:
    - PHP 8.x
    - MySQL Database

- **Frontend**:
    - HTML5
    - CSS3 (Responsive Design)
    - JavaScript

- **Others**:
    - PhpStorm for development
    - XAMPP/WAMP for local server testing (optional)

---

## Future Enhancements

- Add image validation to ensure proper file types and size limits.
- Implement user registration.
- Add pagination for the photo gallery.
- Enhance security with prepared statements (if not already done).
- Provide a profile page for users.
- Enable better error handling and feedback for uploads.

---

## Author

- **Name**: Ochola

Feel free to reach out for any questions or suggestions regarding this project!

---

## License

This project is licensed under the MIT License.