<?php

namespace Rhaima\VoltPanel\Plugins;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class PluginManager
{
    protected Collection $plugins;
    protected string $pluginsPath;

    public function __construct()
    {
        $this->plugins = collect();
        $this->pluginsPath = config('voltpanel.plugins.path', base_path('voltpanel-plugins'));
    }

    public function register(Plugin $plugin): void
    {
        $this->plugins->put($plugin->getName(), $plugin);
    }

    public function boot(): void
    {
        foreach ($this->plugins as $plugin) {
            if ($plugin->isEnabled()) {
                $plugin->boot();
            }
        }
    }

    public function getPlugins(): Collection
    {
        return $this->plugins;
    }

    public function getPlugin(string $name): ?Plugin
    {
        return $this->plugins->get($name);
    }

    public function discoverPlugins(): void
    {
        if (!File::exists($this->pluginsPath)) {
            return;
        }

        $directories = File::directories($this->pluginsPath);

        foreach ($directories as $directory) {
            $pluginFile = $directory . '/Plugin.php';

            if (File::exists($pluginFile)) {
                require_once $pluginFile;
            }
        }
    }
}
