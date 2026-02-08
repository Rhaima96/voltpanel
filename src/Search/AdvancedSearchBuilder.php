<?php

namespace Rhaima\VoltPanel\Search;

use Illuminate\Database\Eloquent\Builder;

class AdvancedSearchBuilder
{
    protected Builder $query;
    protected array $searchableColumns = [];

    public function __construct(Builder $query, array $searchableColumns = [])
    {
        $this->query = $query;
        $this->searchableColumns = $searchableColumns;
    }

    public function search(string $searchTerm): Builder
    {
        $terms = $this->parseSearchTerm($searchTerm);

        foreach ($terms as $term) {
            $this->applyTerm($term);
        }

        return $this->query;
    }

    protected function parseSearchTerm(string $searchTerm): array
    {
        $terms = [];
        $pattern = '/([a-z_]+):(["\']?)([^"\']+)\2|([^"\'\s]+)/i';

        preg_match_all($pattern, $searchTerm, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (!empty($match[1])) {
                // Field-specific search (e.g., email:john@example.com)
                $terms[] = [
                    'type' => 'field',
                    'field' => $match[1],
                    'value' => $match[3],
                ];
            } else {
                // General search
                $terms[] = [
                    'type' => 'general',
                    'value' => $match[4],
                ];
            }
        }

        return $terms;
    }

    protected function applyTerm(array $term): void
    {
        if ($term['type'] === 'field') {
            $this->applyFieldSearch($term['field'], $term['value']);
        } else {
            $this->applyGeneralSearch($term['value']);
        }
    }

    protected function applyFieldSearch(string $field, string $value): void
    {
        // Handle operators
        if (str_contains($value, '>')) {
            [$operator, $val] = explode('>', $value);
            $this->query->where($field, '>', $val);
        } elseif (str_contains($value, '<')) {
            [$operator, $val] = explode('<', $value);
            $this->query->where($field, '<', $val);
        } elseif (str_contains($value, '>=')) {
            [$operator, $val] = explode('>=', $value);
            $this->query->where($field, '>=', $val);
        } elseif (str_contains($value, '<=')) {
            [$operator, $val] = explode('<=', $value);
            $this->query->where($field, '<=', $val);
        } elseif (str_contains($value, '*')) {
            $this->query->where($field, 'LIKE', str_replace('*', '%', $value));
        } else {
            $this->query->where($field, $value);
        }
    }

    protected function applyGeneralSearch(string $value): void
    {
        if (empty($this->searchableColumns)) {
            return;
        }

        $this->query->where(function ($query) use ($value) {
            foreach ($this->searchableColumns as $column) {
                $query->orWhere($column, 'LIKE', "%{$value}%");
            }
        });
    }
}
