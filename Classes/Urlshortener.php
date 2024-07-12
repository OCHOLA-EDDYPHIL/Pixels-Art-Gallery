<?php

class Urlshortener extends Databasehandler
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function shortenURL($longUrl)
    {
        $shortCode = $this->generateShortCode();
        $this->storeURL($longUrl, $shortCode);
        return $shortCode;
    }

    protected function generateShortCode()
    {
        return substr(md5(uniqid(rand(), true)), 0, 6);
    }

    protected function storeURL($longUrl, $shortCode)
    {
        $stmt = $this->pdo->prepare("INSERT INTO urls (long_url, short_code) VALUES (?, ?)");
        $stmt->execute([$longUrl, $shortCode]);
    }

    public function getLongURL($shortCode)
    {
        $stmt = $this->pdo->prepare("SELECT long_url FROM urls WHERE short_code = ?");
        $stmt->execute([$shortCode]);
        $result = $stmt->fetch();
        return $result ? $result['long_url'] : null;
    }
}