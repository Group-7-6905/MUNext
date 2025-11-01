<?php

/**
 * MUNext Database Configuration Template
 * 
 * INSTRUCTIONS:
 * 1. Copy this file to includes/config.php (or wherever your config file is located)
 * 2. Update the values below with your local database credentials
 * 3. DO NOT commit your actual credentials to version control
 */

// Database Configuration
define('DB_HOST', 'localhost');          // Database host (usually localhost)
define('DB_NAME', 'erisdb');             // Database name
define('DB_USER', 'root');               // Database username
define('DB_PASSWORD', '');               // Database password (update with your password)
define('DB_PORT', '3306');               // Database port (default MySQL port)

// Application Configuration
define('APP_URL', 'http://localhost/MUNext');  // Your application URL
define('APP_NAME', 'MUNext');                   // Application name

// File Upload Configuration
define('UPLOAD_PATH', 'uploads/');              // Path for job attachments
define('RESUME_PATH', 'resumes_cv/');           // Path for resume uploads
define('MAX_FILE_SIZE', 5242880);               // Max file size (5MB in bytes)
define('ALLOWED_EXTENSIONS', 'pdf,doc,docx');   // Allowed file extensions

// Session Configuration
define('SESSION_TIMEOUT', 3600);                // Session timeout in seconds (1 hour)

// Database Connection
try {
    $con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
    
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }
    
    // Set charset to utf8mb4 for emoji and special character support
    $con->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Alternatively, if using PDO:
/*
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}
*/

?>