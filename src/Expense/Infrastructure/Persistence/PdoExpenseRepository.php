<?php

declare(strict_types=1);

namespace App\Expense\Infrastructure\Persistence;

use App\Expense\Domain\Expense;
use App\Expense\Domain\ExpenseCategory;
use App\Expense\Domain\ExpenseRepositoryInterface;
use InvalidArgumentException;
use PDO;

final readonly class PdoExpenseRepository implements ExpenseRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function save(Expense $expense): Expense
    {
        if ($expense->id !== null) {
            throw new InvalidArgumentException('save принимает только новые расходы');
        }
        $statement = $this->pdo->prepare(
            'INSERT INTO expenses (amount, category, spent_at, description) VALUES (:amount, :category, :spent_at, :description)'
        );
        $statement->execute([
            'amount' => $expense->amount,
            'category' => $expense->category->value,
            'spent_at' => $expense->spentAt,
            'description' => $expense->description,
        ]);

        return new Expense((int) $this->pdo->lastInsertId(), $expense->amount, $expense->category, $expense->spentAt, $expense->description);
    }

    public function findById(int $id): ?Expense
    {
        $statement = $this->pdo->prepare('SELECT * FROM expenses WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function findAll(): array
    {
        return array_map($this->hydrate(...), $this->pdo->query('SELECT * FROM expenses ORDER BY id DESC')->fetchAll());
    }

    private function hydrate(array $row): Expense
    {
        return new Expense((int) $row['id'], (int) $row['amount'], ExpenseCategory::from($row['category']), $row['spent_at'], $row['description']);
    }
}
