<?php

declare(strict_types=1);

namespace LaravelInteraction\Vote\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelInteraction\Vote\Tests\Models\User;
use LaravelInteraction\Vote\VoteServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @before
     */
    protected function setUpDatabaseMigrations(): void
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
            'vote.models.user' => User::class,
            'vote.uuids' => false,
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array<class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [VoteServiceProvider::class];
    }
}
