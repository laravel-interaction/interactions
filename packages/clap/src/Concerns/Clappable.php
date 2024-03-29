<?php

declare(strict_types=1);

namespace LaravelInteraction\Clap\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use LaravelInteraction\Support\Interaction;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Clap\Applause[] $clappableApplause
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Clap\Concerns\Clapper[] $clappers
 * @property-read int|null $clappable_applause_count
 * @property-read int|null $clappers_count
 * @property float|null $clappable_applause_sum_applause
 * @property float|null $clappable_applause_avg_applause
 *
 * @method static static|\Illuminate\Database\Eloquent\Builder whereClappedBy(\Illuminate\Database\Eloquent\Model $user)
 * @method static static|\Illuminate\Database\Eloquent\Builder whereNotClappedBy(\Illuminate\Database\Eloquent\Model $user)
 * @method static static|\Illuminate\Database\Eloquent\Builder withClappersCount($constraints = null)
 */
trait Clappable
{
    public function isClappedBy(Model $user): bool
    {
        if (! is_a($user, config('clap.models.user'))) {
            return false;
        }

        $clappersLoaded = $this->relationLoaded('clappers');

        if ($clappersLoaded) {
            return $this->clappers->contains($user);
        }

        return ($this->relationLoaded('clappableApplause') ? $this->clappableApplause : $this->clappableApplause())
            ->where(config('clap.column_names.user_foreign_key'), $user->getKey())
            ->count() > 0;
    }

    public function isNotClappedBy(Model $user): bool
    {
        return ! $this->isClappedBy($user);
    }

    public function clappableApplause(): MorphMany
    {
        return $this->morphMany(config('clap.models.pivot'), 'clappable');
    }

    public function clappers(): MorphToMany
    {
        return tap(
            $this->morphToMany(
                config('clap.models.user'),
                'clappable',
                config('clap.models.pivot'),
                null,
                config('clap.column_names.user_foreign_key')
            ),
            static function (MorphToMany $relation): void {
                $relation->distinct($relation->getRelated()->qualifyColumn($relation->getRelatedKeyName()));
            }
        );
    }

    /**
     * @param callable|null $constraints
     *
     * @return $this
     */
    public function loadClappersCount($constraints = null)
    {
        $this->loadCount(
            [
                'clappers' => fn ($query) => $this->selectDistinctClappersCount($query, $constraints),
            ]
        );

        return $this;
    }

    public function clappersCount(): int
    {
        if ($this->clappers_count !== null) {
            return (int) $this->clappers_count;
        }

        $this->loadClappersCount();

        return (int) $this->clappers_count;
    }

    /**
     * @phpstan-param 1|2|3|4 $mode
     *
     * @param array<int, string>|null $divisors
     */
    public function clappersCountForHumans(int $precision = 1, int $mode = PHP_ROUND_HALF_UP, $divisors = null): string
    {
        return Interaction::numberForHumans(
            $this->clappersCount(),
            $precision,
            $mode,
            $divisors ?? config('clap.divisors')
        );
    }

    public function scopeWhereClappedBy(Builder $query, Model $user): Builder
    {
        return $query->whereHas(
            'clappers',
            static fn (Builder $query): Builder => $query->whereKey($user->getKey())
        );
    }

    public function scopeWhereNotClappedBy(Builder $query, Model $user): Builder
    {
        return $query->whereDoesntHave(
            'clappers',
            static fn (Builder $query): Builder => $query->whereKey($user->getKey())
        );
    }

    /**
     * @param callable $constraints
     */
    public function scopeWithClappersCount(Builder $query, $constraints = null): Builder
    {
        return $query->withCount(
            [
                'clappers' => fn ($query) => $this->selectDistinctClappersCount($query, $constraints),
            ]
        );
    }

    /**
     * @param callable $constraints
     */
    protected function selectDistinctClappersCount(Builder $query, $constraints = null): Builder
    {
        if ($constraints !== null) {
            $query = $constraints($query);
        }

        $column = $query->getModel()
            ->getQualifiedKeyName();

        return $query->select(DB::raw(sprintf('COUNT(DISTINCT(%s))', $column)));
    }

    public function clappableApplauseCount(): int
    {
        if ($this->clappable_applause_count !== null) {
            return (int) $this->clappable_applause_count;
        }

        $this->loadCount('clappableApplause');

        return (int) $this->clappable_applause_count;
    }

    /**
     * @phpstan-param 1|2|3|4 $mode
     *
     * @param array<int, string>|null $divisors
     */
    public function clappableApplauseCountForHumans(
        int $precision = 1,
        int $mode = PHP_ROUND_HALF_UP,
        $divisors = null
    ): string {
        return Interaction::numberForHumans(
            $this->clappableApplauseCount(),
            $precision,
            $mode,
            $divisors ?? config('clap.divisors')
        );
    }
}
