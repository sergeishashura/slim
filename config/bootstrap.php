<?php

declare(strict_types=1);

use App\Expense\Application\CreateExpense;
use App\Expense\Application\GetExpense;
use App\Expense\Http\CreateExpenseAction;
use App\Expense\Http\GetExpenseAction;
use App\Expense\Infrastructure\Persistence\PdoExpenseRepository;
use Slim\Factory\AppFactory;

$pdo = new PDO('sqlite:' . dirname(__DIR__) . '/var/database.sqlite', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$pdo->exec('PRAGMA busy_timeout = 5000');
$repository = new PdoExpenseRepository($pdo);
$createExpenseAction = new CreateExpenseAction(new CreateExpense($repository));
$getExpenseAction = new GetExpenseAction(new GetExpense($repository));

$app = AppFactory::create();
(require __DIR__ . '/routes.php')($app, $createExpenseAction, $getExpenseAction, $repository);
$app->addErrorMiddleware(true, true, true); // Local development only.

return $app;
