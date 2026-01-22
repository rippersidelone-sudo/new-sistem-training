<?php
// app/Traits/HasAdvancedFilters.php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasAdvancedFilters
{
    /**
     * Apply search filter to query
     */
    protected function applySearch(Builder $query, Request $request, array $searchableFields): Builder
    {
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search, $searchableFields) {
                foreach ($searchableFields as $field) {
                    // Handle relation searches (e.g., 'role.name')
                    if (str_contains($field, '.')) {
                        [$relation, $column] = explode('.', $field);
                        $q->orWhereHas($relation, function($query) use ($column, $search) {
                            $query->where($column, 'like', "%{$search}%");
                        });
                    } else {
                        $q->orWhere($field, 'like', "%{$search}%");
                    }
                }
            });
        }

        return $query;
    }

    /**
     * Apply single filter to query
     */
    protected function applyFilter(Builder $query, Request $request, string $filterName, string $column): Builder
    {
        if ($value = $request->input($filterName)) {
            // Handle relation filters (e.g., 'role.name')
            if (str_contains($column, '.')) {
                [$relation, $relColumn] = explode('.', $column);
                $query->whereHas($relation, function($q) use ($relColumn, $value) {
                    $q->where($relColumn, $value);
                });
            } else {
                $query->where($column, $value);
            }
        }

        return $query;
    }

    /**
     * Apply date range filter to query
     */
    protected function applyDateRange(Builder $query, Request $request, string $column = 'created_at'): Builder
    {
        if ($startDate = $request->input('start_date')) {
            $query->whereDate($column, '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate($column, '<=', $endDate);
        }

        return $query;
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting(Builder $query, Request $request, string $defaultColumn = 'created_at', string $defaultDirection = 'desc'): Builder
    {
        $sortBy = $request->input('sort_by', $defaultColumn);
        $sortDirection = $request->input('sort_direction', $defaultDirection);

        // Validate sort direction
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = $defaultDirection;
        }

        return $query->orderBy($sortBy, $sortDirection);
    }

    /**
     * Get active filters count
     */
    protected function getActiveFiltersCount(Request $request, array $filterKeys): int
    {
        $count = 0;
        
        foreach ($filterKeys as $key) {
            if ($request->filled($key) && $request->input($key) !== '') {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Build filter URL with preserved parameters
     */
    protected function buildFilterUrl(Request $request, array $additionalParams = []): string
    {
        $params = array_merge($request->except('page'), $additionalParams);
        return request()->url() . '?' . http_build_query($params);
    }
}