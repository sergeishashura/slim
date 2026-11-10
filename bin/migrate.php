<?php

declare(strict_types=1);

$pdo = (require __DIR__ . '/../config/database.php')();
$mysql = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql';
$pdo->exec('CREATE TABLE IF NOT EXISTS migrations (name VARCHAR(255) PRIMARY KEY)');
$directory = dirname(__DIR__) . '/migrations' . ($mysql ? '/mysql' : '');
$files = glob($directory . '/*.sql');
$find = $pdo->prepare('SELECT name FROM migrations WHERE name = ?');
$record = $pdo->prepare($mysql
    ? 'INSERT INTO migrations (name) VALUES (?) ON DUPLICATE KEY UPDATE name = name'
    : 'INSERT INTO migrations (name) VALUES (?)');
sort($files);
foreach ($files as $file) {
    $name = basename($file);
    // MySQL commits DDL automatically, so its migration files must be safe to retry.
    if (!$mysql) {
        $pdo->beginTransaction();
    }
    try {
        $find->execute([$name]);
        $applied = $find->fetchColumn() !== false;
        $find->closeCursor();
        if (!$applied) {
            $pdo->exec(file_get_contents($file));
            $record->execute([$name]);
        }
        if (!$mysql) {
            $pdo->commit();
        }
        if (!$applied) {
            echo "Applied: $name\n";
        }
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}
