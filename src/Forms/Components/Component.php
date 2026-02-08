<?php

namespace Rhaima\VoltPanel\Forms\Components;

use Closure;
use Rhaima\VoltPanel\Forms\Concerns\HasDependencies;
use Rhaima\VoltPanel\Forms\Concerns\HasValidation;

abstract class Component
{
    use HasDependencies;
    use HasValidation;

    protected string $name;
    protected ?string $label = null;
    protected bool $required = false;
    protected ?Closure $requiredWhen = null;
    protected ?string $placeholder = null;
    protected ?string $helperText = null;
    protected mixed $default = null;
    protected ?Closure $visible = null;
    protected bool $disabled = false;
    protected ?\Rhaima\VoltPanel\Forms\Form $form = null;
    protected int $columnSpan = 12; // Default full width (12 columns)

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function form(\Rhaima\VoltPanel\Forms\Form $form): static
    {
        $this->form = $form;

        return $this;
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

    public function required(Closure|bool $required = true): static
    {
        if ($required instanceof Closure) {
            $this->requiredWhen = $required;
        } else {
            $this->required = $required;
        }

        return $this;
    }

    public function requiredOnCreate(): static
    {
        return $this->required(fn () => $this->form?->isCreating() ?? true);
    }

    public function requiredOnEdit(): static
    {
        return $this->required(fn () => $this->form?->isEditing() ?? false);
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function helperText(string $text): static
    {
        $this->helperText = $text;

        return $this;
    }

    public function default(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function visible(Closure $callback): static
    {
        $this->visible = $callback;

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

    public function columnSpan(int $span): static
    {
        $this->columnSpan = min(12, max(1, $span)); // Ensure between 1-12

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function getType(): string;

    public function toArray(): array
    {
        $required = $this->required;

        // Evaluate requiredWhen closure if it exists
        if ($this->requiredWhen !== null) {
            $required = call_user_func($this->requiredWhen);
        }

        return array_merge([
            'type' => $this->getType(),
            'name' => $this->name,
            'label' => $this->label ?? ucfirst(str_replace('_', ' ', $this->name)),
            'required' => $required,
            'placeholder' => $this->placeholder ?? '',
            'helperText' => $this->helperText ?? '',
            'default' => $this->default ?? '',
            'disabled' => $this->disabled,
            'columnSpan' => $this->columnSpan,
        ], $this->serializeDependencies(), $this->serializeValidation());
    }
}
