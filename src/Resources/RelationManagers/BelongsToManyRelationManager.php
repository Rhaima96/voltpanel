<?php

namespace Rhaima\VoltPanel\Resources\RelationManagers;

use Illuminate\Database\Eloquent\Model;

class BelongsToManyRelationManager extends RelationManager
{
    protected static bool $isPreloaded = true;

    public function canAttach(): bool
    {
        return true;
    }

    public function canDetach(Model $record): bool
    {
        return true;
    }

    public static function form(\Rhaima\VoltPanel\Forms\Form $form): \Rhaima\VoltPanel\Forms\Form
    {
        return $form;
    }

    public static function table(\Rhaima\VoltPanel\Tables\Table $table): \Rhaima\VoltPanel\Tables\Table
    {
        return $table;
    }
}
