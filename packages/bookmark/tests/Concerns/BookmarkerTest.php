<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Tests\Concerns;

use LaravelInteraction\Bookmark\Bookmark;
use LaravelInteraction\Bookmark\Tests\Models\Channel;
use LaravelInteraction\Bookmark\Tests\Models\User;
use LaravelInteraction\Bookmark\Tests\TestCase;

class BookmarkerTest extends TestCase
{
    protected function getMorphs($name, $type, $id)
    {
        return [$type ?: $name . '_type', $id ?: $name . '_id'];
    }

    public function testBookmark(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->bookmark($channel);
        [$type,$id] = $this->getMorphs(config('bookmark.morph_name', 'bookmarkable'), null, null);
        $this->assertDatabaseHas(
            Bookmark::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                $type => $channel->getMorphClass(),
                $id => $channel->getKey(),
            ]
        );
    }

    public function testUnbookmark(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->bookmark($channel);
        [$type,$id] = $this->getMorphs(config('bookmark.morph_name', 'bookmarkable'), null, null);
        $this->assertDatabaseHas(
            Bookmark::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                $type => $channel->getMorphClass(),
                $id => $channel->getKey(),
            ]
        );
        $user->unbookmark($channel);
        $this->assertDatabaseMissing(
            Bookmark::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                $type => $channel->getMorphClass(),
                $id => $channel->getKey(),
            ]
        );
    }

    public function testToggleBookmark(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleBookmark($channel);
        [$type,$id] = $this->getMorphs(config('bookmark.morph_name', 'bookmarkable'), null, null);
        $this->assertDatabaseHas(
            Bookmark::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                $type => $channel->getMorphClass(),
                $id => $channel->getKey(),
            ]
        );
        $user->toggleBookmark($channel);
        $this->assertDatabaseMissing(
            Bookmark::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                $type => $channel->getMorphClass(),
                $id => $channel->getKey(),
            ]
        );
    }

    public function testBookmarkerBookmarks(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleBookmark($channel);
        self::assertSame(1, $user->bookmarkerBookmarks()->count());
        self::assertSame(1, $user->bookmarkerBookmarks->count());
    }

    public function testHasBookmarked(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleBookmark($channel);
        self::assertTrue($user->hasBookmarked($channel));
        $user->toggleBookmark($channel);
        $user->load('bookmarkerBookmarks');
        self::assertFalse($user->hasBookmarked($channel));
    }

    public function testHasNotBookmarked(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleBookmark($channel);
        self::assertFalse($user->hasNotBookmarked($channel));
        $user->toggleBookmark($channel);
        self::assertTrue($user->hasNotBookmarked($channel));
    }
}
