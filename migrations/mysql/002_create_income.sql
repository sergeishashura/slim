CREATE TABLE IF NOT EXISTS income (
    id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    amount BIGINT NOT NULL CHECK (amount > 0),
    category VARCHAR(64) NOT NULL CHECK (CHAR_LENGTH(TRIM(category)) > 0),
    received_at CHAR(10) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    description LONGTEXT NULL,
    INDEX (received_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
