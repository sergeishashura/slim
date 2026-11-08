<?php

declare(strict_types=1);

$pdo = new PDO('sqlite:' . dirname(__DIR__) . '/var/database.sqlite', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
$pdo->exec('PRAGMA busy_timeout = 5000');
$pdo->exec('CREATE TABLE IF NOT EXISTS migrations (name TEXT PRIMARY KEY)');
$files = glob(dirname(__DIR__) . '/migrations/*.sql');
sort($files);
foreach ($files as $file) {
    $name = basename($file);
    $pdo->beginTransaction();
    try {
        $statement = $pdo->prepare('SELECT name FROM migrations WHERE name = ?');
        $statement->execute([$name]);
        if ($statement->fetchColumn() !== false) {
            $pdo->commit();
            continue;
        }
        $pdo->exec(file_get_contents($file));
        $pdo->prepare('INSERT INTO migrations (name) VALUES (?)')->execute([$name]);
        $pdo->commit();
        echo "Applied: $name\n";
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}
