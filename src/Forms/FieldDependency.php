<?php

namespace Rhaima\VoltPanel\Forms;

use Closure;

class FieldDependency
{
    protected string $field;
    protected mixed $value;
    protected string $operator;
    protected ?Closure $callback = null;

    public static function make(string $field): static
    {
        return new static($field);
    }

    public function __construct(string $field)
    {
        $this->field = $field;
        $this->operator = '=';
    }

    public function equals(mixed $value): static
    {
        $this->value = $value;
        $this->operator = '=';
        return $this;
    }

    public function notEquals(mixed $value): static
    {
        $this->value = $value;
        $this->operator = '!=';
        return $this;
    }

    public function greaterThan(mixed $value): static
    {
        $this->value = $value;
        $this->operator = '>';
        return $this;
    }

    public function greaterThanOrEqual(mixed $value): static
    {
        $this->value = $value;
        $this->operator = '>=';
        return $this;
    }

    public function lessThan(mixed $value): static
    {
        $this->value = $value;
        $this->operator = '<';
        return $this;
    }

    public function lessThanOrEqual(mixed $value): static
    {
        $this->value = $value;
        $this->operator = '<=';
        return $this;
    }

    public function in(array $values): static
    {
        $this->value = $values;
        $this->operator = 'in';
        return $this;
    }

    public function notIn(array $values): static
    {
        $this->value = $values;
        $this->operator = 'not_in';
        return $this;
    }

    public function contains(string $value): static
    {
        $this->value = $value;
        $this->operator = 'contains';
        return $this;
    }

    public function filled(): static
    {
        $this->operator = 'filled';
        return $this;
    }

    public function blank(): static
    {
        $this->operator = 'blank';
        return $this;
    }

    public function custom(Closure $callback): static
    {
        $this->callback = $callback;
        return $this;
    }

    public function evaluate(mixed $fieldValue): bool
    {
        if ($this->callback) {
            return call_user_func($this->callback, $fieldValue);
        }

        return match($this->operator) {
            '=' => $fieldValue == $this->value,
            '==' => $fieldValue === $this->value,
            '!=' => $fieldValue != $this->value,
            '!==' => $fieldValue !== $this->value,
            '>' => $fieldValue > $this->value,
            '>=' => $fieldValue >= $this->value,
            '<' => $fieldValue < $this->value,
            '<=' => $fieldValue <= $this->value,
            'in' => in_array($fieldValue, (array) $this->value),
            'not_in' => !in_array($fieldValue, (array) $this->value),
            'contains' => str_contains((string) $fieldValue, (string) $this->value),
            'filled' => filled($fieldValue),
            'blank' => blank($fieldValue),
            default => false,
        };
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'value' => $this->value ?? null,
            'operator' => $this->operator,
            'hasCallback' => $this->callback !== null,
        ];
    }
}
