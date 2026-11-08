<?php

declare(strict_types=1);

namespace App\Expense\Domain;

interface ExpenseRepositoryInterface
{
    /** Saves a new expense and returns a new object with its assigned ID. */
    public function save(Expense $expense): Expense;

    public function findById(int $id): ?Expense;

    /** @return list<Expense> */
    public function findAll(): array;
}
