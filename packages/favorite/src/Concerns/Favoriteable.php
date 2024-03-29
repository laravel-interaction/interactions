<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use LaravelInteraction\Support\Interaction;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Favorite\Favorite[] $favoriteableFavorites
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Favorite\Concerns\Favoriter[] $favoriters
 * @property-read string|int|null $favoriters_count
 *
 * @method static static|\Illuminate\Database\Eloquent\Builder whereFavoritedBy(\Illuminate\Database\Eloquent\Model $user)
 * @method static static|\Illuminate\Database\Eloquent\Builder whereNotFavoritedBy(\Illuminate\Database\Eloquent\Model $user)
 */
trait Favoriteable
{
    public function isNotFavoritedBy(Model $user): bool
    {
        return ! $this->isFavoritedBy($user);
    }

    public function isFavoritedBy(Model $user): bool
    {
        if (! is_a($user, config('favorite.models.user'))) {
            return false;
        }

        $favoritersLoaded = $this->relationLoaded('favoriters');

        if ($favoritersLoaded) {
            return $this->favoriters->contains($user);
        }

        return ($this->relationLoaded(
            'favoriteableFavorites'
        ) ? $this->favoriteableFavorites : $this->favoriteableFavorites())
            ->where(config('favorite.column_names.user_foreign_key'), $user->getKey())
            ->count() > 0;
    }

    public function scopeWhereNotFavoritedBy(Builder $query, Model $user): Builder
    {
        return $query->whereDoesntHave(
            'favoriters',
            static fn (Builder $query): Builder => $query->whereKey($user->getKey())
        );
    }

    public function scopeWhereFavoritedBy(Builder $query, Model $user): Builder
    {
        return $query->whereHas(
            'favoriters',
            static fn (Builder $query): Builder => $query->whereKey($user->getKey())
        );
    }

    public function favoriteableFavorites(): MorphMany
    {
        return $this->morphMany(config('favorite.models.pivot'), 'favoriteable');
    }

    public function favoriters(): BelongsToMany
    {
        return $this->morphToMany(
            config('favorite.models.user'),
            'favoriteable',
            config('favorite.models.pivot'),
            null,
            config('favorite.column_names.user_foreign_key')
        )->withTimestamps();
    }

    public function favoritersCount(): int
    {
        if ($this->favoriters_count !== null) {
            return (int) $this->favoriters_count;
        }

        $this->loadCount('favoriters');

        return (int) $this->favoriters_count;
    }

    /**
     * @phpstan-param 1|2|3|4 $mode
     *
     * @param array<int, string>|null $divisors
     */
    public function favoritersCountForHumans(
        int $precision = 1,
        int $mode = PHP_ROUND_HALF_UP,
        $divisors = null
    ): string {
        return Interaction::numberForHumans(
            $this->favoritersCount(),
            $precision,
            $mode,
            $divisors ?? config('favorite.divisors')
        );
    }
}
