<?php

namespace Rhaima\VoltPanel\Resources\Pages;

use Illuminate\Database\Eloquent\Builder;

class ListRecords extends Page
{
    protected static ?string $view = 'VoltPanel/Resources/List';

    public function getTableQuery(): Builder
    {
        return static::getResource()::getModel()::query();
    }

    public function getTable(): array
    {
        $resource = static::getResource();
        $table = $resource::table(new \Rhaima\VoltPanel\Tables\Table());

        return $table->toArray();
    }

    public static function getTitle(): string
    {
        return static::$title ?? static::getResource()::getPluralModelLabel();
    }
}
