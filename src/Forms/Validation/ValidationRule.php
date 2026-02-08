<?php

namespace Rhaima\VoltPanel\Forms\Validation;

use Closure;

class ValidationRule
{
    protected string $name;
    protected array $parameters = [];
    protected ?string $message = null;
    protected ?Closure $validator = null;

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function parameters(array $parameters): static
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function message(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function validator(Closure $callback): static
    {
        $this->validator = $callback;
        return $this;
    }

    // Common validation rules
    public static function unique(string $table, ?string $column = null, ?int $ignoreId = null): static
    {
        $rule = new static('unique');
        $params = [$table];

        if ($column) {
            $params[] = $column;
        }

        if ($ignoreId) {
            $params[] = $ignoreId;
        }

        return $rule->parameters($params);
    }

    public static function exists(string $table, string $column = 'id'): static
    {
        return (new static('exists'))->parameters([$table, $column]);
    }

    public static function regex(string $pattern): static
    {
        return (new static('regex'))->parameters([$pattern]);
    }

    public static function confirmed(): static
    {
        return new static('confirmed');
    }

    public static function min(int $value): static
    {
        return (new static('min'))->parameters([$value]);
    }

    public static function max(int $value): static
    {
        return (new static('max'))->parameters([$value]);
    }

    public static function between(int $min, int $max): static
    {
        return (new static('between'))->parameters([$min, $max]);
    }

    public static function email(): static
    {
        return new static('email');
    }

    public static function url(): static
    {
        return new static('url');
    }

    public static function json(): static
    {
        return new static('json');
    }

    public static function custom(Closure $validator, ?string $message = null): static
    {
        $rule = new static('custom');
        $rule->validator = $validator;

        if ($message) {
            $rule->message = $message;
        }

        return $rule;
    }

    public function toRule(): string
    {
        if (empty($this->parameters)) {
            return $this->name;
        }

        return $this->name . ':' . implode(',', $this->parameters);
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function validate(mixed $value): bool
    {
        if ($this->validator) {
            return call_user_func($this->validator, $value);
        }

        return true;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'parameters' => $this->parameters,
            'message' => $this->message,
            'rule' => $this->toRule(),
        ];
    }
}
