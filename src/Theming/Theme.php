<?php

namespace Rhaima\VoltPanel\Theming;

class Theme
{
    protected string $name;
    protected array $colors = [];
    protected ?string $font = null;
    protected array $customCss = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function primaryColor(string $color): static
    {
        $this->colors['primary'] = $color;

        return $this;
    }

    public function primaryForeground(string $color): static
    {
        $this->colors['primary-foreground'] = $color;

        return $this;
    }

    public function successColor(string $color): static
    {
        $this->colors['success'] = $color;

        return $this;
    }

    public function successForeground(string $color): static
    {
        $this->colors['success-foreground'] = $color;

        return $this;
    }

    public function dangerColor(string $color): static
    {
        $this->colors['danger'] = $color;

        return $this;
    }

    public function dangerForeground(string $color): static
    {
        $this->colors['danger-foreground'] = $color;

        return $this;
    }

    public function warningColor(string $color): static
    {
        $this->colors['warning'] = $color;

        return $this;
    }

    public function warningForeground(string $color): static
    {
        $this->colors['warning-foreground'] = $color;

        return $this;
    }

    public function infoColor(string $color): static
    {
        $this->colors['info'] = $color;

        return $this;
    }

    public function infoForeground(string $color): static
    {
        $this->colors['info-foreground'] = $color;

        return $this;
    }

    public function colors(array $colors): static
    {
        $this->colors = array_merge($this->colors, $colors);

        return $this;
    }

    public function font(string $font): static
    {
        $this->font = $font;

        return $this;
    }

    public function customCss(array $css): static
    {
        $this->customCss = $css;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColors(): array
    {
        return $this->colors;
    }

    public function getFont(): ?string
    {
        return $this->font;
    }

    public function getCustomCss(): array
    {
        return $this->customCss;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'colors' => $this->colors,
            'font' => $this->font,
            'customCss' => $this->customCss,
        ];
    }
}
