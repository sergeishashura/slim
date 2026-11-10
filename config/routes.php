<?php

declare(strict_types=1);

use App\Expense\Http\CreateExpenseAction;
use App\Expense\Http\GetExpenseAction;
use App\Expense\Http\ListExpensesAction;
use App\Income\Http\CreateIncomeAction;
use App\Income\Http\GetIncomeAction;
use App\Income\Http\ListIncomeAction;
use App\Report\Http\GetMonthlySummaryAction;
use App\Shared\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app): void {
    $app->get('/health', fn (Request $request, Response $response): Response =>
        JsonResponse::write($response, ['status' => 'ok']));
    $app->group('/expenses', function (RouteCollectorProxy $routes): void {
        $routes->get('', ListExpensesAction::class);
        $routes->post('', CreateExpenseAction::class);
        $routes->get('/{id:[0-9]+}', GetExpenseAction::class);
    });
    $app->group('/income', function (RouteCollectorProxy $routes): void {
        $routes->get('', ListIncomeAction::class);
        $routes->post('', CreateIncomeAction::class);
        $routes->get('/{id:[0-9]+}', GetIncomeAction::class);
    });
    $app->get('/reports/monthly', GetMonthlySummaryAction::class);
};
