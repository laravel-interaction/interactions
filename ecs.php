<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Zing\CodingStandard\Set\ECSSetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(ECSSetList::CUSTOM);
    $containerConfigurator->import(ECSSetList::PHP71_MIGRATION);
    $containerConfigurator->import(ECSSetList::PHP71_MIGRATION_RISKY);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PARALLEL, true);
    $parameters->set(
        Option::SKIP,
        [
            // Will be removed in a future major version.
            \SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff::class,

            // issue: https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/5850
            PhpdocTypesOrderFixer::class,
        ]
    );
    $parameters->set(
        Option::PATHS,
        [__DIR__ . '/packages', __DIR__ . '/ecs.php', __DIR__ . '/monorepo-builder.php', __DIR__ . '/rector.php']
    );
};
