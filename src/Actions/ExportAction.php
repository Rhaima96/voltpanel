<?php

namespace Rhaima\VoltPanel\Actions;

class ExportAction extends BulkAction
{
    protected string $format = 'csv';
    protected ?string $fileName = null;

    public function __construct()
    {
        parent::__construct('export');

        $this->label('Export')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success');
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function fileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function csv(): static
    {
        return $this->format('csv');
    }

    public function xlsx(): static
    {
        return $this->format('xlsx');
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'format' => $this->format,
            'fileName' => $this->fileName,
        ]);
    }
}
