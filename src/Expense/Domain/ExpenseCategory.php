<?php

declare(strict_types=1);

namespace App\Expense\Domain;

enum ExpenseCategory: string
{
    case Credit = 'credit';
    case Food = 'food';
    case Education = 'education';
    case Transport = 'transport';
}
