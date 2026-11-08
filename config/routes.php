<?php

declare(strict_types=1);

use App\Expense\Domain\ExpenseRepositoryInterface;
use App\Expense\Http\CreateExpenseAction;
use App\Expense\Http\ExpenseView;
use App\Expense\Http\GetExpenseAction;
use App\Shared\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return static function (App $app, CreateExpenseAction $create, GetExpenseAction $get, ExpenseRepositoryInterface $expenses): void {
    $app->get('/health', static fn (Request $request, Response $response): Response =>
        JsonResponse::write($response, ['status' => 'ok']));
    $app->post('/expenses', $create);
    $app->get('/expenses/{id:[0-9]+}', $get);
    $app->get('/expenses', static fn (Request $request, Response $response): Response =>
        JsonResponse::write($response, ['items' => array_map(ExpenseView::toArray(...), $expenses->findAll())]));
};
