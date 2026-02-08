<?php

use Illuminate\Support\Facades\Route;
use Rhaima\VoltPanel\Http\Controllers\ResourceController;
use Rhaima\VoltPanel\Http\Controllers\DashboardController;
use Rhaima\VoltPanel\Http\Controllers\GlobalSearchController;
use Rhaima\VoltPanel\Http\Controllers\SettingsController;
use Rhaima\VoltPanel\Http\Controllers\ExportController;
use Rhaima\VoltPanel\Http\Controllers\SavedFilterController;
use Rhaima\VoltPanel\Http\Controllers\TenantController;
use Rhaima\VoltPanel\Http\Controllers\TablePreferenceController;
use Rhaima\VoltPanel\Http\Controllers\CommentController;

Route::get('/', [DashboardController::class, 'index'])->name('voltpanel.dashboard');

Route::get('/global-search', GlobalSearchController::class)->name('voltpanel.global-search');

Route::prefix('resources/{resource}')->name('voltpanel.resources.')->group(function () {
    Route::get('/', [ResourceController::class, 'index'])->name('index');
    Route::get('/create', [ResourceController::class, 'create'])->name('create');
    Route::get('/export', [ExportController::class, 'export'])->name('export');
    Route::post('/bulk-action', [ResourceController::class, 'bulkAction'])->name('bulk-action');
    Route::post('/', [ResourceController::class, 'store'])->name('store');
    Route::get('/{record}', [ResourceController::class, 'view'])->name('view');
    Route::get('/{record}/edit', [ResourceController::class, 'edit'])->name('edit');
    Route::put('/{record}', [ResourceController::class, 'update'])->name('update');
    Route::delete('/{record}', [ResourceController::class, 'destroy'])->name('destroy');
});

Route::prefix('pages/{page}')->name('voltpanel.pages.')->group(function () {
    Route::get('/', [SettingsController::class, 'show'])->name('show');
    Route::post('/', [SettingsController::class, 'update'])->name('update');
});

// API Routes
Route::prefix('api')->name('voltpanel.api.')->group(function () {
    // Rich Editor File Upload
    Route::post('/rich-editor/upload', [ResourceController::class, 'uploadRichEditorFile'])->name('rich-editor.upload');

    // Saved Filters
    Route::prefix('saved-filters')->name('saved-filters.')->group(function () {
        Route::get('/', [SavedFilterController::class, 'index'])->name('index');
        Route::post('/', [SavedFilterController::class, 'store'])->name('store');
        Route::put('/{id}', [SavedFilterController::class, 'update'])->name('update');
        Route::delete('/{id}', [SavedFilterController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/make-default', [SavedFilterController::class, 'makeDefault'])->name('make-default');
    });

    // Tenants
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [TenantController::class, 'index'])->name('index');
        Route::get('/current', [TenantController::class, 'current'])->name('current');
        Route::post('/{tenantId}/switch', [TenantController::class, 'switch'])->name('switch');
    });

    // Table Preferences
    Route::prefix('table-preferences')->name('table-preferences.')->group(function () {
        Route::post('/', [TablePreferenceController::class, 'store'])->name('store');
        Route::get('/{tableIdentifier}', [TablePreferenceController::class, 'show'])->name('show');
        Route::delete('/{tableIdentifier}', [TablePreferenceController::class, 'destroy'])->name('destroy');
    });

    // Comments
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::post('/', [CommentController::class, 'store'])->name('store');
        Route::delete('/{id}', [CommentController::class, 'destroy'])->name('destroy');
    });
});
