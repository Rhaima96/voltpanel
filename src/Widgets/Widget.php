<?php

namespace Rhaima\VoltPanel\Widgets;

use Closure;

abstract class Widget
{
    protected static ?int $sort = null;
    protected static ?string $view = null;
    protected int | Closure | null $columnSpan = 1;

    public static function getSort(): int
    {
        return static::$sort ?? 0;
    }

    public static function getView(): string
    {
        return static::$view ?? 'voltpanel::widgets.default';
    }

    public function columnSpan(int | Closure $span): static
    {
        $this->columnSpan = $span;

        return $this;
    }

    public function getColumnSpan(): int
    {
        return $this->columnSpan instanceof Closure
            ? call_user_func($this->columnSpan)
            : $this->columnSpan;
    }

    abstract public function getData(): array;

    public function toArray(): array
    {
        return [
            'type' => class_basename(static::class),
            'data' => $this->getData(),
            'columnSpan' => $this->getColumnSpan(),
        ];
    }
}
