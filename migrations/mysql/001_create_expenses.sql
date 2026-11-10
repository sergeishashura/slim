CREATE TABLE IF NOT EXISTS expenses (
    id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    amount BIGINT NOT NULL CHECK (amount > 0),
    category VARCHAR(64) NOT NULL CHECK (CHAR_LENGTH(TRIM(category)) > 0),
    spent_at CHAR(10) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    description LONGTEXT NULL,
    INDEX (spent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
