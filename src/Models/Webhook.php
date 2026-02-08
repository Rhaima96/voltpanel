<?php

namespace Rhaima\VoltPanel\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $guarded = [];

    protected $casts = [
        'headers' => 'array',
        'is_active' => 'boolean',
        'successful_calls' => 'integer',
        'failed_calls' => 'integer',
        'last_called_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('voltpanel.webhooks.table_name', 'webhooks'));
    }
}
