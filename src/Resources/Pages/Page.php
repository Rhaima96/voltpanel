<?php

namespace Rhaima\VoltPanel\Resources\Pages;

use Illuminate\Database\Eloquent\Model;

abstract class Page
{
    protected static string $resource;
    protected static ?string $title = null;
    protected static ?string $view = null;

    public function __construct(
        protected ?Model $record = null
    ) {
    }

    public static function getResource(): string
    {
        return static::$resource;
    }

    public static function getTitle(): string
    {
        return static::$title ?? static::getResource()::getModelLabel();
    }

    public static function getView(): string
    {
        return static::$view ?? 'voltpanel::pages.default';
    }

    public function getRecord(): ?Model
    {
        return $this->record;
    }

    public function mount(): void
    {
        //
    }

    public function getViewData(): array
    {
        return [];
    }
}
