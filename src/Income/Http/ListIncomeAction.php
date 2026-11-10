<?php

declare(strict_types=1);

namespace App\Income\Http;

use App\Income\Domain\IncomeRepositoryInterface;
use App\Shared\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class ListIncomeAction
{
    public function __construct(private IncomeRepositoryInterface $income) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $items = array_map(IncomeView::toArray(...), $this->income->findAll());

        return JsonResponse::write($response, ['items' => $items]);
    }
}
