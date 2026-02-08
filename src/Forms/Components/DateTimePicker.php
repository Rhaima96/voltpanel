<?php

namespace Rhaima\VoltPanel\Forms\Components;

class DateTimePicker extends Component
{
    protected ?string $format = 'Y-m-d H:i';
    protected ?string $displayFormat = 'F j, Y g:i A';
    protected ?string $minDate = null;
    protected ?string $maxDate = null;
    protected int $step = 60; // Step in seconds (default: 1 minute)
    protected bool $seconds = false;

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

    public function step(int $seconds): static
    {
        $this->step = $seconds;

        return $this;
    }

    public function seconds(bool $seconds = true): static
    {
        $this->seconds = $seconds;
        if ($seconds) {
            $this->step = 1;
        }

        return $this;
    }

    public function getType(): string
    {
        return 'date-time-picker';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'format' => $this->format,
            'displayFormat' => $this->displayFormat,
            'minDate' => $this->minDate,
            'maxDate' => $this->maxDate,
            'step' => $this->step,
            'seconds' => $this->seconds,
        ]);
    }
}
