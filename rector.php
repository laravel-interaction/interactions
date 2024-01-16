<?php

declare(strict_types=1);

use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\Set\ValueObject\LevelSetList;
use Zing\CodingStandard\Set\RectorSetList;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->sets([LevelSetList::UP_TO_PHP_80, PHPUnitSetList::PHPUNIT_CODE_QUALITY, RectorSetList::CUSTOM]);
    $rectorConfig->phpstanConfig(__DIR__ . '/phpstan.neon');
    $rectorConfig->bootstrapFiles([
        __DIR__ . '/vendor/squizlabs/php_codesniffer/autoload.php',
        __DIR__ . '/vendor/symplify/easy-coding-standard/vendor/autoload.php',
        __DIR__ . '/vendor/nunomaduro/larastan/bootstrap.php',
    ]);
    $rectorConfig->skip([
        RenameParamToMatchTypeRector::class,
        AddSeeTestAnnotationRector::class,
        FinalizeClassesWithoutChildrenRector::class,
    ]);
    $rectorConfig->paths(
        [__DIR__ . '/packages', __DIR__ . '/ecs.php', __DIR__ . '/monorepo-builder.php', __DIR__ . '/rector.php']
    );
};
