<?php

namespace Rhaima\VoltPanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('voltpanel.activity_log.table_name', 'activity_log'));
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    public function getChanges(): array
    {
        return $this->properties['attributes'] ?? [];
    }

    public function getOldValues(): array
    {
        return $this->properties['old'] ?? [];
    }
}
