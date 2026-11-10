<?php

declare(strict_types=1);

namespace App\Income\Application;

use App\Income\Domain\IncomeCategory;

final readonly class CreateIncomeCommand
{
    public function __construct(
        public int $amount,
        public IncomeCategory $category,
        public string $receivedAt,
        public ?string $description = null,
    ) {}
}
