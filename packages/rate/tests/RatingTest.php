<?php

declare(strict_types=1);

namespace LaravelInteraction\Rate\Tests;

use Illuminate\Support\Carbon;
use LaravelInteraction\Rate\Rating;
use LaravelInteraction\Rate\Tests\Models\Channel;
use LaravelInteraction\Rate\Tests\Models\User;

/**
 * @internal
 */
final class RatingTest extends TestCase
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null
     */
    private $user;

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null
     */
    private $channel;

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null
     */
    private $rating;

    public function testRatingTimestamp(): void
    {
        self::assertInstanceOf(Carbon::class, $this->rating->created_at);
        self::assertInstanceOf(Carbon::class, $this->rating->updated_at);
    }

    public function testScopeWithType(): void
    {
        self::assertSame(1, Rating::query()->withType(Channel::class)->count());
        self::assertSame(0, Rating::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        self::assertSame(config('rate.table_names.pivot'), $this->rating->getTable());
    }

    public function testRater(): void
    {
        self::assertInstanceOf(User::class, $this->rating->rater);
    }

    public function testRatable(): void
    {
        self::assertInstanceOf(Channel::class, $this->rating->ratable);
    }

    public function testUser(): void
    {
        self::assertInstanceOf(User::class, $this->rating->user);
    }

    public function testIsRatedTo(): void
    {
        self::assertTrue($this->rating->isRatedTo($this->channel));
        self::assertFalse($this->rating->isRatedTo($this->user));
    }

    public function testIsRatedBy(): void
    {
        self::assertFalse($this->rating->isRatedBy($this->channel));
        self::assertTrue($this->rating->isRatedBy($this->user));
    }
}
