<?php

namespace Rhaima\VoltPanel\Tables\Columns;

use Closure;

class BadgeColumn extends Column
{
    protected array|Closure $colors = [];

    public function colors(array|Closure $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function getType(): string
    {
        return 'badge';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'colors' => is_callable($this->colors) ? [] : $this->colors,
        ]);
    }
}
