<?php

namespace Rhaima\VoltPanel\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Rhaima\VoltPanel\Forms\Form;
use Rhaima\VoltPanel\Tables\Table;
use Rhaima\VoltPanel\Resources\Concerns\Exportable;

abstract class Resource
{
    use Exportable;
    protected static ?string $model = null;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;
    protected static ?string $navigationParentItem = null;
    protected static ?int $navigationSort = null;
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $slug = null;
    protected static ?string $recordTitleAttribute = null;
    protected static bool $tablePreferences = false;

    public static function getModel(): string
    {
        return static::$model ?? (string) Str::of(class_basename(static::class))
            ->beforeLast('Resource')
            ->prepend('App\\Models\\');
    }

    public static function getModelLabel(): string
    {
        return (string) Str::of(class_basename(static::getModel()))
            ->headline()
            ->singular();
    }

    public static function getPluralModelLabel(): string
    {
        return (string) Str::of(class_basename(static::getModel()))
            ->headline()
            ->plural();
    }

    public static function getSlug(): string
    {
        return static::$slug ?? (string) Str::of(class_basename(static::class))
            ->beforeLast('Resource')
            ->kebab()
            ->plural();
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::getPluralModelLabel();
    }

    public static function getNavigationIcon(): ?string
    {
        return static::$navigationIcon;
    }

    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    public static function getNavigationParentItem(): ?string
    {
        return static::$navigationParentItem;
    }

    public static function getNavigationSort(): ?int
    {
        return static::$navigationSort;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::$shouldRegisterNavigation;
    }

    public static function getRecordTitleAttribute(): ?string
    {
        return static::$recordTitleAttribute;
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        if (! $record) {
            return null;
        }

        $titleAttribute = static::getRecordTitleAttribute();

        return $titleAttribute ? $record->getAttribute($titleAttribute) : $record->getKey();
    }

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecords::class,
            'create' => Pages\CreateRecord::class,
            'edit' => Pages\EditRecord::class,
        ];
    }

    public static function getUrl(string $name = 'index', array $parameters = []): string
    {
        return route(
            'voltpanel.resources.'.$name,
            array_merge(['resource' => static::getSlug()], $parameters)
        );
    }

    public static function getRouteBaseName(): string
    {
        return 'voltpanel.resources';
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canView(Model $record): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }

    public static function canDeleteAny(): bool
    {
        return true;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [static::getRecordTitleAttribute() ?? 'id'];
    }

    public static function getGlobalSearchResults(string $search): \Illuminate\Support\Collection
    {
        $model = static::getModel();
        $query = $model::query();

        $searchableAttributes = static::getGloballySearchableAttributes();

        $query->where(function ($query) use ($searchableAttributes, $search) {
            foreach ($searchableAttributes as $attribute) {
                $query->orWhere($attribute, 'LIKE', "%{$search}%");
            }
        });

        return $query->limit(10)->get();
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return static::getRecordTitle($record) ?? "#{$record->getKey()}";
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('edit', ['record' => $record]);
    }

    public static function hasTablePreferences(): bool
    {
        return static::$tablePreferences;
    }
}
