<?php

declare(strict_types=1);

namespace LaravelInteraction\Vote\Tests\Concerns;

use Iterator;
use LaravelInteraction\Vote\Tests\Models\Channel;
use LaravelInteraction\Vote\Tests\Models\User;
use LaravelInteraction\Vote\Tests\TestCase;

/**
 * @internal
 */
final class VoteableTest extends TestCase
{
    /**
     * @return \Iterator<array<class-string<\LaravelInteraction\Vote\Tests\Models\Channel|\LaravelInteraction\Vote\Tests\Models\User>>>
     */
    public function provideModelClasses(): Iterator
    {
        yield [Channel::class];

        yield [User::class];
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testVotes($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame(1, $model->voteableVotes()->count());
        self::assertSame(1, $model->voteableVotes->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testVotersCount($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame(1, $model->votersCount());
        $user->cancelVote($model);
        self::assertSame(1, $model->votersCount());
        $model->loadCount('voters');
        self::assertSame(0, $model->votersCount());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testUpvotersCount($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        self::assertSame(1, $model->upvotersCount());
        $user->cancelVote($model);
        self::assertSame(1, $model->upvotersCount());
        $model->loadCount('upvoters');
        self::assertSame(0, $model->upvotersCount());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testDownvotersCount($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        self::assertSame(1, $model->downvotersCount());
        $user->cancelVote($model);
        self::assertSame(1, $model->downvotersCount());
        $model->loadCount('downvoters');
        self::assertSame(0, $model->downvotersCount());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testVotersCountForHumans($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame('1', $model->votersCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testUpvotersCountForHumans($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        self::assertSame('1', $model->upvotersCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testDownvotersCountForHumans($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        self::assertSame('1', $model->downvotersCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testIsVotedBy($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isVotedBy($model));
        $user->vote($model);
        self::assertTrue($model->isVotedBy($user));
        $model->load('voters');
        $user->cancelVote($model);
        self::assertTrue($model->isVotedBy($user));
        $model->load('voters');
        self::assertFalse($model->isVotedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testIsNotVotedBy($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotVotedBy($model));
        $user->vote($model);
        self::assertFalse($model->isNotVotedBy($user));
        $model->load('voters');
        $user->cancelVote($model);
        self::assertFalse($model->isNotVotedBy($user));
        $model->load('voters');
        self::assertTrue($model->isNotVotedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testIsUpvotedBy($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isVotedBy($model));
        $user->upvote($model);
        self::assertTrue($model->isUpvotedBy($user));
        $model->load('upvoters');
        $user->cancelVote($model);
        self::assertTrue($model->isUpvotedBy($user));
        $model->load('upvoters');
        self::assertFalse($model->isUpvotedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testIsNotUpvotedBy($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotUpvotedBy($model));
        $user->vote($model);
        self::assertFalse($model->isNotUpvotedBy($user));
        $model->load('upvoters');
        $user->cancelVote($model);
        self::assertFalse($model->isNotUpvotedBy($user));
        $model->load('upvoters');
        self::assertTrue($model->isNotUpvotedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testIsDownvotedBy($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isDownvotedBy($model));
        $user->downvote($model);
        self::assertTrue($model->isDownvotedBy($user));
        $model->load('downvoters');
        $user->cancelVote($model);
        self::assertTrue($model->isDownvotedBy($user));
        $model->load('downvoters');
        self::assertFalse($model->isDownvotedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testIsNotDownvotedBy($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotDownvotedBy($model));
        $user->downvote($model);
        self::assertFalse($model->isNotDownvotedBy($user));
        $model->load('downvoters');
        $user->cancelVote($model);
        self::assertFalse($model->isNotDownvotedBy($user));
        $model->load('downvoters');
        self::assertTrue($model->isNotDownvotedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testVoters($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame(1, $model->voters()->count());
        $user->cancelVote($model);
        self::assertSame(0, $model->voters()->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testUpvoters($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        self::assertSame(1, $model->upvoters()->count());
        $user->cancelVote($model);
        self::assertSame(0, $model->upvoters()->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testDownvoters($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        self::assertSame(1, $model->downvoters()->count());
        $user->cancelVote($model);
        self::assertSame(0, $model->downvoters()->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereVotedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame(1, $modelClass::query()->whereVotedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereVotedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotVotedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotVotedBy($user)->count()
        );
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotVotedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereUpvotedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        self::assertSame(1, $modelClass::query()->whereUpvotedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereUpvotedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotUpvotedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        self::assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotUpvotedBy($user)->count()
        );
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotUpvotedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereDownvotedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        self::assertSame(1, $modelClass::query()->whereDownVotedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereDownVotedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotDownvotedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        self::assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotDownvotedBy($user)->count()
        );
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotDownvotedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testSumVotes($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();

        /** @var \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel $model */
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame(1, $model->sumVotes());
        $user->cancelVote($model);
        self::assertSame(1, $model->sumVotes());
        $model->loadSum('voteableVotes', 'votes');
        self::assertSame(0, $model->sumVotes());
        $user->upvote($model, 3);
        $model->loadSum('voteableVotes', 'votes');
        self::assertSame(3, $model->sumVotes());
        $other->downvote($model, 2);
        $model->loadSum('voteableVotes', 'votes');
        self::assertSame(1, $model->sumVotes());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testSumUpvotes($modelClass): void
    {
        $user = User::query()->create();

        /** @var \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel $model */
        $model = $modelClass::query()->create();
        $user->upvote($model);
        self::assertSame(1, $model->sumUpvotes());
        $user->cancelVote($model);
        self::assertSame(1, $model->sumUpvotes());
        $model->loadSum([
            'voteableVotes as voteable_votes_sum_upvotes' => function ($query) {
                return $query->where('votes', '>', 0);
            },
        ], 'votes');
        self::assertSame(0, $model->sumUpvotes());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testSumDownvotes($modelClass): void
    {
        $user = User::query()->create();

        /** @var \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel $model */
        $model = $modelClass::query()->create();
        $user->downvote($model);
        self::assertSame(-1, $model->sumDownvotes());
        $user->cancelVote($model);
        self::assertSame(-1, $model->sumDownvotes());
        $model->loadSum([
            'voteableVotes as voteable_votes_sum_downvotes' => function ($query) {
                return $query->where('votes', '<', 0);
            },
        ], 'votes');
        self::assertSame(0, $model->sumDownvotes());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testSumVotesForHumans($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame('1', $model->sumVotesForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testSumUpvotesForHumans($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        self::assertSame('1', $model->sumUpvotesForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testSumDownvotesForHumans($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        self::assertSame('-1', $model->sumDownvotesForHumans());
    }
}
