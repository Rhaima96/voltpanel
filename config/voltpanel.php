<?php

return [

    /*
    |--------------------------------------------------------------------------
    | VoltPanel Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where VoltPanel will be accessible from. Feel free
    | to change this path to anything you like.
    |
    */

    'path' => env('VOLTPANEL_PATH', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | VoltPanel Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be assigned to every VoltPanel route, giving you
    | the chance to add your own middleware to this list or change any of
    | the existing middleware.
    |
    */

    'middleware' => [
        'web',
        'auth',
    ],

    /*
    |--------------------------------------------------------------------------
    | Auth Guard
    |--------------------------------------------------------------------------
    |
    | This configuration option defines the authentication guard that will
    | be used to protect your admin panel routes.
    |
    */

    'auth' => [
        'guard' => env('VOLTPANEL_AUTH_GUARD', 'web'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | Configure the branding for your admin panel.
    |
    */

    'branding' => [
        'name' => env('APP_NAME', 'VoltPanel'),
        'logo' => null,
        'favicon' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | Configure the default theme colors and styling.
    |
    */

    'theme' => [
        'primary_color' => '#6366f1',
        'dark_mode' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | Configure navigation settings.
    |
    */

    'navigation' => [
        'grouped' => true,
        'collapsible' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Search
    |--------------------------------------------------------------------------
    |
    | Enable or disable global search functionality.
    |
    */

    'global_search' => [
        'enabled' => true,
        'keybinding' => ['ctrl', 'k'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Configure notification settings.
    |
    */

    'notifications' => [
        'enabled' => true,
        'duration' => 5000, // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | Configure database settings for VoltPanel.
    |
    */

    'database' => [
        'table_prefix' => 'voltpanel_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | Configure role-based access control settings.
    |
    */

    'authorization' => [
        'enabled' => true,
        'role_model' => \Rhaima\VoltPanel\Models\Role::class,
        'role_user_table' => 'role_user',
        'super_admin_role' => 'super_admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    |
    | Configure activity logging settings.
    |
    */

    'activity_log' => [
        'enabled' => true,
        'model' => \Rhaima\VoltPanel\Models\Activity::class,
        'table_name' => 'activity_log',
        'log_name' => 'voltpanel',
        'clean_old_logs_after_days' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Import/Export
    |--------------------------------------------------------------------------
    |
    | Configure import and export settings.
    |
    */

    'import_export' => [
        'enabled' => true,
        'chunk_size' => 1000,
        'queue_exports' => false,
        'queue_imports' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Configure system settings and preferences.
    |
    */

    'settings' => [
        'enabled' => true,
        'model' => \Rhaima\VoltPanel\Models\SystemSetting::class,
        'table_name' => 'settings',
        'cache_enabled' => true,
        'cache_ttl' => 3600, // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Preferences
    |--------------------------------------------------------------------------
    |
    | Configure user table preferences for column visibility and ordering.
    |
    */

    'table_preferences' => [
        'enabled' => true,
        'table_name' => 'user_table_preferences',
        'persist_to_database' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Saved Filters
    |--------------------------------------------------------------------------
    |
    | Configure saved filter presets for resources.
    |
    */

    'saved_filters' => [
        'enabled' => true,
        'table_name' => 'saved_filters',
        'allow_public_filters' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Tenancy
    |--------------------------------------------------------------------------
    |
    | Configure multi-tenancy settings for isolating data by tenant.
    |
    */

    'multi_tenancy' => [
        'enabled' => false,
        'tenants_table' => 'tenants',
        'tenant_user_table' => 'tenant_user',
        'identify_by_subdomain' => false,
        'identify_by_domain' => false,
        'tenant_column' => 'tenant_id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Library
    |--------------------------------------------------------------------------
    */

    'media' => [
        'enabled' => true,
        'table_name' => 'media',
        'disk' => 'public',
        'path' => 'media',
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    */

    'localization' => [
        'enabled' => true,
        'default_locale' => 'en',
        'supported_locales' => ['en', 'fr', 'es', 'de'],
        'path' => resource_path('lang/vendor/voltpanel'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    'dashboard' => [
        'customizable' => true,
        'layouts_table' => 'dashboard_layouts',
        'available_widgets' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins
    |--------------------------------------------------------------------------
    */

    'plugins' => [
        'enabled' => true,
        'path' => base_path('voltpanel-plugins'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks
    |--------------------------------------------------------------------------
    */

    'webhooks' => [
        'enabled' => true,
        'table_name' => 'webhooks',
    ],

    /*
    |--------------------------------------------------------------------------
    | Comments
    |--------------------------------------------------------------------------
    */

    'comments' => [
        'enabled' => true,
        'table_name' => 'comments',
        'mentions_enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tags
    |--------------------------------------------------------------------------
    */

    'tags' => [
        'enabled' => true,
        'table_name' => 'tags',
    ],

    /*
    |--------------------------------------------------------------------------
    | Favorites
    |--------------------------------------------------------------------------
    */

    'favorites' => [
        'enabled' => true,
        'table_name' => 'favorites',
    ],

    /*
    |--------------------------------------------------------------------------
    | Scheduling
    |--------------------------------------------------------------------------
    */

    'scheduling' => [
        'enabled' => true,
        'table_name' => 'scheduled_exports',
    ],

];
