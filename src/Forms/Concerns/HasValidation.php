<?php

namespace Rhaima\VoltPanel\Forms\Concerns;

use Rhaima\VoltPanel\Forms\Validation\ValidationRule;
use Closure;

trait HasValidation
{
    protected array $rules = [];
    protected array $validationMessages = [];
    protected ?Closure $customValidator = null;

    public function rules(array|string $rules): static
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        $this->rules = array_merge($this->rules, $rules);

        return $this;
    }

    public function rule(string|ValidationRule $rule): static
    {
        if ($rule instanceof ValidationRule) {
            $this->rules[] = $rule->toRule();

            if ($message = $rule->getMessage()) {
                $this->validationMessages[$rule->toRule()] = $message;
            }
        } else {
            $this->rules[] = $rule;
        }

        return $this;
    }

    public function unique(string $table, ?string $column = null, ?int $ignoreId = null): static
    {
        return $this->rule(ValidationRule::unique($table, $column, $ignoreId));
    }

    public function exists(string $table, string $column = 'id'): static
    {
        return $this->rule(ValidationRule::exists($table, $column));
    }

    public function regex(string $pattern, ?string $message = null): static
    {
        $rule = ValidationRule::regex($pattern);

        if ($message) {
            $rule->message($message);
        }

        return $this->rule($rule);
    }

    public function email(): static
    {
        return $this->rule('email');
    }

    public function url(): static
    {
        return $this->rule('url');
    }

    public function min(int $value): static
    {
        return $this->rule("min:$value");
    }

    public function max(int $value): static
    {
        return $this->rule("max:$value");
    }

    public function between(int $min, int $max): static
    {
        return $this->rule("between:$min,$max");
    }

    public function confirmed(): static
    {
        return $this->rule('confirmed');
    }

    public function customValidation(Closure $callback): static
    {
        $this->customValidator = $callback;
        return $this;
    }

    public function validationMessages(array $messages): static
    {
        $this->validationMessages = array_merge($this->validationMessages, $messages);
        return $this;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getValidationMessages(): array
    {
        return $this->validationMessages;
    }

    public function hasCustomValidator(): bool
    {
        return $this->customValidator !== null;
    }

    public function runCustomValidator(mixed $value): bool
    {
        if (!$this->customValidator) {
            return true;
        }

        return call_user_func($this->customValidator, $value);
    }

    protected function serializeValidation(): array
    {
        return [
            'rules' => $this->rules,
            'messages' => $this->validationMessages,
            'hasCustomValidator' => $this->hasCustomValidator(),
        ];
    }
}
