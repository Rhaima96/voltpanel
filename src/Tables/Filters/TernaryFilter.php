<?php

namespace Rhaima\VoltPanel\Tables\Filters;

use Illuminate\Database\Eloquent\Builder;

class TernaryFilter extends Filter
{
    protected ?string $trueLabel = 'Yes';
    protected ?string $falseLabel = 'No';
    protected ?string $nullLabel = 'All';

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

    public function nullLabel(string $label): static
    {
        $this->nullLabel = $label;

        return $this;
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        // If custom query callback is defined, use it
        if ($this->query) {
            call_user_func($this->query, $query, $value);
        } else {
            // Default behavior: convert to boolean and filter
            $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            $query->where($this->name, $boolValue);
        }

        return $query;
    }

    public function getType(): string
    {
        return 'ternary';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'trueLabel' => $this->trueLabel,
            'falseLabel' => $this->falseLabel,
            'nullLabel' => $this->nullLabel,
        ]);
    }
}
