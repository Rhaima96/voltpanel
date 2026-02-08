<?php

namespace Rhaima\VoltPanel\Tables\Columns;

class DateColumn extends Column
{
    protected string $format = 'M j, Y';
    protected bool $date = true;
    protected bool $time = false;
    protected bool $relative = false;

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function date(bool $date = true): static
    {
        $this->date = $date;

        return $this;
    }

    public function time(bool $time = true): static
    {
        $this->time = $time;

        return $this;
    }

    public function dateTime(): static
    {
        return $this->date()->time();
    }

    public function relative(bool $relative = true): static
    {
        $this->relative = $relative;

        return $this;
    }

    public function getType(): string
    {
        return 'date';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'format' => $this->format,
            'date' => $this->date,
            'time' => $this->time,
            'relative' => $this->relative,
        ]);
    }
}
