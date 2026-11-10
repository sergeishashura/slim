<?php

declare(strict_types=1);

namespace App\Report\Application;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class GetMonthlySummaryHandler
{
    public function __construct(private MonthlySummaryReaderInterface $reader) {}

    public function handle(GetMonthlySummaryQuery $query): MonthlySummary
    {
        $month = $query->month;
        if (!preg_match('/^(?!0000)[0-9]{4}-(0[1-9]|1[0-2])$/D', $month)) {
            throw new InvalidArgumentException('Месяц должен иметь формат YYYY-MM.');
        }

        $start = new DateTimeImmutable($month . '-01');

        return $this->reader->read(
            $start->format('Y-m-d'),
            $start->modify('last day of this month')->format('Y-m-d'),
        );
    }
}
