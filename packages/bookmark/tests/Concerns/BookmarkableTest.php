<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Tests\Concerns;

use LaravelInteraction\Bookmark\Tests\Models\Channel;
use LaravelInteraction\Bookmark\Tests\Models\User;
use LaravelInteraction\Bookmark\Tests\TestCase;

class BookmarkableTest extends TestCase
{
    public function modelClasses(): array
    {
        return[[Channel::class], [User::class]];
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel $modelClass
     */
    public function testBookmarkableBookmarks($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(1, $model->bookmarkableBookmarks()->count());
        self::assertSame(1, $model->bookmarkableBookmarks->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel $modelClass
     */
    public function testBookmarkersCount($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(1, $model->bookmarkersCount());
        $user->unbookmark($model);
        self::assertSame(1, $model->bookmarkersCount());
        $model->loadCount('bookmarkers');
        self::assertSame(0, $model->bookmarkersCount());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel $modelClass
     */
    public function testBookmarkersCountForHumans($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame('1', $model->bookmarkersCountForHumans());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel $modelClass
     */
    public function testIsBookmarkedBy($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isBookmarkedBy($model));
        $user->bookmark($model);
        self::assertTrue($model->isBookmarkedBy($user));
        $model->load('bookmarkers');
        $user->unbookmark($model);
        self::assertTrue($model->isBookmarkedBy($user));
        $model->load('bookmarkers');
        self::assertFalse($model->isBookmarkedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel $modelClass
     */
    public function testIsNotBookmarkedBy($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotBookmarkedBy($model));
        $user->bookmark($model);
        self::assertFalse($model->isNotBookmarkedBy($user));
        $model->load('bookmarkers');
        $user->unbookmark($model);
        self::assertFalse($model->isNotBookmarkedBy($user));
        $model->load('bookmarkers');
        self::assertTrue($model->isNotBookmarkedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel $modelClass
     */
    public function testBookmarkers($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(1, $model->bookmarkers()->count());
        $user->unbookmark($model);
        self::assertSame(0, $model->bookmarkers()->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel $modelClass
     */
    public function testScopeWhereBookmarkedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(1, $modelClass::query()->whereBookmarkedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereBookmarkedBy($other)->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel $modelClass
     */
    public function testScopeWhereNotBookmarkedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotBookmarkedBy($user)->count()
        );
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotBookmarkedBy($other)->count());
    }
}
