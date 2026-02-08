<?php

namespace Rhaima\VoltPanel\Keyboard;

use Illuminate\Support\Collection;

class KeyboardShortcutManager
{
    protected Collection $shortcuts;

    public function __construct()
    {
        $this->shortcuts = collect();
    }

    public function register(string $key, string $description, callable $action): void
    {
        $this->shortcuts->put($key, [
            'key' => $key,
            'description' => $description,
            'action' => $action,
        ]);
    }

    public function getShortcuts(): Collection
    {
        return $this->shortcuts;
    }

    public function getShortcutsForFrontend(): array
    {
        return $this->shortcuts->map(function ($shortcut) {
            return [
                'key' => $shortcut['key'],
                'description' => $shortcut['description'],
            ];
        })->values()->toArray();
    }
}
