<?php

declare(strict_types=1);

use App\Expense\Domain\ExpenseRepositoryInterface;
use App\Expense\Infrastructure\Persistence\PdoExpenseRepository;
use App\Income\Domain\IncomeRepositoryInterface;
use App\Income\Infrastructure\Persistence\Doctrine\DoctrineIncomeRepository;
use App\Income\Infrastructure\Persistence\PdoIncomeRepository;
use App\Report\Application\MonthlySummaryReaderInterface;
use App\Report\Infrastructure\Persistence\PdoMonthlySummaryReader;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

use function DI\factory;
use function DI\get;

return [
    PDO::class => factory(require __DIR__ . '/database.php'),
    EntityManagerInterface::class => factory(static function (): EntityManagerInterface {
        $mysql = require __DIR__ . '/mysql.php';
        $connection = DriverManager::getConnection(['driver' => 'pdo_mysql'] + $mysql);
        $config = ORMSetup::createAttributeMetadataConfig(
            paths: [dirname(__DIR__) . '/src/Income/Infrastructure/Persistence/Doctrine'],
            isDevMode: true,
        );
        $config->enableNativeLazyObjects(true);

        return new EntityManager($connection, $config);
    }),
    ExpenseRepositoryInterface::class => get(PdoExpenseRepository::class),
    IncomeRepositoryInterface::class => get(getenv('DB_DRIVER') === 'mysql'
        ? DoctrineIncomeRepository::class
        : PdoIncomeRepository::class),
    MonthlySummaryReaderInterface::class => get(PdoMonthlySummaryReader::class),
];
