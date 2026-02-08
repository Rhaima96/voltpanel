<?php

namespace Rhaima\VoltPanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('voltpanel.tags.table_name', 'tags'));
    }

    public function taggables(): MorphToMany
    {
        return $this->morphedByMany(Model::class, 'taggable', 'taggables');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
