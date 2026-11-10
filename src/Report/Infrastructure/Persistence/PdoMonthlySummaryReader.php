<?php

declare(strict_types=1);

namespace App\Report\Infrastructure\Persistence;

use App\Report\Application\MonthlySummary;
use App\Report\Application\MonthlySummaryReaderInterface;
use PDO;

final readonly class PdoMonthlySummaryReader implements MonthlySummaryReaderInterface
{
    public function __construct(private PDO $pdo) {}

    public function read(string $from, string $through): MonthlySummary
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
            SELECT
                (SELECT COALESCE(SUM(amount), 0)
                 FROM income
                 WHERE received_at BETWEEN :income_from AND :income_through) AS income,
                (SELECT COALESCE(SUM(amount), 0)
                 FROM expenses
                 WHERE spent_at BETWEEN :expense_from AND :expense_through) AS expenses
            SQL
        );
        $statement->execute([
            'income_from' => $from,
            'income_through' => $through,
            'expense_from' => $from,
            'expense_through' => $through,
        ]);
        $totals = $statement->fetch(PDO::FETCH_ASSOC);

        return new MonthlySummary(
            income: (int) $totals['income'],
            expenses: (int) $totals['expenses'],
        );
    }
}
