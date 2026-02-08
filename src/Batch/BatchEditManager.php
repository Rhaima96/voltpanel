<?php

namespace Rhaima\VoltPanel\Batch;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BatchEditManager
{
    public function updateRecords(string $modelClass, array $ids, array $data): int
    {
        $model = new $modelClass;

        return $model::whereIn('id', $ids)->update($data);
    }

    public function deleteRecords(string $modelClass, array $ids): int
    {
        $model = new $modelClass;

        return $model::whereIn('id', $ids)->delete();
    }

    public function restoreRecords(string $modelClass, array $ids): int
    {
        $model = new $modelClass;

        if (!method_exists($model, 'restore')) {
            return 0;
        }

        return $model::whereIn('id', $ids)->restore();
    }

    public function performAction(string $modelClass, array $ids, string $action, array $params = []): mixed
    {
        $model = new $modelClass;
        $records = $model::whereIn('id', $ids)->get();

        $results = [];

        foreach ($records as $record) {
            if (method_exists($record, $action)) {
                $results[] = $record->$action(...$params);
            }
        }

        return $results;
    }
}
