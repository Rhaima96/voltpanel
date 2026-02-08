<?php

namespace Rhaima\VoltPanel\Authorization;

class Role
{
    protected string $name;
    protected array $permissions = [];
    protected ?string $description = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function permissions(array $permissions): static
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions) || in_array('*', $this->permissions);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'permissions' => $this->permissions,
            'description' => $this->description,
        ];
    }
}
