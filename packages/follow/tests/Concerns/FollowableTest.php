<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Tests\Concerns;

use LaravelInteraction\Follow\Tests\Models\Channel;
use LaravelInteraction\Follow\Tests\Models\User;
use LaravelInteraction\Follow\Tests\TestCase;

class FollowableTest extends TestCase
{
    public function modelClasses(): array
    {
        return[[Channel::class], [User::class]];
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel $modelClass
     */
    public function testFollowings($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->follow($model);
        self::assertSame(1, $model->followableFollowings()->count());
        self::assertSame(1, $model->followableFollowings->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel $modelClass
     */
    public function testFollowersCount($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->follow($model);
        self::assertSame(1, $model->followersCount());
        $user->unfollow($model);
        self::assertSame(1, $model->followersCount());
        $model->loadCount('followers');
        self::assertSame(0, $model->followersCount());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel $modelClass
     */
    public function testFollowersCountForHumans($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->follow($model);
        self::assertSame('1', $model->followersCountForHumans());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel $modelClass
     */
    public function testIsFollowedBy($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isFollowedBy($model));
        $user->follow($model);
        self::assertTrue($model->isFollowedBy($user));
        $model->load('followers');
        $user->unfollow($model);
        self::assertTrue($model->isFollowedBy($user));
        $model->load('followers');
        self::assertFalse($model->isFollowedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel $modelClass
     */
    public function testIsNotFollowedBy($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotFollowedBy($model));
        $user->follow($model);
        self::assertFalse($model->isNotFollowedBy($user));
        $model->load('followers');
        $user->unfollow($model);
        self::assertFalse($model->isNotFollowedBy($user));
        $model->load('followers');
        self::assertTrue($model->isNotFollowedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel $modelClass
     */
    public function testFollowers($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->follow($model);
        self::assertSame(1, $model->followers()->count());
        $user->unfollow($model);
        self::assertSame(0, $model->followers()->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel $modelClass
     */
    public function testScopeWhereFollowedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->follow($model);
        self::assertSame(1, $modelClass::query()->whereFollowedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereFollowedBy($other)->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel $modelClass
     */
    public function testScopeWhereNotFollowedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->follow($model);
        self::assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotFollowedBy($user)->count()
        );
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotFollowedBy($other)->count());
    }
}
