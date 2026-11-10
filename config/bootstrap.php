<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/dependencies.php');
$container = $builder->build();

$app = AppFactory::create(container: $container);
(require __DIR__ . '/routes.php')($app);
$app->addErrorMiddleware(true, true, true); // Local development only.

return $app;
