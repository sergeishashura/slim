<?php

declare(strict_types=1);

namespace App\Income\Infrastructure\Persistence;

use App\Income\Domain\Income;
use App\Income\Domain\IncomeCategory;
use App\Income\Domain\IncomeRepositoryInterface;
use InvalidArgumentException;
use PDO;

final readonly class PdoIncomeRepository implements IncomeRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function save(Income $income): Income
    {
        if ($income->id !== null) {
            throw new InvalidArgumentException('save принимает только новые доходы');
        }
        $statement = $this->pdo->prepare(
            'INSERT INTO income (amount, category, received_at, description) VALUES (:amount, :category, :received_at, :description)'
        );
        $statement->execute([
            'amount' => $income->amount,
            'category' => $income->category->value,
            'received_at' => $income->receivedAt,
            'description' => $income->description,
        ]);

        return new Income((int) $this->pdo->lastInsertId(), $income->amount, $income->category, $income->receivedAt, $income->description);
    }

    public function findById(int $id): ?Income
    {
        $statement = $this->pdo->prepare('SELECT * FROM income WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function findAll(): array
    {
        return array_map($this->hydrate(...), $this->pdo->query('SELECT * FROM income ORDER BY id DESC')->fetchAll());
    }

    private function hydrate(array $row): Income
    {
        return new Income((int) $row['id'], (int) $row['amount'], IncomeCategory::from($row['category']), $row['received_at'], $row['description']);
    }
}
