<?php

namespace Rhaima\VoltPanel\Widgets;

class ActivityLogWidget extends Widget
{
    protected ?int $limit = 10;
    protected ?string $logName = null;
    protected ?string $subjectType = null;

    public static function make(): static
    {
        return new static();
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function logName(string $logName): static
    {
        $this->logName = $logName;

        return $this;
    }

    public function subjectType(string $subjectType): static
    {
        $this->subjectType = $subjectType;

        return $this;
    }

    public function getData(): array
    {
        $activityModel = config('voltpanel.activity_log.model', \Rhaima\VoltPanel\Models\Activity::class);

        $query = $activityModel::query()
            ->with(['causer', 'subject'])
            ->latest();

        if ($this->logName) {
            $query->where('log_name', $this->logName);
        }

        if ($this->subjectType) {
            $query->where('subject_type', $this->subjectType);
        }

        $activities = $query->limit($this->limit)->get();

        return [
            'activities' => $activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'event' => $activity->event,
                    'causer' => $activity->causer?->name ?? 'System',
                    'subject_type' => class_basename($activity->subject_type),
                    'created_at' => $activity->created_at->diffForHumans(),
                    'properties' => $activity->properties,
                ];
            }),
            'limit' => $this->limit,
        ];
    }
}
