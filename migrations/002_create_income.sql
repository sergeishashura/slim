CREATE TABLE income (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    amount INTEGER NOT NULL CHECK (amount > 0),
    category TEXT NOT NULL CHECK (length(trim(category)) > 0),
    received_at TEXT NOT NULL,
    description TEXT
);
