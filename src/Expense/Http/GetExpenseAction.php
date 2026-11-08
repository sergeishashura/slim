<?php

declare(strict_types=1);

namespace App\Expense\Http;

use App\Shared\Http\JsonResponse;

use App\Expense\Application\GetExpense;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class GetExpenseAction
{
    public function __construct(private GetExpense $getExpense) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = filter_var($args['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $expense = $id === false ? null : $this->getExpense->execute($id);
        if ($expense === null) {
            return JsonResponse::write($response, ['error' => 'Расход не найден'], 404);
        }

        return JsonResponse::write($response, ['data' => ExpenseView::toArray($expense)]);
    }
}
