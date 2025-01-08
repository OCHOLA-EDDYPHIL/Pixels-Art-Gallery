# Pixels - Online Art Gallery

Welcome to **Pixels**, an art gallery web application where users can upload, view, and manage their photos. This project is designed with a robust **Object-Oriented Programming (OOP)** architecture, leveraging PHP for efficient backend operations, and uses MySQL for database integration.

---

## Features

- **User Authentication**: Secure login and logout functionality with validation.
- **User Registration**: Allows new users to create accounts.
- **Image Validation**: Limits uploads to specific file types (JPG, JPEG, PNG) with size restrictions (max 10MB).
- **Image Upload**: Users can upload photos along with captions.
- **Photo Management**:
   - View uploaded photos in a gallery format.
   - Delete photos directly from the gallery.
- **Responsive Design**: The application is mobile-friendly for an enhanced user experience.

---

## Requirements

You'll need the following to run the project:

- **PHP 8.4** or higher
- **MySQL** database
- A web server:
   - **Windows**: [XAMPP](https://www.apachefriends.org/index.html)
   - **Linux**: [LAMP](https://wiki.debian.org/LAMP) or an equivalent server stack
- Composer (for dependency management, if required)
- PhpStorm IDE (optional, for development)

---

## Installation and Setup

1. **Clone the Repository**:
   Clone the repository and navigate into the project directory:
   ```bash
   git clone https://github.com/OCHOLA-EDDYPHIL/Pixels-Art-Gallery.git
   ```
   ```bash
   cd Pixels-Art-Gallery
   ```

2. **Ensure You Have a Local Web Server**:
   - On **Windows**, download and set up [XAMPP](https://www.apachefriends.org/index.html).
   - On **Linux**, set up a LAMP stack or an alternative.
   - Ensure both the PHP interpreter and the MySQL database are running.

3. **Database Setup**:
   The `Databasehandler.php` class automatically creates the required database (if it doesn't exist) along with the necessary tables. No manual database setup is required.

4. **Configure Database Connection**:
   Update your database credentials in `Classes/Databasehandler.php`:
   ```php
   private string $host = "localhost";  // Database host
   private string $dbname = "project"; // Database name
   private string $username = "root";  // MySQL username
   private string $password = "";      // MySQL password
   ```

5. **Start the Server**:
   - Using PHP's built-in development server:
     ```bash
     php -S localhost:8000
     ```
   - Or place the project directory in your server's document root:
      - For **XAMPP**, copy it into the `htdocs` folder.
      - For **LAMP**, move it to `/var/www/html/`.

   Open your browser and visit:
   ```
   http://localhost:8000
   ```

6. **Enjoy the Application**:
   Log in, upload photos, and explore the photo gallery.

---

## Project Structure

The project adopts an **Object-Oriented Programming (OOP)** structure for scalability and maintainability. Below is the folder and file organization:

```plaintext
pixels/
├── Classes/
│   ├── Databasehandler.php   # Handles database connection and setup.
│   ├── ImageHandler.php      # Manages image uploads, validation, and deletion.
│   ├── Login.php             # Handles user authentication.
│   ├── Signup.php            # Manages user registration.
├── assets/
│   ├── css/                  # Stylesheets for the frontend.
│   ├── js/                   # JavaScript for interactivity.
├── uploads/                  # Directory for uploaded images.
├── includes/
│   ├── logout.inc.php        # Handles user logout requests.
│   ├── delete_image.inc.php  # Handles photo deletion requests.
├── index.php                 # Home page for the application.
├── upload.php                # Interface for uploading photos.
└── README.md                 # Project documentation.
```

---

## Current Features

This project already includes:

- **Database & Table Creation**: Automatically handled by the `Databasehandler` class.
- **User Registration**: Implements features for creating new accounts, including email and password validation in the `Signup` class.
- **Image Validation**: Ensures uploaded files are valid images (JPG, JPEG, PNG) and below 10MB in size. This is managed by the `ImageHandler` class.
- **Password Security**: User passwords are hashed using `password_hash()` for security.
- **File Storage**: Uploaded files are securely moved to the `/uploads` directory.

[//]: # (Todo: Add screenshots of the application.)
---
[//]: # (## Screenshots)

[//]: # ()
[//]: # (### Homepage)

[//]: # (![Homepage Screenshot]&#40;assets/images/homepage-screenshot.png&#41;)

## Future Features

While most core functionality is already implemented, these enhancements are planned for future versions:

- More detailed **user profiles** for account customization.
- **Pagination** for handling large galleries efficiently.
- **Enhanced error handling** and user feedback for upload forms.
- **Social sharing** for photos and galleries.

---

## Troubleshooting & Common Issues

1. **Connection Errors**: Ensure the MySQL server is running, and the database credentials in `Databasehandler.php` are correct.
2. **Permissions**: Ensure the `uploads/` directory has proper write permissions:
   ```bash
   chmod -R 775 uploads/
   ```
3. **XAMPP/LAMP Issues**: Verify that Apache and MySQL modules are active.

---


## Author

- **Name**: Ochola

Feel free to reach out with questions, suggestions, or feedback!

---

## License

This project is licensed under the **MIT License**.