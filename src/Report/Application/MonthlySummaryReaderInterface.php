<?php

declare(strict_types=1);

namespace App\Report\Application;

interface MonthlySummaryReaderInterface
{
    /** Both dates are inclusive and use YYYY-MM-DD. */
    public function read(string $from, string $through): MonthlySummary;
}
