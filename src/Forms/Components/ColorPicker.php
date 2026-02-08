<?php

namespace Rhaima\VoltPanel\Forms\Components;

class ColorPicker extends Component
{
    protected bool $swatches = true;
    protected array $swatchColors = [
        '#ef4444', '#f97316', '#f59e0b', '#eab308',
        '#84cc16', '#22c55e', '#10b981', '#14b8a6',
        '#06b6d4', '#0ea5e9', '#3b82f6', '#6366f1',
        '#8b5cf6', '#a855f7', '#d946ef', '#ec4899',
    ];

    public function swatches(bool $show = true): static
    {
        $this->swatches = $show;

        return $this;
    }

    public function swatchColors(array $colors): static
    {
        $this->swatchColors = $colors;

        return $this;
    }

    public function getType(): string
    {
        return 'color-picker';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'swatches' => $this->swatches,
            'swatchColors' => $this->swatchColors,
        ]);
    }
}
