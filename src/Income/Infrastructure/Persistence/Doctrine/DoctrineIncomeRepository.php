<?php

declare(strict_types=1);

namespace App\Income\Infrastructure\Persistence\Doctrine;

use App\Income\Domain\Income;
use App\Income\Domain\IncomeRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

final readonly class DoctrineIncomeRepository implements IncomeRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function save(Income $income): Income
    {
        if ($income->id !== null) {
            throw new InvalidArgumentException('save принимает только новые доходы');
        }

        $record = new IncomeRecord($income->amount, $income->category, $income->receivedAt, $income->description);
        $this->entityManager->persist($record);
        $this->entityManager->flush();

        return $record->toDomain();
    }

    public function findById(int $id): ?Income
    {
        return $this->entityManager->find(IncomeRecord::class, $id)?->toDomain();
    }

    public function findAll(): array
    {
        $records = $this->entityManager->getRepository(IncomeRecord::class)->findBy([], ['id' => 'DESC']);

        return array_map(static fn (IncomeRecord $record): Income => $record->toDomain(), $records);
    }
}
