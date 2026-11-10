<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Expense\Domain\Expense;
use App\Expense\Domain\ExpenseCategory;
use App\Expense\Infrastructure\Persistence\PdoExpenseRepository;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$file = tempnam(sys_get_temp_dir(), 'expenses-test-');
try {
    $connect = static fn (): PDO => new PDO('sqlite:' . $file, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo = $connect();
    $pdo->exec(file_get_contents(__DIR__ . '/../migrations/001_create_expenses.sql'));
    $repository = new PdoExpenseRepository($pdo);
    $builder = new ContainerBuilder();
    $builder->addDefinitions(__DIR__ . '/../config/dependencies.php');
    $container = $builder->build();
    $container->set(PDO::class, $pdo);
    $app = AppFactory::create(container: $container);
    (require __DIR__ . '/../config/routes.php')($app);
    $send = static function (string $method, string $path, string $body = '', string $type = 'application/json') use ($app) {
        $request = (new ServerRequestFactory())->createServerRequest($method, $path)->withHeader('Content-Type', $type);
        $request->getBody()->write($body);
        $request->getBody()->rewind();
        return $app->handle($request);
    };
    $body = '{"amount":2500,"category":"food","date":"2026-11-08","description":"Обед"}';
    $created = $send('POST', '/expenses', $body);
    check($created->getStatusCode() === 201, 'Create must return 201');
    $expense = json_decode((string) $created->getBody(), true, flags: JSON_THROW_ON_ERROR)['data'];
    check($expense['id'] > 0 && $expense['amount'] === 2500, 'Created expense must have ID and amount');
    check($created->getHeaderLine('Location') === '/expenses/' . $expense['id'], 'Location must identify created expense');
    $get = $send('GET', '/expenses/' . $expense['id']);
    check($get->getStatusCode() === 200, 'Get must return 200');
    check(json_decode((string) $get->getBody(), true)['data'] === $expense, 'Get must return saved values');
    $secondRepository = new PdoExpenseRepository($connect());
    check($secondRepository->findById($expense['id'])?->category === ExpenseCategory::Food, 'Data must survive a new database connection');
    check(count(json_decode((string) $send('GET', '/expenses')->getBody(), true)['items']) === 1, 'List must read database');
    check($send('GET', '/expenses/99999')->getStatusCode() === 404, 'Missing expense must return 404');
    check($send('POST', '/expenses', '{')->getStatusCode() === 400, 'Malformed JSON must return 400');
    check($send('POST', '/expenses', '[]')->getStatusCode() === 400, 'Array body must return 400');
    check($send('POST', '/expenses', $body, 'text/plain')->getStatusCode() === 415, 'Unsupported media type must return 415');
    foreach (['{"amount":-1,"category":"food","date":"2026-11-08"}', '{"amount":"2500","category":"food","date":"2026-11-08"}', '{"amount":2500,"category":" ","date":"2026-11-08"}', '{"amount":2500,"category":"food","date":"2026-02-30"}', '{"amount":2500,"category":"food","date":"2026-11-08","description":false}', '{}'] as $invalid) {
        check($send('POST', '/expenses', $invalid)->getStatusCode() === 422, 'Invalid fields must return 422');
    }
    check(count($repository->findAll()) === 1, 'Invalid requests must not create records');
    try {
        new Expense(null, 0, ExpenseCategory::Food, '2026-11-08');
        throw new RuntimeException('Domain must reject zero amount');
    } catch (InvalidArgumentException) {
        // Expected: domain rules hold outside HTTP too.
    }
    $unknown = $send('POST', '/expenses', '{"amount":2500,"category":"unknown","date":"2026-11-08"}');
    check($unknown->getStatusCode() === 422, 'Unknown category must return 422');
    check(json_decode((string) $unknown->getBody(), true)['allowedCategories'] === ['credit', 'food', 'education', 'transport'], 'Error must list allowed categories');
    check(count($repository->findAll()) === 1, 'Unknown category must not create a record');
    foreach (ExpenseCategory::cases() as $category) {
        $result = $send('POST', '/expenses', json_encode(['amount' => 100, 'category' => $category->value, 'date' => '2026-11-08']));
        check($result->getStatusCode() === 201, 'Each allowed category must be accepted');
        $item = json_decode((string) $result->getBody(), true)['data'];
        check($item['category'] === $category->value, 'API must return category string');
        check($secondRepository->findById($item['id'])?->category === $category, 'Repository must restore category enum');
    }
    echo "Integration checks passed\n";
} finally {
    unlink($file);
}
