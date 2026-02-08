<?php

namespace Rhaima\VoltPanel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Rhaima\VoltPanel\Panels\Panel panel(string $id)
 * @method static \Rhaima\VoltPanel\VoltPanelManager register(\Rhaima\VoltPanel\Panels\Panel $panel)
 * @method static \Rhaima\VoltPanel\Panels\Panel|null getPanel(string $id)
 * @method static \Illuminate\Support\Collection getPanels()
 * @method static \Rhaima\VoltPanel\Panels\Panel|null getDefaultPanel()
 *
 * @see \Rhaima\VoltPanel\VoltPanelManager
 */
class VoltPanel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'voltpanel';
    }
}
