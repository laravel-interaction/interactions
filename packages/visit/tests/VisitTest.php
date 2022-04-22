<?php

declare(strict_types=1);

namespace LaravelInteraction\Visit\Tests;

use Illuminate\Support\Carbon;
use LaravelInteraction\Visit\Tests\Models\Subject;
use LaravelInteraction\Visit\Tests\Models\User;
use LaravelInteraction\Visit\Visit;

/**
 * @internal
 */
final class VisitTest extends TestCase
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null
     */
    private $user;

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null
     */
    private $subject;

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null
     */
    private $visit;

    public function testVisitTimestamp(): void
    {
        self::assertInstanceOf(Carbon::class, $this->visit->created_at);
        self::assertInstanceOf(Carbon::class, $this->visit->updated_at);
    }

    public function testScopeWithType(): void
    {
        self::assertSame(1, Visit::query()->withType(Subject::class)->count());
        self::assertSame(0, Visit::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        self::assertSame(config('visit.table_names.pivot'), $this->visit->getTable());
    }

    public function testVisitor(): void
    {
        self::assertInstanceOf(User::class, $this->visit->visitor);
    }

    public function testVisitable(): void
    {
        self::assertInstanceOf(Subject::class, $this->visit->visitable);
    }

    public function testUser(): void
    {
        self::assertInstanceOf(User::class, $this->visit->user);
    }

    public function testIsVisitedTo(): void
    {
        self::assertTrue($this->visit->isVisitedTo($this->subject));
        self::assertFalse($this->visit->isVisitedTo($this->user));
    }

    public function testIsVisitedBy(): void
    {
        self::assertFalse($this->visit->isVisitedBy($this->subject));
        self::assertTrue($this->visit->isVisitedBy($this->user));
    }
}
