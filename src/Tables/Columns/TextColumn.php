<?php

namespace Rhaima\VoltPanel\Tables\Columns;

class TextColumn extends Column
{
    protected bool $copyable = false;
    protected ?int $limit = null;
    protected bool $wrap = false;

    public function copyable(bool $copyable = true): static
    {
        $this->copyable = $copyable;

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function wrap(bool $wrap = true): static
    {
        $this->wrap = $wrap;

        return $this;
    }

    public function getType(): string
    {
        return 'text';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'copyable' => $this->copyable,
            'limit' => $this->limit,
            'wrap' => $this->wrap,
        ]);
    }
}
