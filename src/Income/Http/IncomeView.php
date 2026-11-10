<?php

declare(strict_types=1);

namespace App\Income\Http;

use App\Income\Domain\Income;

final class IncomeView
{
    public static function toArray(Income $income): array
    {
        return [
            'id' => $income->id,
            'amount' => $income->amount,
            'category' => $income->category->value,
            'date' => $income->receivedAt,
            'description' => $income->description,
        ];
    }
}
