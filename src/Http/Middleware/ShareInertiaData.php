<?php

namespace Rhaima\VoltPanel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Rhaima\VoltPanel\Facades\VoltPanel;

class ShareInertiaData
{
    public function handle(Request $request, Closure $next)
    {
        Inertia::share([
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                ] : null,
            ],
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                    'warning' => $request->session()->get('warning'),
                    'info' => $request->session()->get('info'),
                ];
            },
            'voltpanel' => function () {
                $panel = VoltPanel::getDefaultPanel();

                return [
                    'branding' => [
                        'name' => $panel?->getBrandName() ?? config('voltpanel.branding.name', config('app.name')),
                        'logo' => $panel?->getBrandLogo() ?? config('voltpanel.branding.logo'),
                    ],
                    'navigation' => $this->getNavigation($panel),
                    'theme' => $this->getTheme($panel),
                    'basePath' => '/'.ltrim(config('voltpanel.path', 'admin'), '/'),
                    'routingMode' => $this->detectRoutingMode(),
                    'multiTenancyEnabled' => config('voltpanel.multi_tenancy.enabled', false),
                    'currentTenant' => $this->getCurrentTenant(),
                ];
            },
        ]);

        return $next($request);
    }

    protected function getNavigation($panel): array
    {
        if (! $panel) {
            return [];
        }

        $navigation = [];

        // Add Dashboard link
        $navigation[] = [
            'label' => 'Dashboard',
            'icon' => 'heroicon-o-home',
            'url' => route('voltpanel.dashboard'),
            'active' => request()->is(config('voltpanel.path', 'admin')) || request()->is(config('voltpanel.path', 'admin').'/'),
            'group' => '',
            'parent' => null,
            'sort' => -1000, // Ensure Dashboard is always first
        ];

        // Get resources from panel
        foreach ($panel->getResources() as $resource) {
            // Skip resources that should not be registered in navigation
            if (! $resource::shouldRegisterNavigation()) {
                continue;
            }

            $slug = $resource::getSlug();

            $navigation[] = [
                'label' => $resource::getNavigationLabel(),
                'icon' => $resource::getNavigationIcon() ?? 'heroicon-o-rectangle-stack',
                'url' => route('voltpanel.resources.index', ['resource' => $slug]),
                'active' => request()->is('*'.$slug.'*'),
                'group' => $resource::getNavigationGroup() ?? '',
                'parent' => $resource::getNavigationParentItem() ?? null,
                'sort' => $resource::getNavigationSort() ?? 0,
            ];
        }

        // Get pages from panel
        foreach ($panel->getPages() as $page) {
            if (! $page::canAccess()) {
                continue;
            }

            $pageClass = str_replace('\\', '_', $page);

            $navigation[] = [
                'label' => $page::getNavigationLabel(),
                'icon' => $page::getNavigationIcon() ?? 'heroicon-o-document-text',
                'url' => route('voltpanel.pages.show', ['page' => $pageClass]),
                'active' => request()->is('*pages/'.$pageClass.'*'),
                'group' => $page::getNavigationGroup() ?? '',
                'parent' => null,
                'sort' => $page::getNavigationSort() ?? 0,
            ];
        }

        // Resolve parent class names to labels
        foreach ($navigation as &$item) {
            if ($item['parent'] && class_exists($item['parent'])) {
                // Resolve class name to navigation label
                $item['parent'] = $item['parent']::getNavigationLabel();
            }
        }

        // Sort by sort order
        usort($navigation, fn ($a, $b) => $a['sort'] <=> $b['sort']);

        return $navigation;
    }

    protected function getTheme($panel): array
    {
        $theme = $panel?->getTheme();

        if ($theme) {
            return $theme->toArray();
        }

        // Return default theme from config
        return [
            'name' => 'default',
            'colors' => [
                'primary' => $panel?->getPrimaryColor() ?? config('voltpanel.theme.primary_color', '#6366f1'),
            ],
            'font' => null,
            'customCss' => [],
        ];
    }

    protected function getCurrentTenant(): ?array
    {
        if (! config('voltpanel.multi_tenancy.enabled', false)) {
            return null;
        }

        if (! app()->bound('voltpanel.tenancy')) {
            return null;
        }

        $tenant = app('voltpanel.tenancy')->getCurrentTenant();

        if (! $tenant) {
            return null;
        }

        return [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'subdomain' => $tenant->subdomain,
            'domain' => $tenant->domain,
            'is_active' => $tenant->is_active,
        ];
    }

    /**
     * Detect which frontend routing system is being used.
     * Ziggy is used by Breeze, Wayfinder by Laravel Vue starter kit.
     */
    protected function detectRoutingMode(): string
    {
        $packageJsonPath = base_path('package.json');

        if (! file_exists($packageJsonPath)) {
            return 'ziggy';
        }

        $packageJson = json_decode(file_get_contents($packageJsonPath), true);
        $dependencies = array_merge(
            $packageJson['dependencies'] ?? [],
            $packageJson['devDependencies'] ?? []
        );

        // Check for Wayfinder
        if (isset($dependencies['@laravel/wayfinder']) || isset($dependencies['laravel-wayfinder'])) {
            return 'wayfinder';
        }

        // Check for Ziggy
        if (isset($dependencies['ziggy-js'])) {
            return 'ziggy';
        }

        // Default to ziggy (more common)
        return 'ziggy';
    }
}
