<?php

declare(strict_types=1);

namespace App\Income\Http;

use App\Income\Application\CreateIncomeCommand;
use App\Income\Application\CreateIncomeHandler;
use App\Income\Domain\IncomeCategory;
use App\Shared\Http\JsonResponse;
use InvalidArgumentException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class CreateIncomeAction
{
    public function __construct(private CreateIncomeHandler $handler) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $contentType = strtolower(trim(explode(';', $request->getHeaderLine('Content-Type'))[0]));
        if ($contentType !== 'application/json') {
            return JsonResponse::write($response, ['error' => 'Ожидается Content-Type: application/json'], 415);
        }
        try {
            $data = json_decode((string) $request->getBody(), false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return JsonResponse::write($response, ['error' => 'Некорректный JSON'], 400);
        }
        if (!$data instanceof \stdClass) {
            return JsonResponse::write($response, ['error' => 'Ожидается JSON-объект'], 400);
        }
        if (!is_int($data->amount ?? null)
            || !is_string($data->category ?? null)
            || !is_string($data->date ?? null)
            || (isset($data->description) && !is_string($data->description))) {
            return JsonResponse::write($response, ['error' => 'Нужны amount (int), category (string), date (string); description — string или null'], 422);
        }
        $category = IncomeCategory::tryFrom($data->category);
        if ($category === null) {
            return JsonResponse::write($response, [
                'error' => 'Неизвестная категория дохода',
                'allowedCategories' => array_column(IncomeCategory::cases(), 'value'),
            ], 422);
        }
        $command = new CreateIncomeCommand(
            amount: $data->amount,
            category: $category,
            receivedAt: $data->date,
            description: $data->description ?? null,
        );
        try {
            $income = $this->handler->handle($command);
        } catch (InvalidArgumentException $exception) {
            return JsonResponse::write($response, ['error' => $exception->getMessage()], 422);
        }

        return JsonResponse::write($response, ['data' => IncomeView::toArray($income)], 201)
            ->withHeader('Location', '/income/' . $income->id);
    }
}
