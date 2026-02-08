<?php

namespace Rhaima\VoltPanel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function show(string $page): Response
    {
        // Convert URL parameter back to class name
        $pageClass = str_replace('_', '\\', $page);

        if (!class_exists($pageClass) || !is_subclass_of($pageClass, \Rhaima\VoltPanel\Pages\Page::class)) {
            abort(404);
        }

        // Check if it's a settings page
        if (is_subclass_of($pageClass, \Rhaima\VoltPanel\Pages\SettingsPage::class)) {
            return $this->showSettingsPage($pageClass);
        }

        // Handle regular pages
        abort(404, 'Regular pages not yet implemented');
    }

    protected function showSettingsPage(string $pageClass): Response
    {
        $page = new $pageClass();
        $settings = $pageClass::getSettings();

        $settingsArray = $settings->map(fn ($setting) => $setting->toArray())->toArray();
        $values = $settings->mapWithKeys(fn ($setting) => [$setting->toArray()['key'] => $setting->get()])->toArray();

        return Inertia::render('VoltPanel/Settings', [
            'title' => $pageClass::getNavigationLabel(),
            'description' => $pageClass::getNavigationDescription(),
            'settings' => $settingsArray,
            'values' => $values,
        ]);
    }

    public function update(Request $request, string $page)
    {
        // Convert URL parameter back to class name
        $pageClass = str_replace('_', '\\', $page);

        if (!class_exists($pageClass) || !is_subclass_of($pageClass, \Rhaima\VoltPanel\Pages\SettingsPage::class)) {
            abort(404);
        }

        $pageInstance = new $pageClass();
        $pageInstance->save($request->all());

        return redirect()->back()->with('success', 'Settings saved successfully!');
    }
}
