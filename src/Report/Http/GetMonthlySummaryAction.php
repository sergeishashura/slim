<?php

declare(strict_types=1);

namespace App\Report\Http;

use App\Report\Application\GetMonthlySummaryHandler;
use App\Report\Application\GetMonthlySummaryQuery;
use App\Shared\Http\JsonResponse;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class GetMonthlySummaryAction
{
    public function __construct(private GetMonthlySummaryHandler $handler) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $month = $request->getQueryParams()['month'] ?? null;
        if (!is_string($month)) {
            return JsonResponse::write($response, ['error' => 'Укажи month в формате YYYY-MM.'], 400);
        }

        $query = new GetMonthlySummaryQuery($month);
        try {
            $summary = $this->handler->handle($query);
        } catch (InvalidArgumentException $exception) {
            return JsonResponse::write($response, ['error' => $exception->getMessage()], 400);
        }

        return JsonResponse::write($response, ['data' => [
            'month' => $month,
            'income' => $summary->income,
            'expenses' => $summary->expenses,
            'balance' => $summary->balance(),
        ]]);
    }
}
