<?php

declare(strict_types=1);

namespace App\Income\Domain;

interface IncomeRepositoryInterface
{
    /** Saves a new income and returns a new object with its assigned ID. */
    public function save(Income $income): Income;

    public function findById(int $id): ?Income;

    /** @return list<Income> */
    public function findAll(): array;
}
