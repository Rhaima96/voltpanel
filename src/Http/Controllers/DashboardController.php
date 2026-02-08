<?php

namespace Rhaima\VoltPanel\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Rhaima\VoltPanel\Facades\VoltPanel;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $panel = VoltPanel::getDefaultPanel();

        $widgets = [];

        if ($panel) {
            foreach ($panel->getWidgets() as $widgetClass) {
                $widget = $widgetClass::make();

                // Get the parent class to determine widget type
                $parentClass = get_parent_class($widgetClass);
                $widgetType = $parentClass ? class_basename($parentClass) : class_basename($widgetClass);

                $widgets[] = [
                    'type' => $widgetType,
                    'data' => $widget->getData(),
                    'columnSpan' => $widget->getColumnSpan(),
                ];
            }
        }

        return Inertia::render('VoltPanel/Dashboard', [
            'title' => 'Dashboard',
            'widgets' => $widgets,
        ]);
    }
}
