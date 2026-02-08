<?php

namespace Rhaima\VoltPanel\Notifications;

class Notification
{
    protected string $title;
    protected ?string $body = null;
    protected string $status = 'info';
    protected int $duration = 5000;
    protected ?string $icon = null;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public static function make(string $title): static
    {
        return new static($title);
    }

    public function body(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function success(): static
    {
        $this->status = 'success';

        return $this;
    }

    public function danger(): static
    {
        $this->status = 'danger';

        return $this;
    }

    public function warning(): static
    {
        $this->status = 'warning';

        return $this;
    }

    public function info(): static
    {
        $this->status = 'info';

        return $this;
    }

    public function duration(int $milliseconds): static
    {
        $this->duration = $milliseconds;

        return $this;
    }

    public function persistent(): static
    {
        $this->duration = 0;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function send(): void
    {
        session()->flash('voltpanel.notification', $this->toArray());
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'status' => $this->status,
            'duration' => $this->duration,
            'icon' => $this->icon,
        ];
    }
}
