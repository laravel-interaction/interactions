<?php

declare(strict_types=1);

namespace LaravelInteraction\Vote;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use LaravelInteraction\Vote\Events\VoteCanceled;
use LaravelInteraction\Vote\Events\Voted;

/**
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $voter
 * @property \Illuminate\Database\Eloquent\Model $voteable
 * @property int $votes
 *
 * @method static \LaravelInteraction\Vote\Vote|\Illuminate\Database\Eloquent\Builder withType(string $type)
 * @method static \LaravelInteraction\Vote\Vote|\Illuminate\Database\Eloquent\Builder query()
 */
class Vote extends MorphPivot
{
    protected $casts = [
        'upvote' => 'bool',
    ];

    protected $dispatchesEvents = [
        'created' => Voted::class,
        'updated' => Voted::class,
        'deleted' => VoteCanceled::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(
            function (self $vote): void {
                if ($vote->uuids()) {
                    $vote->{$vote->getKeyName()} = Str::orderedUuid();
                }
            }
        );
    }

    public $incrementing = true;

    public function getIncrementing(): bool
    {
        if ($this->uuids()) {
            return false;
        }

        return parent::getIncrementing();
    }

    public function getKeyName(): string
    {
        return $this->uuids() ? 'uuid' : parent::getKeyName();
    }

    public function getKeyType(): string
    {
        return $this->uuids() ? 'string' : parent::getKeyType();
    }

    public function getTable()
    {
        return config('vote.table_names.votes') ?: parent::getTable();
    }

    public function isDownvote(): bool
    {
        return ! $this->isUpvote();
    }

    public function isUpvote(): bool
    {
        return $this->votes > 0;
    }

    public function isVotedBy(Model $user): bool
    {
        return $user->is($this->voter);
    }

    public function isVotedTo(Model $object): bool
    {
        return $object->is($this->voteable);
    }

    public function scopeWithType(Builder $query, string $type): Builder
    {
        return $query->where('voteable_type', app($type)->getMorphClass());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('vote.models.user'), config('vote.column_names.user_foreign_key'));
    }

    protected function uuids(): bool
    {
        return (bool) config('vote.uuids');
    }

    public function voteable(): MorphTo
    {
        return $this->morphTo();
    }

    public function voter(): BelongsTo
    {
        return $this->user();
    }
}
