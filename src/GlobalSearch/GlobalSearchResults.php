<?php

namespace Rhaima\VoltPanel\GlobalSearch;

use Illuminate\Support\Collection;

class GlobalSearchResults
{
    protected Collection $results;

    public function __construct()
    {
        $this->results = new Collection();
    }

    public function add(string $resource, Collection $records): static
    {
        $this->results->put($resource, $records);

        return $this;
    }

    public function getResults(): Collection
    {
        return $this->results;
    }

    public function isEmpty(): bool
    {
        return $this->results->isEmpty();
    }

    public function toArray(): array
    {
        return $this->results->map(function ($records, $resource) {
            return [
                'resource' => $resource,
                'label' => $resource::getPluralModelLabel(),
                'records' => $records->map(function ($record) use ($resource) {
                    return [
                        'id' => $record->getKey(),
                        'title' => $resource::getRecordTitle($record),
                        'url' => $resource::getUrl('edit', ['record' => $record]),
                    ];
                }),
            ];
        })->values()->all();
    }
}
