<?php

namespace Rhaima\VoltPanel\Forms\Components;

class DatePicker extends Component
{
    protected ?string $format = 'Y-m-d';
    protected ?string $displayFormat = 'F j, Y';
    protected bool $withTime = false;
    protected ?string $minDate = null;
    protected ?string $maxDate = null;

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function displayFormat(string $format): static
    {
        $this->displayFormat = $format;

        return $this;
    }

    public function withTime(bool $withTime = true): static
    {
        $this->withTime = $withTime;

        return $this;
    }

    public function minDate(string $date): static
    {
        $this->minDate = $date;

        return $this;
    }

    public function maxDate(string $date): static
    {
        $this->maxDate = $date;

        return $this;
    }

    public function getType(): string
    {
        return 'date-picker';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'format' => $this->format,
            'displayFormat' => $this->displayFormat,
            'withTime' => $this->withTime,
            'minDate' => $this->minDate,
            'maxDate' => $this->maxDate,
        ]);
    }
}
