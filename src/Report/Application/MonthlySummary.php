<?php

declare(strict_types=1);

namespace App\Report\Application;

final readonly class MonthlySummary
{
    public function __construct(
        public int $income,
        public int $expenses,
    ) {}

    public function balance(): int
    {
        return $this->income - $this->expenses;
    }
}
