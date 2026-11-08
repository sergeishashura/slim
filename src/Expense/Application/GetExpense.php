<?php

declare(strict_types=1);

namespace App\Expense\Application;

use App\Expense\Domain\Expense;
use App\Expense\Domain\ExpenseRepositoryInterface;

final readonly class GetExpense
{
    public function __construct(private ExpenseRepositoryInterface $expenses) {}

    public function execute(int $id): ?Expense
    {
        return $this->expenses->findById($id);
    }
}
