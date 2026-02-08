<?php

namespace Rhaima\VoltPanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedFilter extends Model
{
    protected $guarded = [];

    protected $casts = [
        'filters' => 'array',
        'is_public' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('voltpanel.saved_filters.table_name', 'saved_filters'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            config('auth.providers.users.model', \App\Models\User::class)
        );
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('is_public', true);
        });
    }

    public function scopeForResource($query, string $resource)
    {
        return $query->where('resource', $resource);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public static function getForUser(int $userId, string $resource)
    {
        return static::forUser($userId)
            ->forResource($resource)
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();
    }

    public static function saveFilter(array $data): self
    {
        // If this is being set as default, unset other defaults for this user/resource
        if ($data['is_default'] ?? false) {
            static::where('user_id', $data['user_id'])
                ->where('resource', $data['resource'])
                ->update(['is_default' => false]);
        }

        return static::create($data);
    }

    public function makeDefault(): void
    {
        // Unset other defaults
        static::where('user_id', $this->user_id)
            ->where('resource', $this->resource)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }
}
