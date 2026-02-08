<?php

namespace Rhaima\VoltPanel\Tables\Filters;

class SelectFilter extends Filter
{
    protected array $options = [];

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getType(): string
    {
        return 'select';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => $this->options,
        ]);
    }
}
