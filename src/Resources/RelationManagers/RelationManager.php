<?php

namespace Rhaima\VoltPanel\Resources\RelationManagers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Rhaima\VoltPanel\Forms\Form;
use Rhaima\VoltPanel\Tables\Table;

abstract class RelationManager
{
    protected static ?string $title = null;
    protected static ?string $icon = null;
    protected static string $relationship;
    protected static ?string $inverseRelationship = null;
    protected ?Model $ownerRecord = null;

    public function __construct(?Model $ownerRecord = null)
    {
        $this->ownerRecord = $ownerRecord;
    }

    public static function getTitle(): string
    {
        return static::$title ?? ucfirst(static::$relationship);
    }

    public static function getIcon(): ?string
    {
        return static::$icon;
    }

    public static function getRelationshipName(): string
    {
        return static::$relationship;
    }

    public function getRelationship(): Relation
    {
        return $this->ownerRecord->{static::$relationship}();
    }

    abstract public static function form(Form $form): Form;

    abstract public static function table(Table $table): Table;

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

    public function canAttach(): bool
    {
        return false;
    }

    public function canDetach(Model $record): bool
    {
        return false;
    }

    public function canAssociate(): bool
    {
        return false;
    }

    public function canDissociate(Model $record): bool
    {
        return false;
    }
}
