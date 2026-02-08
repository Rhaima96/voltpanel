<?php

namespace Rhaima\VoltPanel\Dashboards;

use Illuminate\Support\Collection;
use Rhaima\VoltPanel\Models\DashboardLayout;

class DashboardManager
{
    public function getLayout(?int $userId = null): ?DashboardLayout
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return null;
        }

        return DashboardLayout::where('user_id', $userId)->first();
    }

    public function saveLayout(array $widgets, ?int $userId = null): DashboardLayout
    {
        $userId = $userId ?? auth()->id();

        return DashboardLayout::updateOrCreate(
            ['user_id' => $userId],
            ['widgets' => $widgets]
        );
    }

    public function resetLayout(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        return DashboardLayout::where('user_id', $userId)->delete() > 0;
    }

    public function getAvailableWidgets(): Collection
    {
        return collect(config('voltpanel.dashboard.available_widgets', []));
    }
}
