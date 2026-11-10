<?php

declare(strict_types=1);

namespace App\Income\Application;

use App\Income\Domain\Income;
use App\Income\Domain\IncomeRepositoryInterface;

final readonly class CreateIncomeHandler
{
    public function __construct(private IncomeRepositoryInterface $income) {}

    public function handle(CreateIncomeCommand $command): Income
    {
        $income = new Income(
            id: null,
            amount: $command->amount,
            category: $command->category,
            receivedAt: $command->receivedAt,
            description: $command->description,
        );

        return $this->income->save($income);
    }
}
