<?php

declare(strict_types=1);

namespace App\Income\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class Income
{
    public function __construct(
        public ?int $id,
        public int $amount,
        public IncomeCategory $category,
        public string $receivedAt,
        public ?string $description = null,
    ) {
        if ($id !== null && $id <= 0) {
            throw new InvalidArgumentException('Некорректный идентификатор');
        }
        if ($amount <= 0) {
            throw new InvalidArgumentException('Сумма должна быть положительной');
        }
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $receivedAt)) {
            throw new InvalidArgumentException('Дата должна иметь формат YYYY-MM-DD');
        }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $receivedAt);
        if (!$date || $date->format('Y-m-d') !== $receivedAt) {
            throw new InvalidArgumentException('Дата должна существовать и иметь формат YYYY-MM-DD');
        }
    }
}
