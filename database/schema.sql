-- Pixels / IS project1 — database schema
-- Target database is provided by the caller (CNS 3205 Lab 2 uses `Lab2`).
-- Run with:  sudo mariadb Lab2 < database/schema.sql
--
-- Engine/charset chosen for utf8mb4 (full Unicode) and InnoDB (transactions,
-- foreign keys, row-level locking).

-- Registered users. Login key is the email; `username` is the human-facing
-- display name added for CNS 3205 Lab 2 (studentCNSA, studentMiguel, ...).
CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NULL,
    email_address VARCHAR(255) NOT NULL UNIQUE,
    pwd           VARCHAR(255) NOT NULL,
    reg_date      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Uploaded gallery images (so the post-login dashboard renders cleanly).
CREATE TABLE IF NOT EXISTS photos (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    filename  VARCHAR(255) NOT NULL,
    caption   VARCHAR(255) NULL,
    user_id   VARCHAR(255) NOT NULL,
    reg_date  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_reg_date (user_id, reg_date),
    INDEX idx_filename (filename)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- URL shortener table (feature shipped with the app).
CREATE TABLE IF NOT EXISTS urls (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    long_url   TEXT         NOT NULL,
    short_code VARCHAR(16)  NOT NULL UNIQUE,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CNS 3205 Lab 2, Part B #7: a second simple form backed by its own table.
-- The feedback form POSTs here and the page reads the rows back out.
CREATE TABLE IF NOT EXISTS feedback (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(255) NULL,
    message    TEXT         NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
