<?php

declare(strict_types=1);

return static function (): PDO {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $driver = getenv('DB_DRIVER') ?: 'sqlite';

    if ($driver === 'sqlite') {
        $pdo = new PDO('sqlite:' . dirname(__DIR__) . '/var/database.sqlite', options: $options);
        $pdo->exec('PRAGMA busy_timeout = 5000');

        return $pdo;
    }

    if ($driver !== 'mysql') {
        throw new RuntimeException('Unsupported DB_DRIVER: ' . $driver);
    }

    $mysql = require __DIR__ . '/mysql.php';
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $mysql['host'], $mysql['port'], $mysql['dbname'], $mysql['charset']);

    return new PDO(
        $dsn,
        $mysql['user'],
        $mysql['password'],
        $options + [PDO::ATTR_EMULATE_PREPARES => false],
    );
};
