<?php

namespace Rhaima\VoltPanel\Tables\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    protected string $name;
    protected ?string $label = null;
    protected ?Closure $query = null;
    protected mixed $default = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function query(Closure $callback): static
    {
        $this->query = $callback;

        return $this;
    }

    public function default(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if ($this->query) {
            call_user_func($this->query, $query, $value);
        } else {
            // Default behavior: simple where clause
            $query->where($this->name, $value);
        }

        return $query;
    }

    abstract public function getType(): string;

    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->name,
            'label' => $this->label ?? ucfirst(str_replace('_', ' ', $this->name)),
            'default' => $this->default ?? '',
        ];
    }
}
