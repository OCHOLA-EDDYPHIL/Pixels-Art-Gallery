<?php

/**
 * Class Urlshortener extends the Databasehandler class to provide URL shortening functionality.
 */
class Urlshortener extends Databasehandler
{
    /**
     * @var PDO $pdo Database connection object.
     */
    protected $pdo;

    /**
     * Constructor for the Urlshortener class.
     *
     * @param PDO $pdo The PDO database connection object from the Databasehandler class.
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Shortens a given URL.
     *
     * Generates a short code for a given URL, stores it in the database, and returns the short code.
     *
     * @param string $longUrl The original URL to be shortened.
     * @return string The generated short code for the URL.
     */
    public function shortenURL($longUrl)
    {
        $shortCode = $this->generateShortCode();
        $this->storeURL($longUrl, $shortCode);
        return $shortCode;
    }

    /**
     * Generates a short code.
     *
     * Creates a unique short code by hashing a generated unique ID and returning the first 6 characters.
     *
     * @return string The generated short code.
     */
    protected function generateShortCode()
    {
        return substr(md5(uniqid(rand(), true)), 0, 6);
    }

    /**
     * Stores the original URL and its corresponding short code in the database.
     *
     * @param string $longUrl The original URL.
     * @param string $shortCode The generated short code for the URL.
     */
    protected function storeURL($longUrl, $shortCode)
    {
        $stmt = $this->pdo->prepare("INSERT INTO urls (long_url, short_code) VALUES (?, ?)");
        $stmt->execute([$longUrl, $shortCode]);
    }

    /**
     * Retrieves the original URL based on a given short code.
     *
     * Looks up the short code in the database and returns the original URL if found.
     *
     * @param string $shortCode The short code to look up.
     * @return string|null The original URL if found, null otherwise.
     */
    public function getLongURL($shortCode)
    {
        $stmt = $this->pdo->prepare("SELECT long_url FROM urls WHERE short_code = ?");
        $stmt->execute([$shortCode]);
        $result = $stmt->fetch();
        return $result ? $result['long_url'] : null;
    }
}