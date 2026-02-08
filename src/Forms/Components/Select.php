<?php

namespace Rhaima\VoltPanel\Forms\Components;

use Closure;

class Select extends Component
{
    protected array|Closure $options = [];
    protected bool $searchable = false;
    protected bool $multiple = false;
    protected ?string $relationship = null;
    protected ?string $titleAttribute = null;
    protected ?Closure $getOptionLabelUsing = null;

    public function options(array|Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function relationship(string $relationship, string $titleAttribute = 'name', ?Closure $modifyQueryUsing = null): static
    {
        $this->relationship = $relationship;
        $this->titleAttribute = $titleAttribute;

        return $this;
    }

    public function getOptionLabelUsing(?Closure $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function getType(): string
    {
        return 'select';
    }

    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->titleAttribute;
    }

    public function toArray(): array
    {
        $options = is_callable($this->options) ? call_user_func($this->options) : $this->options;

        // If relationship is set, load relationship options
        if ($this->relationship && $this->form) {
            $modelClass = $this->form->getModelClass();
            $model = $this->form->getModel();

            // Try to get the relationship model from either the instance or the class
            if ($modelClass) {
                // Create a temporary instance to access the relationship
                $tempInstance = $model ?? new $modelClass;

                if (method_exists($tempInstance, $this->relationship)) {
                    $relationshipModel = $tempInstance->{$this->relationship}()->getRelated();
                    $records = $relationshipModel::all();

                    $options = $records->mapWithKeys(function ($record) {
                        $label = $this->getOptionLabelUsing
                            ? call_user_func($this->getOptionLabelUsing, $record)
                            : $record->{$this->titleAttribute};

                        return [$record->getKey() => $label];
                    })->toArray();
                }
            }
        }

        return array_merge(parent::toArray(), [
            'options' => $options,
            'searchable' => $this->searchable,
            'multiple' => $this->multiple,
            'relationship' => $this->relationship,
        ]);
    }
}
