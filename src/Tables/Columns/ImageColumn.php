<?php

namespace Rhaima\VoltPanel\Tables\Columns;

class ImageColumn extends Column
{
    protected bool $circular = false;
    protected ?int $height = 40;
    protected ?int $width = 40;

    public function circular(bool $circular = true): static
    {
        $this->circular = $circular;

        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getType(): string
    {
        return 'image';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'circular' => $this->circular,
            'height' => $this->height,
            'width' => $this->width,
        ]);
    }
}
