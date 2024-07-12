<?php

// Centralized configuration settings for database connection
define('DB_HOST', 'localhost'); // Database host
define('DB_NAME', 'project'); // Database name
define('DB_USER', 'root'); // Database user
define('DB_PASS', ''); // Database password

/**
 * Function to dynamically generate the base URL of the project.
 *
 * This function considers both HTTP and HTTPS protocols and dynamically
 * constructs the base URL using the server's host and the script's directory path.
 * Useful for generating absolute URLs for assets, links, and redirections within the project.
 *
 * @return string The base URL of the project.
 */
function getBaseUrl() {
    // Determine the protocol (HTTP or HTTPS)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    // Get the domain name
    $domainName = $_SERVER['HTTP_HOST'] . '/';
    // Construct and return the full base URL
    return $protocol . $domainName . trim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
}