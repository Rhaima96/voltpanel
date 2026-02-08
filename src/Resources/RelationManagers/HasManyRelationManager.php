<?php

namespace Rhaima\VoltPanel\Resources\RelationManagers;

use Illuminate\Database\Eloquent\Model;

class HasManyRelationManager extends RelationManager
{
    public function canCreate(): bool
    {
        return true;
    }

    public function canEdit(Model $record): bool
    {
        return true;
    }

    public function canDelete(Model $record): bool
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
