<?php

declare(strict_types=1);

namespace App\Income\Domain;

enum IncomeCategory: string
{
    case Salary = 'salary';
    case Freelance = 'freelance';
    case Gift = 'gift';
    case Investment = 'investment';
    case Other = 'other';
}
