<?php

namespace Rhaima\VoltPanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTablePreference extends Model
{
    protected $guarded = [];

    protected $casts = [
        'visible_columns' => 'array',
        'column_order' => 'array',
        'hidden_columns' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('voltpanel.table_preferences.table_name', 'user_table_preferences'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            config('auth.providers.users.model', \App\Models\User::class)
        );
    }

    public static function getPreference(int $userId, string $table): ?self
    {
        return static::where('user_id', $userId)
            ->where('table_identifier', $table)
            ->first();
    }

    public static function savePreference(int $userId, string $table, array $data): self
    {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'table_identifier' => $table,
            ],
            $data
        );
    }
}
