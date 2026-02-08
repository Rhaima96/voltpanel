<?php

namespace Rhaima\VoltPanel\Forms\Components;

class Textarea extends Component
{
    protected ?int $rows = 4;
    protected ?int $maxLength = null;
    protected bool $autosize = false;

    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function maxLength(int $length): static
    {
        $this->maxLength = $length;

        return $this;
    }

    public function autosize(bool $autosize = true): static
    {
        $this->autosize = $autosize;

        return $this;
    }

    public function getType(): string
    {
        return 'textarea';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'rows' => $this->rows,
            'maxLength' => $this->maxLength,
            'autosize' => $this->autosize,
        ]);
    }
}
