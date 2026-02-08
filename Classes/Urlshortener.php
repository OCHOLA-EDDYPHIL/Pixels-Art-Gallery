<?php

/**
 * Class Urlshortener extends the Databasehandler class to provide URL shortening functionality.
 */
class Urlshortener extends Databasehandler
{
    /**
     * @var PDO $pdo Database connection object.
     */
    protected PDO $pdo;

    /**
     * Constructor for the Urlshortener class.
     *
     * @param PDO $pdo The PDO database connection object from the Databasehandler class.
     */
    public function __construct(PDO $pdo)
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
    public function shortenURL(string $longUrl): string
    {
        $sanitizedUrl = filter_var($longUrl, FILTER_SANITIZE_URL);
        if (!filter_var($sanitizedUrl, FILTER_VALIDATE_URL)) {
            return 'Invalid URL';
        }

        $scheme = parse_url($sanitizedUrl, PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return 'Only http/https URLs allowed';
        }

        $shortCode = $this->generateShortCode();
        $this->storeURL($sanitizedUrl, $shortCode);
        return $shortCode;
    }

    /**
     * Generates a short code.
     *
     * Creates a unique short code by hashing a generated unique ID and returning the first 6 characters.
     *
     * @return string The generated short code.
     */
    protected function generateShortCode(): string
    {
        $attempts = 0;
        do {
            $attempts++;
            $code = substr(bin2hex(random_bytes(4)), 0, 6);
            $stmt = $this->pdo->prepare("SELECT 1 FROM urls WHERE short_code = ?");
            $stmt->execute([$code]);
        } while ($stmt->fetchColumn() && $attempts < 5);

        if ($attempts >= 5) {
            throw new RuntimeException('Failed to generate unique short code');
        }

        return $code;
    }

    /**
     * Stores the original URL and its corresponding short code in the database.
     *
     * @param string $longUrl The original URL.
     * @param string $shortCode The generated short code for the URL.
     */
    protected function storeURL(string $longUrl, string $shortCode): void
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
    public function getLongURL(string $shortCode): ?string
    {
        $stmt = $this->pdo->prepare("SELECT long_url FROM urls WHERE short_code = ?");
        $stmt->execute([$shortCode]);
        $result = $stmt->fetch();
        return $result ? $result['long_url'] : null;
    }
}
