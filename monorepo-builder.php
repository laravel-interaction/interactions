<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\Config\MBConfig;

return static function (MBConfig $config): void {
    $config->packageDirectories([__DIR__ . '/packages']);

    $config->dataToAppend(
        [
            'support' => [
                'issues' => 'https://github.com/laravel-interaction/interactions/issues',
                'source' => 'https://github.com/laravel-interaction/interactions',
            ],
            'authors' => [
                [
                    'name' => 'zingimmick',
                    'email' => 'zingimmick@outlook.com',
                    'homepage' => 'https://github.com/zingimmick',
                ],
            ],
            'require' => [
                'php' => '^8.0',
            ],
            'require-dev' => [
                'symplify/monorepo-builder' => '^11.0',
            ],
            'config' => [
                'sort-packages' => true,
                'preferred-install' => 'dist',
            ],
            'minimum-stability' => 'dev',
            'prefer-stable' => true,
        ]
    );
    $config->packageAliasFormat('<major>.x-dev');
};
