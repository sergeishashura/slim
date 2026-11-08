<?php

declare(strict_types=1);

namespace App\Expense\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class Expense
{
    public function __construct(
        public ?int $id,
        public int $amount,
        public ExpenseCategory $category,
        public string $spentAt,
        public ?string $description = null,
    ) {
        if ($id !== null && $id <= 0) {
            throw new InvalidArgumentException('Некорректный идентификатор');
        }
        if ($amount <= 0) {
            throw new InvalidArgumentException('Сумма должна быть положительной');
        }
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $spentAt)) {
            throw new InvalidArgumentException('Дата должна иметь формат YYYY-MM-DD');
        }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $spentAt);
        if (!$date || $date->format('Y-m-d') !== $spentAt) {
            throw new InvalidArgumentException('Дата должна существовать и иметь формат YYYY-MM-DD');
        }
    }
}
