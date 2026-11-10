<?php

declare(strict_types=1);

namespace App\Income\Application;

use App\Income\Domain\Income;
use App\Income\Domain\IncomeRepositoryInterface;

final readonly class GetIncome
{
    public function __construct(private IncomeRepositoryInterface $income) {}

    public function execute(int $id): ?Income
    {
        return $this->income->findById($id);
    }
}
