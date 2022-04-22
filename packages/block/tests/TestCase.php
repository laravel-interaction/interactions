<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelInteraction\Block\BlockServiceProvider;
use LaravelInteraction\Block\Tests\Models\User;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @before
     */
    public function setUpDatabaseMigrations(): void
    {
        $this->afterApplicationCreated(function (): void {
            $this->loadMigrationsFrom(__DIR__ . '/../migrations');
            Schema::create(
                'users',
                function (Blueprint $table): void {
                    $table->bigIncrements('id');
                    $table->timestamps();
                }
            );
            Schema::create(
                'channels',
                function (Blueprint $table): void {
                    $table->bigIncrements('id');
                    $table->timestamps();
                }
            );
        });
    }

    protected function getEnvironmentSetUp($app): void
    {
        config([
            'database.default' => 'testing',
            'block.models.user' => User::class,
            'block.uuids' => false,
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array<class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [BlockServiceProvider::class];
    }
}
