<?php

namespace Rhaima\VoltPanel\Forms\Components;

class Checkbox extends Component
{
    protected bool $inline = false;

    public function inline(bool $inline = true): static
    {
        $this->inline = $inline;

        return $this;
    }

    public function getType(): string
    {
        return 'checkbox';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'inline' => $this->inline,
        ]);
    }
}
