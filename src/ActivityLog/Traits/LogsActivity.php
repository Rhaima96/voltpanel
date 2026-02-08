<?php

namespace Rhaima\VoltPanel\ActivityLog\Traits;

use Rhaima\VoltPanel\ActivityLog\Activity;

trait LogsActivity
{
    protected static $logAttributes = ['*'];
    protected static $logName = 'default';
    protected static $logOnlyDirty = false;
    protected static $logExcept = [];

    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            if (static::$logOnlyDirty && !$model->isDirty()) {
                return;
            }
            $model->logActivity('updated');
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->logActivity('restored');
            });
        }
    }

    protected function logActivity(string $event): void
    {
        $attributes = $this->getActivityAttributes();

        Activity::make($event)
            ->on($this)
            ->by(auth()->user())
            ->withProperties($attributes)
            ->description($this->getActivityDescription($event))
            ->log(static::$logName)
            ->save();
    }

    protected function getActivityAttributes(): array
    {
        if (in_array('*', static::$logAttributes)) {
            $attributes = $this->getAttributes();
        } else {
            $attributes = array_intersect_key(
                $this->getAttributes(),
                array_flip(static::$logAttributes)
            );
        }

        return array_diff_key($attributes, array_flip(static::$logExcept));
    }

    protected function getActivityDescription(string $event): string
    {
        $modelName = class_basename($this);
        return ucfirst($event) . ' ' . $modelName;
    }

    public function activities()
    {
        $activityModel = config('voltpanel.activity_log.model');

        return $this->morphMany($activityModel, 'subject');
    }
}
