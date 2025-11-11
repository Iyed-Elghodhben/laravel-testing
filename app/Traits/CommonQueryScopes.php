<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CommonQueryScopes
{
    /**
     * Filtrer par date
     */
    public function scopeFilterByDate(Builder $query, ?string $date): Builder
    {
        return $date ? $query->whereDate('date', $date) : $query;
    }

    /**
     * Rechercher par titre
     */
    public function scopeSearchByTitle(Builder $query, ?string $search): Builder
    {
        return $search ? $query->where('title', 'like', '%' . $search . '%') : $query;
    }

    /**
     * Filtrer par location
     */
    public function scopeFilterByLocation(Builder $query, ?string $location): Builder
    {
        return $location ? $query->where('location', 'like', '%' . $location . '%') : $query;
    }
}
