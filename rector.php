<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Rector\CodingStyle\Rector\ClassMethod\ReturnArrayClassMethodToYieldRector;
use Rector\CodingStyle\ValueObject\ReturnArrayClassMethodToYield;
use Rector\Core\Configuration\Option;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\PHPUnit\Rector\Class_\AddSeeTestAnnotationRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\Set\ValueObject\LevelSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Zing\CodingStandard\Set\RectorSetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(RectorSetList::CUSTOM);
    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_CODE_QUALITY);
    $containerConfigurator->import(LevelSetList::UP_TO_PHP_73);

    $services = $containerConfigurator->services();
    $services->set(ReturnArrayClassMethodToYieldRector::class)
        ->configure([new ReturnArrayClassMethodToYield(TestCase::class, '*provide*')]);
    $parameters = $containerConfigurator->parameters();
    $parameters->set(
        Option::BOOTSTRAP_FILES,
        [
            __DIR__ . '/vendor/squizlabs/php_codesniffer/autoload.php',
            __DIR__ . '/vendor/symplify/easy-coding-standard/vendor/autoload.php',
        ]
    );
    $parameters->set(
        Option::SKIP,
        [
            RenameParamToMatchTypeRector::class,
            AddSeeTestAnnotationRector::class,
            FinalizeClassesWithoutChildrenRector::class,
        ]
    );
    $parameters->set(
        Option::PATHS,
        [__DIR__ . '/packages', __DIR__ . '/ecs.php', __DIR__ . '/monorepo-builder.php', __DIR__ . '/rector.php']
    );
};
