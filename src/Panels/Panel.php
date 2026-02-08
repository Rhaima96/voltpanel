<?php

namespace Rhaima\VoltPanel\Panels;

use Closure;
use Illuminate\Support\Collection;
use Rhaima\VoltPanel\Navigation\NavigationItem;
use Rhaima\VoltPanel\Pages\Page;
use Rhaima\VoltPanel\Resources\Resource;
use Rhaima\VoltPanel\Theming\Theme;
use Rhaima\VoltPanel\Widgets\Widget;

class Panel
{
    protected string $id;
    protected ?string $path = null;
    protected ?string $name = null;
    protected Collection $resources;
    protected Collection $pages;
    protected Collection $widgets;
    protected Collection $navigationItems;
    protected array $middleware = [];
    protected ?string $homeUrl = null;
    protected ?string $brandName = null;
    protected ?string $brandLogo = null;
    protected bool $darkMode = true;
    protected ?string $primaryColor = null;
    protected ?Theme $theme = null;
    protected ?Closure $authGuard = null;

    public function __construct(string $id = 'default')
    {
        $this->id = $id;
        $this->resources = new Collection();
        $this->pages = new Collection();
        $this->widgets = new Collection();
        $this->navigationItems = new Collection();
    }

    public static function make(string $id = 'default'): static
    {
        return new static($id);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path ?? config('voltpanel.path', 'admin');
    }

    public function brandName(string $name): static
    {
        $this->brandName = $name;

        return $this;
    }

    public function getBrandName(): string
    {
        return $this->brandName ?? config('voltpanel.branding.name', config('app.name'));
    }

    public function brandLogo(?string $logo): static
    {
        $this->brandLogo = $logo;

        return $this;
    }

    public function getBrandLogo(): ?string
    {
        return $this->brandLogo ?? config('voltpanel.branding.logo');
    }

    public function darkMode(bool $enabled = true): static
    {
        $this->darkMode = $enabled;

        return $this;
    }

    public function hasDarkMode(): bool
    {
        return $this->darkMode;
    }

    public function primaryColor(string $color): static
    {
        $this->primaryColor = $color;

        return $this;
    }

    public function getPrimaryColor(): string
    {
        return $this->primaryColor ?? config('voltpanel.theme.primary_color', '#6366f1');
    }

    public function theme(Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function resources(array $resources): static
    {
        foreach ($resources as $resource) {
            $this->resource($resource);
        }

        return $this;
    }

    public function resource(string $resource): static
    {
        $this->resources->push($resource);

        return $this;
    }

    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function pages(array $pages): static
    {
        foreach ($pages as $page) {
            $this->page($page);
        }

        return $this;
    }

    public function page(string $page): static
    {
        $this->pages->push($page);

        return $this;
    }

    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function widgets(array $widgets): static
    {
        foreach ($widgets as $widget) {
            $this->widget($widget);
        }

        return $this;
    }

    public function widget(string $widget): static
    {
        $this->widgets->push($widget);

        return $this;
    }

    public function getWidgets(): Collection
    {
        return $this->widgets->sortBy(function ($widget) {
            return $widget::getSort();
        })->values();
    }

    public function middleware(array $middleware): static
    {
        $this->middleware = $middleware;

        return $this;
    }

    public function getMiddleware(): array
    {
        return $this->middleware ?: config('voltpanel.middleware', ['web', 'auth']);
    }

    public function authGuard(Closure $callback): static
    {
        $this->authGuard = $callback;

        return $this;
    }

    public function homeUrl(?string $url): static
    {
        $this->homeUrl = $url;

        return $this;
    }

    public function getHomeUrl(): string
    {
        return $this->homeUrl ?? url($this->getPath());
    }

    public function navigation(Closure $callback): static
    {
        $callback($this);

        return $this;
    }

    public function getNavigationItems(): Collection
    {
        // Collect navigation from resources and pages
        $items = new Collection();

        foreach ($this->resources as $resource) {
            if (method_exists($resource, 'getNavigationItems')) {
                $items->push(...$resource::getNavigationItems());
            }
        }

        foreach ($this->pages as $page) {
            if (method_exists($page, 'getNavigationItems')) {
                $items->push(...$page::getNavigationItems());
            }
        }

        return $items->merge($this->navigationItems)->sortBy('sort');
    }
}
