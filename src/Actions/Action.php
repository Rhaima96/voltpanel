<?php

namespace Rhaima\VoltPanel\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;

class Action
{
    public string $name;
    public ?string $label = null;
    public ?string $icon = null;
    public ?string $color = 'primary';
    public ?Closure $action = null;
    public ?Closure $visible = null;
    public ?Closure $disabled = null;
    public bool $requiresConfirmation = false;
    public ?string $confirmationTitle = null;
    public ?string $confirmationText = null;
    public ?string $successNotification = null;

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

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function action(Closure $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function visible(Closure $callback): static
    {
        $this->visible = $callback;

        return $this;
    }

    public function disabled(Closure $callback): static
    {
        $this->disabled = $callback;

        return $this;
    }

    public function requiresConfirmation(bool $requires = true): static
    {
        $this->requiresConfirmation = $requires;

        return $this;
    }

    public function confirmationTitle(string $title): static
    {
        $this->confirmationTitle = $title;

        return $this;
    }

    public function confirmationText(string $text): static
    {
        $this->confirmationText = $text;

        return $this;
    }

    public function successNotification(string $message): static
    {
        $this->successNotification = $message;

        return $this;
    }

    public function call(?Model $record = null): mixed
    {
        if ($this->action) {
            return call_user_func($this->action, $record);
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label ?? ucfirst(str_replace('_', ' ', $this->name)),
            'icon' => $this->icon,
            'color' => $this->color,
            'requiresConfirmation' => $this->requiresConfirmation,
            'confirmationTitle' => $this->confirmationTitle,
            'confirmationText' => $this->confirmationText,
        ];
    }
}
