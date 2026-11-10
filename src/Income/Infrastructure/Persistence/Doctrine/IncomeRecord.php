<?php

declare(strict_types=1);

namespace App\Income\Infrastructure\Persistence\Doctrine;

use App\Income\Domain\Income;
use App\Income\Domain\IncomeCategory;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'income')]
final class IncomeRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column(type: Types::BIGINT)]
        private int $amount,
        #[ORM\Column(length: 64, enumType: IncomeCategory::class)]
        private IncomeCategory $category,
        #[ORM\Column(name: 'received_at', type: Types::ASCII_STRING, length: 10, options: ['fixed' => true])]
        private string $receivedAt,
        #[ORM\Column(type: Types::TEXT, nullable: true)]
        private ?string $description,
    ) {}

    public function toDomain(): Income
    {
        return new Income($this->id, $this->amount, $this->category, $this->receivedAt, $this->description);
    }
}
