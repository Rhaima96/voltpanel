<?php

namespace Rhaima\VoltPanel\Tables\Columns;

class BooleanColumn extends Column
{
    protected ?string $trueLabel = 'Yes';
    protected ?string $falseLabel = 'No';
    protected ?string $trueIcon = null;
    protected ?string $falseIcon = null;
    protected ?string $trueColor = 'success';
    protected ?string $falseColor = 'danger';

    public function trueLabel(string $label): static
    {
        $this->trueLabel = $label;

        return $this;
    }

    public function falseLabel(string $label): static
    {
        $this->falseLabel = $label;

        return $this;
    }

    public function trueIcon(string $icon): static
    {
        $this->trueIcon = $icon;

        return $this;
    }

    public function falseIcon(string $icon): static
    {
        $this->falseIcon = $icon;

        return $this;
    }

    public function trueColor(string $color): static
    {
        $this->trueColor = $color;

        return $this;
    }

    public function falseColor(string $color): static
    {
        $this->falseColor = $color;

        return $this;
    }

    public function getType(): string
    {
        return 'boolean';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'trueLabel' => $this->trueLabel,
            'falseLabel' => $this->falseLabel,
            'trueIcon' => $this->trueIcon,
            'falseIcon' => $this->falseIcon,
            'trueColor' => $this->trueColor,
            'falseColor' => $this->falseColor,
        ]);
    }
}
