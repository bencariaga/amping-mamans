<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        $searchableColumns = $this->getSearchableColumns();

        return $query->where(function ($q) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                if (str_contains($column, '.')) {
                    $this->searchRelation($q, $column, $search);
                } else {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            }
        });
    }

    protected function searchRelation(Builder $query, string $column, string $search): void
    {
        [$relation, $field] = explode('.', $column, 2);

        $query->orWhereHas($relation, function ($q) use ($field, $search) {
            if (str_contains($field, '.')) {
                $this->searchRelation($q, $field, $search);
            } else {
                $q->where($field, 'like', "%{$search}%");
            }
        });
    }

    protected function getSearchableColumns(): array
    {
        return property_exists($this, 'searchable') ? $this->searchable : [];
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if (is_null($value) || $value === '') {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    public function scopeSortBy(Builder $query, ?string $sortBy, string $direction = 'asc'): Builder
    {
        if (empty($sortBy)) {
            return $query;
        }

        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';

        if (str_contains($sortBy, '.')) {
            [$relation, $field] = explode('.', $sortBy, 2);
            return $query->orderBy(
                $this->$relation()->getRelated()->getTable() . '.' . $field,
                $direction
            );
        }

        return $query->orderBy($sortBy, $direction);
    }
}
