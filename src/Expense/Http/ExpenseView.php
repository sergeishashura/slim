<?php

declare(strict_types=1);

namespace App\Expense\Http;

use App\Expense\Domain\Expense;

final class ExpenseView
{
    public static function toArray(Expense $expense): array
    {
        return [
            'id' => $expense->id,
            'amount' => $expense->amount,
            'category' => $expense->category->value,
            'date' => $expense->spentAt,
            'description' => $expense->description,
        ];
    }
}
