<?php

declare(strict_types=1);

namespace App\Expense\Http;

use App\Expense\Domain\ExpenseRepositoryInterface;
use App\Shared\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class ListExpensesAction
{
    public function __construct(private ExpenseRepositoryInterface $expenses) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $items = array_map(ExpenseView::toArray(...), $this->expenses->findAll());

        return JsonResponse::write($response, ['items' => $items]);
    }
}
