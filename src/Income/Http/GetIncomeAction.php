<?php

declare(strict_types=1);

namespace App\Income\Http;

use App\Income\Application\GetIncome;
use App\Shared\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class GetIncomeAction
{
    public function __construct(private GetIncome $getIncome) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = filter_var($args['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $income = $id === false ? null : $this->getIncome->execute($id);
        if ($income === null) {
            return JsonResponse::write($response, ['error' => 'Доход не найден'], 404);
        }

        return JsonResponse::write($response, ['data' => IncomeView::toArray($income)]);
    }
}
