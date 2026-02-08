<?php

namespace Rhaima\VoltPanel\Actions;

class ImportAction extends Action
{
    protected array $acceptedFileTypes = ['.csv', '.xlsx'];
    protected bool $updateExisting = false;
    protected ?string $uniqueBy = null;

    public function __construct()
    {
        parent::__construct('import');

        $this->label('Import')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary');
    }

    public function acceptedFileTypes(array $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    public function updateExisting(bool $update = true, ?string $uniqueBy = null): static
    {
        $this->updateExisting = $update;
        $this->uniqueBy = $uniqueBy;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'acceptedFileTypes' => $this->acceptedFileTypes,
            'updateExisting' => $this->updateExisting,
            'uniqueBy' => $this->uniqueBy,
        ]);
    }
}
