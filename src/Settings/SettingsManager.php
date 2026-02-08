<?php

namespace Rhaima\VoltPanel\Settings;

use Illuminate\Support\Collection;

class SettingsManager
{
    protected Collection $settings;

    public function __construct()
    {
        $this->settings = new Collection();
    }

    public function register(Setting $setting): void
    {
        $this->settings->put($setting->toArray()['key'], $setting);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->settings->has($key)) {
            return $default;
        }

        return $this->settings->get($key)->get();
    }

    public function set(string $key, mixed $value): void
    {
        if (!$this->settings->has($key)) {
            $setting = Setting::make($key);
            $this->register($setting);
        }

        $this->settings->get($key)->set($value);
    }

    public function all(): Collection
    {
        return $this->settings;
    }

    public function allByGroup(): Collection
    {
        return $this->settings->groupBy(fn ($setting) => $setting->toArray()['group'] ?? 'general');
    }

    public function forget(string $key): void
    {
        if ($this->settings->has($key)) {
            $this->settings->get($key)->forget();
            $this->settings->forget($key);
        }
    }
}
