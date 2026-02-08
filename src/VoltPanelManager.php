<?php

namespace Rhaima\VoltPanel;

use Illuminate\Support\Collection;
use Rhaima\VoltPanel\Panels\Panel;

class VoltPanelManager
{
    protected Collection $panels;

    public function __construct()
    {
        $this->panels = new Collection();
    }

    public function panel(string $id): Panel
    {
        if (! $this->panels->has($id)) {
            $this->panels->put($id, new Panel($id));
        }

        return $this->panels->get($id);
    }

    public function register(Panel $panel): static
    {
        $this->panels->put($panel->getId(), $panel);

        return $this;
    }

    public function getPanel(string $id): ?Panel
    {
        return $this->panels->get($id);
    }

    public function getPanels(): Collection
    {
        return $this->panels;
    }

    public function getDefaultPanel(): ?Panel
    {
        return $this->panels->first();
    }
}
