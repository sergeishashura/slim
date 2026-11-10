<?php

declare(strict_types=1);

$database = getenv('MYSQL_DATABASE');
$user = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');
if (!$database || !$user || $password === false || $password === '') {
    throw new RuntimeException('Set MYSQL_DATABASE, MYSQL_USER and MYSQL_PASSWORD.');
}

return [
    'host' => 'db',
    'port' => 3306,
    'dbname' => $database,
    'user' => $user,
    'password' => $password,
    'charset' => 'utf8mb4',
];
