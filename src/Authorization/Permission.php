<?php

namespace Rhaima\VoltPanel\Authorization;

class Permission
{
    protected string $name;
    protected ?string $description = null;
    protected ?string $group = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function group(string $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public static function all(): array
    {
        return [
            self::make('view_any')->description('View any records')->group('View'),
            self::make('view')->description('View records')->group('View'),
            self::make('create')->description('Create records')->group('Create'),
            self::make('update')->description('Update records')->group('Update'),
            self::make('delete')->description('Delete records')->group('Delete'),
            self::make('restore')->description('Restore records')->group('Delete'),
            self::make('force_delete')->description('Force delete records')->group('Delete'),
        ];
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'group' => $this->group,
        ];
    }
}
