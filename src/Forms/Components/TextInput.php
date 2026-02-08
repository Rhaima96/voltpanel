<?php

namespace Rhaima\VoltPanel\Forms\Components;

class TextInput extends Component
{
    protected ?string $type = 'text';
    protected ?int $maxLength = null;
    protected ?int $minLength = null;

    public function email(): static
    {
        $this->type = 'email';

        return $this;
    }

    public function password(): static
    {
        $this->type = 'password';

        return $this;
    }

    public function tel(): static
    {
        $this->type = 'tel';

        return $this;
    }

    public function url(): static
    {
        $this->type = 'url';

        return $this;
    }

    public function color(): static
    {
        $this->type = 'color';

        return $this;
    }

    public function number(): static
    {
        $this->type = 'number';

        return $this;
    }

    public function date(): static
    {
        $this->type = 'date';

        return $this;
    }

    public function time(): static
    {
        $this->type = 'time';

        return $this;
    }

    public function datetime(): static
    {
        $this->type = 'datetime-local';

        return $this;
    }

    public function maxLength(int $length): static
    {
        $this->maxLength = $length;

        return $this;
    }

    public function minLength(int $length): static
    {
        $this->minLength = $length;

        return $this;
    }

    public function getType(): string
    {
        return 'text-input';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'inputType' => $this->type,
            'maxLength' => $this->maxLength,
            'minLength' => $this->minLength,
        ]);
    }
}
