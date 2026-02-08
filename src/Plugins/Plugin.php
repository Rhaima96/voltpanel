<?php

namespace Rhaima\VoltPanel\Plugins;

abstract class Plugin
{
    protected bool $enabled = true;

    abstract public function getName(): string;

    abstract public function getVersion(): string;

    abstract public function getDescription(): string;

    public function boot(): void
    {
        // Override in plugin implementation
    }

    public function register(): void
    {
        // Override in plugin implementation
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }
}
