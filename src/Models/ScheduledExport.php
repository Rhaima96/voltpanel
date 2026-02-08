<?php

namespace Rhaima\VoltPanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledExport extends Model
{
    protected $guarded = [];

    protected $casts = [
        'filters' => 'array',
        'recipients' => 'array',
        'is_active' => 'boolean',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('voltpanel.scheduling.table_name', 'scheduled_exports'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            config('auth.providers.users.model', \App\Models\User::class)
        );
    }
}
