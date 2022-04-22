<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelInteraction\Bookmark\BookmarkServiceProvider;
use LaravelInteraction\Bookmark\Tests\Models\User;
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
        config(
            [
                'database.default' => 'testing',
                'bookmark.models.user' => User::class,
                'bookmark.uuids' => false,
            ]
        );
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array<class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [BookmarkServiceProvider::class];
    }
}
