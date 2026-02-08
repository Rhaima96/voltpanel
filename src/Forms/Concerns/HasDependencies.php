<?php

namespace Rhaima\VoltPanel\Forms\Concerns;

use Closure;

trait HasDependencies
{
    protected array $dependencies = [];
    protected ?Closure $visibleWhen = null;
    protected ?Closure $hiddenWhen = null;
    protected ?Closure $requiredWhen = null;
    protected ?Closure $disabledWhen = null;

    public function dependsOn(string $field, mixed $value = null, ?string $operator = '='): static
    {
        $this->dependencies[] = [
            'field' => $field,
            'value' => $value,
            'operator' => $operator,
        ];

        return $this;
    }

    public function visibleWhen(string $field, mixed $value, ?string $operator = '='): static
    {
        $this->visibleWhen = fn ($get) => $this->evaluateCondition($get($field), $value, $operator);

        return $this;
    }

    public function hiddenWhen(string $field, mixed $value, ?string $operator = '='): static
    {
        $this->hiddenWhen = fn ($get) => $this->evaluateCondition($get($field), $value, $operator);

        return $this;
    }

    public function requiredWhen(string $field, mixed $value, ?string $operator = '='): static
    {
        $this->requiredWhen = fn ($get) => $this->evaluateCondition($get($field), $value, $operator);

        return $this;
    }

    public function disabledWhen(string $field, mixed $value, ?string $operator = '='): static
    {
        $this->disabledWhen = fn ($get) => $this->evaluateCondition($get($field), $value, $operator);

        return $this;
    }

    public function visible(Closure $callback): static
    {
        $this->visibleWhen = $callback;

        return $this;
    }

    public function hidden(Closure $callback): static
    {
        $this->hiddenWhen = $callback;

        return $this;
    }

    public function required(Closure|bool $callback): static
    {
        if ($callback instanceof Closure) {
            $this->requiredWhen = $callback;
        } else {
            $this->required = $callback;
        }

        return $this;
    }

    public function disabled(Closure|bool $callback): static
    {
        if ($callback instanceof Closure) {
            $this->disabledWhen = $callback;
        } else {
            $this->disabled = $callback;
        }

        return $this;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getVisibleWhen(): ?Closure
    {
        return $this->visibleWhen;
    }

    public function getHiddenWhen(): ?Closure
    {
        return $this->hiddenWhen;
    }

    public function getRequiredWhen(): ?Closure
    {
        return $this->requiredWhen;
    }

    public function getDisabledWhen(): ?Closure
    {
        return $this->disabledWhen;
    }

    protected function evaluateCondition(mixed $fieldValue, mixed $compareValue, string $operator): bool
    {
        return match($operator) {
            '=' => $fieldValue == $compareValue,
            '==' => $fieldValue === $compareValue,
            '!=' => $fieldValue != $compareValue,
            '!==' => $fieldValue !== $compareValue,
            '>' => $fieldValue > $compareValue,
            '>=' => $fieldValue >= $compareValue,
            '<' => $fieldValue < $compareValue,
            '<=' => $fieldValue <= $compareValue,
            'in' => in_array($fieldValue, (array) $compareValue),
            'not_in' => !in_array($fieldValue, (array) $compareValue),
            'contains' => str_contains((string) $fieldValue, (string) $compareValue),
            'starts_with' => str_starts_with((string) $fieldValue, (string) $compareValue),
            'ends_with' => str_ends_with((string) $fieldValue, (string) $compareValue),
            'filled' => filled($fieldValue),
            'blank' => blank($fieldValue),
            default => false,
        };
    }

    protected function serializeDependencies(): array
    {
        return [
            'dependencies' => $this->dependencies,
            'visibleWhen' => $this->serializeClosure($this->visibleWhen),
            'hiddenWhen' => $this->serializeClosure($this->hiddenWhen),
            'requiredWhen' => $this->serializeClosure($this->requiredWhen),
            'disabledWhen' => $this->serializeClosure($this->disabledWhen),
        ];
    }

    protected function serializeClosure(?Closure $closure): ?array
    {
        if (!$closure) {
            return null;
        }

        // Extract dependency information from closure if possible
        // For simple cases, return the dependency configuration
        // For complex closures, we'll handle them in the frontend

        return [
            'type' => 'closure',
            'dependencies' => $this->dependencies,
        ];
    }
}
