<?php

namespace Rhaima\VoltPanel\Pages;

abstract class Page
{
    protected static ?string $navigationLabel = null;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = null;
    protected static ?string $navigationDescription = null;

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::getTitleFromClassName();
    }

    public static function getNavigationIcon(): ?string
    {
        return static::$navigationIcon;
    }

    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    public static function getNavigationSort(): int
    {
        return static::$navigationSort ?? 0;
    }

    public static function getNavigationDescription(): ?string
    {
        return static::$navigationDescription;
    }

    public static function canAccess(): bool
    {
        return true;
    }

    protected static function getTitleFromClassName(): string
    {
        $className = class_basename(static::class);

        return str($className)
            ->beforeLast('Page')
            ->kebab()
            ->replace('-', ' ')
            ->title()
            ->toString();
    }
}
