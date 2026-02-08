<?php

namespace Rhaima\VoltPanel\Actions;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class BulkAction extends Action
{
    protected bool $deselectRecordsAfterCompletion = true;

    public function deselectRecordsAfterCompletion(bool $deselect = true): static
    {
        $this->deselectRecordsAfterCompletion = $deselect;

        return $this;
    }

    public function call(?Model $record = null): mixed
    {
        // BulkAction overrides call to accept Collection via callBulk
        // This maintains compatibility with parent Action class
        return null;
    }

    public function callBulk(?Collection $records = null): mixed
    {
        if ($this->action && $records) {
            return call_user_func($this->action, $records);
        }

        return null;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'type' => 'bulk',
            'deselectRecordsAfterCompletion' => $this->deselectRecordsAfterCompletion,
        ]);
    }
}
