<?php

namespace Rhaima\VoltPanel\Tables\Columns;

use Closure;

class IconColumn extends Column
{
    protected array | Closure $icons = [];
    protected ?string $size = 'md';
    protected array | Closure $colors = [];

    public function icons(array | Closure $icons): static
    {
        $this->icons = $icons;

        return $this;
    }

    public function size(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function colors(array | Closure $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function getType(): string
    {
        return 'icon';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'icons' => is_callable($this->icons) ? [] : $this->icons,
            'size' => $this->size,
            'colors' => is_callable($this->colors) ? [] : $this->colors,
        ]);
    }
}
