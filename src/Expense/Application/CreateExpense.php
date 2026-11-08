<?php

declare(strict_types=1);

namespace App\Expense\Application;

use App\Expense\Domain\Expense;
use App\Expense\Domain\ExpenseCategory;
use App\Expense\Domain\ExpenseRepositoryInterface;

final readonly class CreateExpense
{
    public function __construct(private ExpenseRepositoryInterface $expenses) {}

    public function execute(int $amount, ExpenseCategory $category, string $spentAt, ?string $description): Expense
    {
        $expense = new Expense(null, $amount, $category, $spentAt, $description);

        return $this->expenses->save($expense);
    }
}
