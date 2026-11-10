<?php

declare(strict_types=1);

use App\Expense\Domain\ExpenseRepositoryInterface;
use App\Expense\Infrastructure\Persistence\PdoExpenseRepository;
use App\Income\Domain\IncomeRepositoryInterface;
use App\Income\Infrastructure\Persistence\PdoIncomeRepository;
use App\Report\Application\MonthlySummaryReaderInterface;
use App\Report\Infrastructure\Persistence\PdoMonthlySummaryReader;

use function DI\factory;
use function DI\get;

return [
    PDO::class => factory(static function (): PDO {
        $pdo = new PDO('sqlite:' . dirname(__DIR__) . '/var/database.sqlite', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA busy_timeout = 5000');

        return $pdo;
    }),
    ExpenseRepositoryInterface::class => get(PdoExpenseRepository::class),
    IncomeRepositoryInterface::class => get(PdoIncomeRepository::class),
    MonthlySummaryReaderInterface::class => get(PdoMonthlySummaryReader::class),
];
