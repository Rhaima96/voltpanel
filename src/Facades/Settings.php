<?php

namespace Rhaima\VoltPanel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(\Rhaima\VoltPanel\Settings\Setting $setting)
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void set(string $key, mixed $value)
 * @method static \Illuminate\Support\Collection all()
 * @method static \Illuminate\Support\Collection allByGroup()
 * @method static void forget(string $key)
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'voltpanel.settings';
    }
}
