<?php

declare(strict_types=1);

namespace App\Report\Application;

final readonly class GetMonthlySummaryQuery
{
    public function __construct(public string $month) {}
}
