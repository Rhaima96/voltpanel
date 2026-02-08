<?php

namespace Rhaima\VoltPanel\Forms\Components;

use Closure;

class Radio extends Component
{
    protected array | Closure $options = [];
    protected bool $inline = false;

    public function options(array | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function inline(bool $inline = true): static
    {
        $this->inline = $inline;

        return $this;
    }

    public function getType(): string
    {
        return 'radio';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => is_callable($this->options) ? [] : $this->options,
            'inline' => $this->inline,
        ]);
    }
}
