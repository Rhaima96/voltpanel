<?php

namespace Rhaima\VoltPanel\Settings;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class Setting
{
    protected string $key;
    protected mixed $default = null;
    protected ?string $group = null;
    protected ?string $label = null;
    protected ?string $description = null;
    protected ?string $type = 'text';
    protected bool $encrypted = false;
    protected bool $cached = true;
    protected ?int $cacheTtl = 3600; // 1 hour

    public static function make(string $key): static
    {
        return new static($key);
    }

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function default(mixed $value): static
    {
        $this->default = $value;
        return $this;
    }

    public function group(string $group): static
    {
        $this->group = $group;
        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function encrypted(bool $encrypted = true): static
    {
        $this->encrypted = $encrypted;
        return $this;
    }

    public function cached(bool $cached = true): static
    {
        $this->cached = $cached;
        return $this;
    }

    public function cacheTtl(int $seconds): static
    {
        $this->cacheTtl = $seconds;
        return $this;
    }

    public function get(): mixed
    {
        if ($this->cached) {
            return Cache::remember(
                $this->getCacheKey(),
                $this->cacheTtl,
                fn () => $this->getValue()
            );
        }

        return $this->getValue();
    }

    public function set(mixed $value): void
    {
        $model = app(config('voltpanel.settings.model'));

        $setting = $model::firstOrNew(['key' => $this->key]);
        $setting->value = $this->encrypted ? encrypt($value) : $value;
        $setting->group = $this->group;
        $setting->save();

        if ($this->cached) {
            Cache::forget($this->getCacheKey());
        }
    }

    public function forget(): void
    {
        $model = app(config('voltpanel.settings.model'));
        $model::where('key', $this->key)->delete();

        if ($this->cached) {
            Cache::forget($this->getCacheKey());
        }
    }

    protected function getValue(): mixed
    {
        $model = app(config('voltpanel.settings.model'));
        $setting = $model::where('key', $this->key)->first();

        if (!$setting) {
            return $this->default;
        }

        $value = $setting->value;

        return $this->encrypted ? decrypt($value) : $value;
    }

    protected function getCacheKey(): string
    {
        return "voltpanel.settings.{$this->key}";
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->get(),
            'default' => $this->default,
            'group' => $this->group,
            'label' => $this->label,
            'description' => $this->description,
            'type' => $this->type,
            'encrypted' => $this->encrypted,
        ];
    }
}
